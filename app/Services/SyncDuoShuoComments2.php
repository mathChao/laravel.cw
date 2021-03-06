<?php

namespace App\Services;

use App\Models\CommentDuoShuo;
use App\ModelHelpers\CommentHelper2;
use App\Models\CommentSyncLog2;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use Log;
use Illuminate\Database\QueryException;

class SyncDuoShuoComments2
{

    private $app_id;
    private $app_key;
    private $sync_api_url;

    public function __construct(){
        $this->app_id  = Config::get('duoshuo.app_id');
        $this->app_key = Config::get('duoshuo.app_key');
        $this->sync_api_url = Config::get('duoshuo.sync_api_url');
    }

    public function syncComment($limit){

        $last_log_id = $this->getLastLogId();

        $params = array(
            'limit' => $limit,
            'order' => 'asc',
            'since_id' => $last_log_id,
            'short_name' => $this->app_id,
            'secret' => $this->app_key
        );

        $http_client = new Client();

        $response = $http_client->request('GET', $this->sync_api_url, [ 'query' => $params ]);

        if ($response->getStatusCode() !== 200) {
            //处理错误,错误消息$response['message'], $response['code']
            Log::info($response['message'].':'.$response['code']);
            return ['result' => 'fail','error'=> $response['code']];
        } else {
            //遍历返回的response，你可以根据action决定对这条评论的处理方式。
            $result = $response->getBody()->getContents();
            $result = \GuzzleHttp\json_decode($result,true);
            $number = count($result['response']);
            file_put_contents('/tmp/comments.txt',json_encode($result)."\n",FILE_APPEND);
            foreach ($result['response'] as $log) {
                //dd($log);
                switch ($log['action']) {
                    case 'create':
                        $this->createComment($log['meta'],$log['date']);  // 0
                        break;
                    case 'approve':
                        $this->updateComments($log['meta'],$log['action'],$log['date']); // 1
                        //这条评论是通过的评论
                        break;
                    case 'spam':
                        $this->updateComments($log['meta'],$log['action'],$log['date']);    // -1
                        //这条评论是标记垃圾的评论
                        break;
                    case 'delete':
                        $this->updateComments($log['meta'],$log['action'],$log['date']);  // -2
                        //这条评论是删除的评论
                        break;
                    case 'delete-forever':
                        $this->deleteComments($log['meta']);  // delete
                        //彻底删除的评论
                        break;
                    default:
                        break;
                }

                //更新last_log_id，记得维护last_log_id。（如update你的数据库）
                if (strlen($log['log_id']) > strlen($last_log_id) || strcmp($log['log_id'], $last_log_id) > 0) {
                    $last_log_id = $log['log_id'];
                }

            }
            // 批量更新评论数量
            //CommentHelper::updateCommentCount();
            // after batch dealt, then insert a log;
            CommentSyncLog2::create(['log_id' => $last_log_id, 'updatetime' => time()]);

        }

        return ['result' => 'success','number' => $number];
    }

    protected function getLastLogId(){
        $last_log = CommentSyncLog2::orderBy('id','desc')->first();
        if($last_log){
            return $last_log->log_id;
        }
        return 0;
    }

    protected function createComment($post,$time){

        $article = DB::table(config('cwzg.edbPrefix').'ecms_article_index')->where('id',$post['thread_key'])->first();
        if($article){
            $check = CommentDuoShuo::where('post_id',$post['post_id'])->get();
            if(count($check) !== 0 ){ // 评论已经存在了
                return;
            }
            try{
                $comment = new CommentDuoShuo();
                $comment->post_id    = $post['post_id'];
                $comment->comment    = json_encode($post);
                $comment->status     = 'create';
                $comment->article_id = $article->id;
                $comment->created_at = date('Y-m-d H:i:s',$time);
                $comment->updated_at = date('Y-m-d H:i:s',$time);
                $comment->save();
            } catch (QueryException $e) {
                Log::info('data error,can not save'.$e->getMessage());
            }
        }
    }

    protected function updateComments($comments,$status,$time){
        if(!empty($comments)){
            $time = $time ? date('Y-m-d H:i:s',$time) : Carbon::now();
            CommentHelper2::updateCommentStatus($comments,$status,$time);
        }
    }

    protected function deleteComments($comments){
        if(!empty($comments)){
            CommentHelper2::deleteComments($comments);
        }
    }

    public function __get($key){
        return $this->$key;
    }
}
