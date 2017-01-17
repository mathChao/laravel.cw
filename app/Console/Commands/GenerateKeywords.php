<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Cache;


class GenerateKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:keywords';

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
        $this->info('生成文章关键字索引关系');

        $this->output->progressStart(DB::table($this->_edbPrefix.'ecms_article')->where('keyboard', '!=', '')->count());
        $keywords = [];
        DB::table($this->_edbPrefix.'ecms_article')
            ->select(['id','keyboard'])
            ->where('keyboard', '!=', '')
            ->chunk(1000, function($articles)use( &$keywords ){
            foreach($articles as $article){
                $this->output->progressAdvance();
                $articleKeywords = array_explode([',', '，', ' ', '　'], $article->keyboard);
                foreach($articleKeywords as $keyword){
                    if($keyword){
                        $keyword = strtoupper($keyword);
                        if(isset($keywords[$keyword]) && !in_array($article->id, $keywords[$keyword])){
                            $keywords[$keyword][] = $article->id;
                        }else{
                            $keywords[$keyword] = [$article->id];
                        }
                    }
                }
            }
        });
        $this->output->progressFinish();

        $this->info('储存文章关键字索引关系');
        $this->output->progressStart(count($keywords));
        foreach($keywords as $key => $ids){
            $this->output->progressAdvance();

            Cache::forever(getKeywordsCacheId($key), $ids);
        }
        $this->output->progressFinish();
    }
}
