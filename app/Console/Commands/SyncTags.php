<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ModelHelpers\PhpcmsMigrationHelper;

use DB;
use Cache;

class SyncTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:tags {--start=today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $_edbPrefix = null;
    protected $_pdbPrefix = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_edbPrefix = config('cwzg.edbPrefix');
        $this->_pdbPrefix = config('cwzg.pdbPrefix');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->info('同步文章关键字tags关系');
        $startTime = strtotime($this->option('start'));
        $this->output->progressStart(DB::table($this->_edbPrefix.'ecms_article')->where('keyboard', '!=', '')->where('lastdotime', '>', $startTime)->count());
        $keywords = [];
        DB::table($this->_edbPrefix.'ecms_article')
            ->select(['id','keyboard','classid', 'newstime'])
            ->where('keyboard', '!=', '')
            ->where('lastdotime', '>', $startTime)
            ->chunk(1000, function($articles)use( &$keywords ){
            foreach($articles as $article){
                $this->output->progressAdvance();
                $articleKeywords = array_explode([',', '，', ' ', '　'], $article->keyboard);
                foreach($articleKeywords as $keyword){
                    if($keyword){
                        $keyword = strtoupper($keyword);
                        if(isset($keywords[$keyword])){
                            if(!isset($keywords[$keyword][$article->id])){
                                $keywords[$keyword][$article->id] = $article;
                            }
                        }else{
                            $keywords[$keyword] = [$article->id => $article];
                        }
                    }
                }
            }
        });
        $this->output->progressFinish();

        $tagsTable = $this->_edbPrefix.'enewstags';
        $tagsDataTable = $this->_edbPrefix.'enewstagsdata';

        $this->info('储存文章关键字tags关系');
        $this->output->progressStart(count($keywords));

        $tags = DB::table($tagsTable)->get()->keyBy('tagname')->toArray();
        foreach($keywords as $key => $articles){
            $this->output->progressAdvance();
            if(!isset($tags[$key])){
                $tagid = DB::table($tagsTable)->insertGetId(['tagname'=>$key, 'num'=>count($articles)]);
                $num = 0;
            }else{
                $tagid = $tags[$key]->tagid;
                $num = $tags[$key]->num;
            }

            $tagArticleIds = DB::table($tagsDataTable)
                ->where('tagid', $tagid)
                ->select('id')
                ->get()
                ->keyBy('id')
                ->toArray();

            foreach($articles as $article){
                if(!isset($tagArticleIds[$article->id])){
                    $data = [
                        'tagid' => $tagid,
                        'classid' => $article->classid,
                        'id' => $article->id,
                        'newstime' => $article->newstime,
                        'mid' => 9
                    ];
                    DB::table($tagsDataTable)->insert($data);
                    $num++;
                }
            }
            DB::table($tagsTable)->where('tagid', $tagid)->update(['num'=>$num]);
        }
        $this->output->progressFinish();
    }
}
