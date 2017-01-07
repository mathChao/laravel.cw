<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\ModelHelpers\CommentHelper;
use App\Models\Comment;
use App\Tools\Ajax;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Log;
use App\ModelHelpers\PhpcmsMigrationHelper;
use App\Models\CommentSyncLog;

class CommentController extends Controller
{

	function syncCallback(Request $request)
	{
		$key        = '2093c10c3fe2847c8d4e178b8f748a51';
		$short_name = 'chawangzg';
		$limit      = 20;

		if ($this->check_signature($request->input(), $key) == false) {
			return false;
		}

		$last_log_id = $this->getLastLogId();

		$params = array(
			'limit' => $limit,
			'order' => 'asc',
			'since_id' => $last_log_id,
			'short_name' => $short_name,
			'secret' => $key
		);

		//自己找一个php的 http 库
		$http_client = new Client();

		$response = $http_client->request('GET', 'http://api.duoshuo.com/log/list.json', [ 'query' => $params ]);

		if ($response->getStatusCode() !== 200) {
			//处理错误,错误消息$response['message'], $response['code']
			Log::info($response['message'].':'.$response['code']);
		} else {
			//遍历返回的response，你可以根据action决定对这条评论的处理方式。
			$result = $response->getBody()->getContents();
			$result = \GuzzleHttp\json_decode($result,true);

			foreach ($result['response'] as $log) {
				//dd($log);
				switch ($log['action']) {
					case 'create':
						$this->createComment($log['meta']);  // 0
						break;
					case 'approve':
						$this->checkComments($log['meta'],1); // 1
						//这条评论是通过的评论
						break;
					case 'spam':
						$this->checkComments($log['meta'],-1);    // -1
						//这条评论是标记垃圾的评论
						break;
					case 'delete':
						$this->checkComments($log['meta'],-2);  // -2
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

			// after batch dealt, then insert a log;
			CommentSyncLog::create(['log_id' => $last_log_id, 'updatetime' => time()]);

		}

		return Ajax::success(['message' => 'success']);
	}

	protected function getLastLogId(){
		$last_log = CommentSyncLog::orderBy('id','desc')->first();
		if($last_log){
			return $last_log->log_id;
		}
		return 0;
	}

	protected function createComment($post){
		//dd($comment);
		$data['post_id'] = $post['post_id'];
		$article_id = PhpcmsMigrationHelper::getNewIdFromOldId('news',$post['thread_key']);
		//$article_id = $article_id == null ? $comment['thread_key'] : $article_id; // 如果没有，则使用最新的id，上线之后直接使用 这个 id
		$article = DB::table(config('cwzg.edbPrefix').'ecms_article_index')->where('id',$article_id)->first();
		// find article
		if($article){
			$check = Comment::where('post_id',$post['post_id'])->get();
			if(count($check) !== 0 ){ // 评论已经存在了
				return;
			}
			$comment = new Comment();
			$comment->id       = $article->id;
			$comment->classid  = $article->classid;
			$comment->username = substr($post['author_name'],0,64);
			$comment->saytext  = $post['message'];
			$comment->checked  = 0; // 待审核
			$comment->saytime  = strtotime($post['created_at']);
			$comment->sayip    = $post['ip'];
			$comment->post_id  = $post['post_id'];
			$comment->save();
		}
	}

	protected function checkComments($comments,$status){
		if(!empty($comments)){
			CommentHelper::updateCommentStatus($comments,$status);
		}
	}

	protected function deleteComments($comments){
		if(!empty($comments)){
			CommentHelper::deleteComments($comments);
		}
	}

	/**
	 *
	 * check signature
	 *
	 */
	protected function check_signature($input, $secret)
	{
		$signature = $input['signature'];
		unset($input['signature']);
		ksort($input);
		$baseString = http_build_query($input, null, '&');
		$expectSignature = base64_encode(hash_hmac('sha1', $baseString, $secret, true));

		if ($signature !== $expectSignature) {
			return false;
		}

		return true;
	}
}
