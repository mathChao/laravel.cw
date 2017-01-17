<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ModelHelpers\PhpcmsMigrationHelper;

use DB;
use Cache;


class GenerateTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:tags';

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
        $this->info('生成文章关键字tags关系');

        $this->output->progressStart(DB::table($this->_edbPrefix.'ecms_article')->where('keyboard', '!=', '')->count());
        $keywords = [];
        DB::table($this->_edbPrefix.'ecms_article')
            ->select(['id','keyboard','classid', 'newstime'])
            ->where('keyboard', '!=', '')
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
        $migrations = DB::table('phpcms_migration')
            ->where(['type'=>'tags'])
            ->select(['name','ecms_id'])
            ->get()
            ->keyBy('name')
            ->toArray();
        foreach($keywords as $key => $articles){
            $this->output->progressAdvance();

            if(!isset($migrations[$key])){
                $tagid = DB::table($tagsTable)->insertGetId(['tagname'=>$key, 'num'=>count($articles)]);
                foreach($articles as $article){
                    $data = [
                        'tagid' => $tagid,
                        'classid' => $article->classid,
                        'id' => $article->id,
                        'newstime' => $article->newstime,
                        'mid' => 9
                    ];
                    DB::table($tagsDataTable)->insert($data);
                }

                $migration = [
                    'type' => 'tags',
                    'name' => $key,
                    'phpcms_table' => '',
                    'phpcms_id' => 0,
                    'ecms_table' => $tagsTable,
                    'ecms_id' => $tagid
                ];
                PhpcmsMigrationHelper::create($migration);
            }
        }
        $this->output->progressFinish();
    }
}
