<?php

namespace App\Console\Commands;

use App\ModelHelpers\ArticleHelper;
use App\Models\CommentDuoShuo;
use Illuminate\Console\Command;
use App\Services\SyncDuoShuoComments2;
use DB;

class genChangYanComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // inner join get all articles where has comments

        $articleTable = 'cwcms_ecms_article';
        $commentTable = 'pl_duoshuo';

        $aids = DB::table($articleTable)->join($commentTable, $articleTable.'.id','=',$commentTable.'.article_id')
            ->whereIn($commentTable.'.status',['approve'])->select($articleTable.'.id')->distinct()->get();

        if($aids){
            foreach($aids as $row){
                $article  = ArticleHelper::getArticleInfo($row->id);
                $comments = CommentDuoShuo::where('article_id',$row->id)->get();
                $json = $this->format($article,$comments)."\n";
                file_put_contents(public_path().'/comments.json',$json,FILE_APPEND);
            }
        }
    }

    protected function format($article,$comments){
//        {
//    _"title":"123",        			//文章标题
//    _"url":"http://localhost/?p=9",   //文章url
//    _"ttime":1401327899094,           //文章创建时间(必填)，这是长时间戳，某些系统的时间可能需要再补三个0
//    _"sourceid":"9",       	        //文章的Id（字符串类型，最长64字符）（全站唯一）
//    "parentid":"0",                   //文章所属专辑的ID，与sourceid相对应(字符串类型，可空，同时属于多个频道时以,号分隔)
//    _"categoryid":"",    	      	    //文章所属频道ID（字符串类型，可留空）
//    "ownerid":"",        		      	//文章发布者ID（可留空）
//    "metadata":"",       	      	    //文章其他信息（可留空）
//    "comments":[
//        {
//            _"cmtid":"358",                   //评论唯一ID
//            _"ctime":1401327899094, //评论时间,这是长时间戳，某些系统的时间可能需要再补三个0
//            _"content":"人生难得痴迷",//评论内容 (如果内容中有html标签请做下转化，比如<br>转为\r\n)
//            _"replyid":"0",                    //回复的评论ID，没有为0
//            "user":{
//                _"userid":"1",                 //发布者ID，全站唯一，必须存在
//                _"nickname":"admin",    //发布者昵称
//                "usericon":"",               //发布者头像（留空使用默认头像）
//                _"userurl":"",                  //发布者主页地址（可留空）
//                "usermetadata":{        //其它用户相关信息，例如性别，头衔等数据
//                  "area":  "",
//            		"gender":"",
//            		"kk": "",
//            		"level":
//        		}
//            },
//            _"ip":"127.0.0.1",              //发布ip
//            _"useragent":"Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20100101 Firefox/22.0",   //浏览器信息
//            "channeltype":"1",          //1为评论框直接发表的评论，2为第三方回流的评论
//            "from":"",                        //评论来源
//            "spcount":"",                  //评论被顶次数
//            "opcount":"",                  //评论被踩次数
//            "attachment":[]                //附件列表
//
//        },
//    ]
//  }

        $json = [
            'title' => $article->title,
            'url' => $article->titleurl,
            'ttime' => $article->newstime.'000',
            'sourceid' => $article->id,
            'parentid' => 0,
            'categoryid' => $article->classid,
            'ownerid'  => '',
            'metadata' => '',
        ];

        foreach($comments as $row){
            $comment = json_decode($row->comment);
            $json['comments'][] = [
                'cmtid' => $comment->post_id  ,
                'ctime' => strtotime($comment->created_at).'000',
                'content' => str_replace('<br />',"\r\n",$comment->message),
                'replyid' => empty($comment->parent_id) ? "0" : $comment->parent_id,
                'user' => [
                    'userid' => $comment->author_id,
                    'nickname' => $comment->author_name,
                    'usericon' => '',
                    'userurl' => $comment->author_url,
                ],
                'ip'          => $comment->ip,
                'useragent'   => $comment->agent,
                'channeltype' => '1',
                'from'       => '',
                'spcount'    => '',
                'opcount'    => '',
            ];
        }

        return json_encode($json);
    }

}
