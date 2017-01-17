<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\ModelHelpers\PhpcmsMigrationHelper;


class MigrationSpecial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:special';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $_edbPrefix = null;
    protected $_pdbPrefix = null;
    protected $_mainTable = null;
    protected $_sideTable = null;

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
        $this->_mainTable = $this->_edbPrefix.'enewszt';
        $this->_sideTable = $this->_edbPrefix.'enewsztadd';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->info('迁移专题信息');
        $system = DB::table($this->_edbPrefix.'enewspublic')->first();
        $sitePrefix = $system->newsurl ? $system->newsurl : '/';
        $migrationInfo = DB::table('phpcms_migration')
            ->where(['type'=>'special', 'phpcms_table'=> $this->_pdbPrefix.'special'])
            ->select(['phpcms_id','ecms_id'])
            ->get()
            ->keyBy('phpcms_id')
            ->toArray();

        $specials = DB::table($this->_pdbPrefix.'special')->get();

        $this->output->progressStart(count($specials));

        foreach($specials as $special){
            $this->output->progressAdvance();

            $mainData = [
                'ztname' => $special->title,
                'ztnum' => 25,
                'listtempid' => 14,
                'ztpath' => 'special/'.$special->filename,
                'zttype' => '.html',
                'zcid' => 1,
                'restb' => 1,
                'islist' => 1,
                'reorder' => 'newstime DESC',
                'intro' => $special->description,
                'ztimg' => $this->imgPathTransfer($sitePrefix, $special->thumb),
                'addtime' => $special->createtime,
            ];

            $sideData = [
                'classtext'=>''
            ];

            if(isset($migrationInfo[$special->id])){
                $id = $migrationInfo[$special->id]->ecms_id;
                DB::table($this->_mainTable)->where('ztid', $id)->update($mainData);
                DB::table($this->_sideTable)->where('ztid', $id)->update($sideData);

                DB::table($this->_edbPrefix.'enewsztinfo')->where('ztid', $id)->delete();
            }else{
                $id = DB::table($this->_mainTable)->insertGetId($mainData);
                $sideData['ztid'] = $id;
                DB::table($this->_sideTable)->insert($sideData);

                //插入迁移表信息
                $migration = [
                    'type'=>'special',
                    'name'=> $special->title,
                    'phpcms_table' => $this->_pdbPrefix.'special',
                    'phpcms_id' => $special->id,
                    'ecms_table' => $this->_mainTable,
                    'ecms_id' => $id,
                ];
                PhpcmsMigrationHelper::create($migration);
            }

            $specialNews = DB::table($this->_pdbPrefix.'special_content')->where('specialid', $special->id)->get();
            foreach($specialNews as $specialNew){
                $article = DB::table($this->_edbPrefix.'ecms_article')->where('title', $specialNew->title)->first();
                if($article){
                    $specialData = [
                        'ztid' => $id,
                        'classid' => $article->classid,
                        'id' => $article->id,
                        'newstime' => $article->newstime,
                        'cid' => 0,
                        'mid' => 9,
                    ];
                    DB::table($this->_edbPrefix.'enewsztinfo')->insert($specialData);
                }
            }
        }

        $this->output->progressFinish();
    }

    private function keywordsTransfer($keywords){
        return str_replace(' ', ',', $keywords);
    }

    private function imgPathTransfer($sitePrefix, $str){
        return str_replace('http://www.cwzg.cn/uploadfile', 'http://static.cwzg.webbig.cn/p', $str);
    }
}
