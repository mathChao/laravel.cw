<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Cache;


class GenerateOnclick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:onclick';

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
        $this->info('生成文章 onclick 信息');

        $articleTable = $this->_edbPrefix.'ecms_article';
        $moodTable = $this->_edbPrefix.'ecmsextend_mood';
        $config = [];

        $this->output->progressStart(DB::table($articleTable)->count());

        DB::table($articleTable)->chunk(1000, function($articles)use($articleTable, $moodTable, $config){
            foreach($articles as $article){
                $this->output->progressAdvance();
                $mood = DB::table($moodTable)
                    ->select([
                        'mood1',
                        'mood2',
                        'mood3',
                        'mood4',
                        'mood5',
                        'mood6',
                        'mood7',
                        'mood8',
                        'mood9',
                        'mood10',
                        'mood11',
                        'mood12'
                    ])
                    ->where('id', $article->id)
                    ->first();
                $moodSum = 0;
                if($mood){
                    $moodSum = array_sum((array) $mood);
                }

                $onclick = rand(100, 150) + $article->plnum * 20 + $moodSum *10;
                DB::table($articleTable)->where('id', $article->id)->update(['onclick'=>$onclick]);
            }
        });

        $this->output->progressFinish();
    }
}
