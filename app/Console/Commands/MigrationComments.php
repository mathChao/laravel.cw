<?php

namespace App\Console\Commands;

use App\ModelHelpers\ArticleHelper;
use App\ModelHelpers\PhpcmsMigrationHelper;
use App\Models\Article;
use Illuminate\Console\Command;

use App\Models\Comment;
use DB;


class MigrationComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $_edbPrefix = null;
    protected $_mainTable = null;

    public function __construct()
    {
        parent::__construct();
        $this->_edbPrefix = config('cwzg.edbPrefix');
        $this->_mainTable = $this->_edbPrefix.'enewspl_1';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->info('Transfer Comments from Duo Shuo');
        $file = file_get_contents('/tmp/export.json');
        $arr  = json_decode($file,true);

//        foreach($arr['threads'] as $row){
//            $article_id = PhpcmsMigrationHelper::getNewIdFromOldId('news',$row['thread_key']);
//            if($article_id !== null){
//                $article = Article::find($article_id);
//                if($article){
//                    $article->thread_id = $row['thread_id'];
//                    $article->save();
//                }
//            }
//        }

        foreach($arr['posts'] as $row){
            $article_id = PhpcmsMigrationHelper::getNewIdFromOldId('news',$row['thread_key']);
            if($article_id !== null){
                $article = DB::table(config('cwzg.edbPrefix').'ecms_article_index')->where('id',$article_id)->first();
                if($article){
                    $this->info('find article');
                    $check = Comment::where('post_id',$row['post_id'])->get();
                    if(count($check) !== 0 ){
                        continue;
                    }

                    $comment = new Comment();
                    $comment->id       = $article->id;
                    $comment->classid  = $article->classid;
                    $comment->username = substr($row['author_name'],0,64);
                    $comment->saytext  = $row['message'];
                    $comment->checked  = 1;
                    $comment->saytime  = strtotime($row['created_at']);
                    $comment->sayip    = $row['ip'];
                    $comment->post_id  = $row['post_id'];
                    $comment->save();
                }
                $this->info('cant find article'.$article_id);
            }
            $this->info('can find new id'.$row['thread_key']);
        }

        $this->info('Transfer Comments finished');
    }

}
