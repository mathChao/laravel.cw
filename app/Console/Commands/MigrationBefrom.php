<?php

namespace App\Console\Commands;

use App\ModelHelpers\PhpcmsMigrationHelper;
use Illuminate\Console\Command;

use DB;
use Cache;


class MigrationBefrom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:befrom';

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
        $this->info('迁移copyfrom信息');
        $indexTable = $this->_edbPrefix.'ecms_copyfrom_index';
        $mainTable = $this->_edbPrefix.'ecms_copyfrom';
        $sideTable = $this->_edbPrefix.'ecms_copyfrom_data_1';

        $count = DB::select('select count(distinct `copyfrom`) num from `cw_news_data` where `copyfrom` != ""')[0]->num;

        $this->output->progressStart($count);
        $migrations = DB::table('phpcms_migration')
            ->select(['phpcms_id', 'ecms_id'])
            ->where(['type'=>'befrom'])
            ->get()
            ->keyBy('phpcms_id')
            ->toArray();

        $copyfromClass = DB::table('cwcms_enewsclass')->where('classid', '11')->first();
        $titles = DB::table($mainTable)->select('title')->get()->keyBy('title')->keys()->toArray();

        $befroms = DB::table('cw_news_data')
            ->select(['copyfrom'])
            ->where('copyfrom','!=', '')
            ->distinct()
            ->get();

        foreach($befroms as $befrom){
            $this->output->progressAdvance();
            $copyfrom = explode('|', $befrom->copyfrom);
            $copyfrom = array_map('trim', $copyfrom);
            if(in_array($copyfrom[0], $titles) || !$copyfrom[0]){
                continue;
            }else{
                $titles[] = $copyfrom[0];
            }

            $oldid = DB::table('cw_news_data')->where('copyfrom', $befrom->copyfrom)->first()->id;
            $row = DB::table('cw_news')->select('copyfromlink','inputtime','updatetime')->where('id', $oldid)->first();

            $copyfromIndexData = [
                'classid' => 11,
                'checked' => 1,
                'newstime' => $row->inputtime,
                'truetime' => $row->inputtime,
                'lastdotime' => $row->updatetime,
                'havehtml' => 0,
            ];

            if(isset($migrations[$oldid])){
                $copyfromid = $migrations[$oldid]->ecms_id;
                DB::table($indexTable)->where('id', $copyfromid)->update($copyfromIndexData);
            }else{
                $copyfromid = DB::table($indexTable)->insertGetId($copyfromIndexData);
            }

            $copyfromUrl = '';
            if(strpos($row->copyfromlink, 'mp.weixin.qq.com') === false){
                if( ($len = strpos($row->copyfromlink, '.com')) !== false){
                    $copyfromUrl = substr($row->copyfromlink, 0, $len + 4);
                }elseif( ($len = strpos($row->copyfromlink, '.cn')) !== false){
                    $copyfromUrl = substr($row->copyfromlink, 0, $len + 3);
                }
            }

            $copyfromMainData = [
                'classid' => $copyfromClass->classid,
                'newspath' => date($copyfromClass->newspath, $row->inputtime),
                'newstime' => $row->inputtime,
                'truetime' => $row->inputtime,
                'lastdotime' => $row->updatetime,
                'ismember' => 0,
                'smalltext' => 0,
                'titleurl' => $copyfromUrl,
                'filename' => $copyfromid,
                'title' => $copyfrom[0],
                'ispic' => 0,
                'isurl' => 1,
                'stb' => 1,
            ];

            $copyfromSideData = [
                'classid' => $copyfromClass->classid,
            ];

            if(isset($migrations[$oldid])){
                DB::table($mainTable)->where('id', $copyfromid)->update($copyfromMainData);
                DB::table($sideTable)->where('id', $copyfromid)->update($copyfromSideData);
            }else{
                $copyfromMainData['id'] = $copyfromid;
                $copyfromSideData['id'] = $copyfromid;
                DB::table($mainTable)->insert($copyfromMainData);
                DB::table($sideTable)->insert($copyfromSideData);
                $migration = [
                    'type' => 'befrom',
                    'name' => $copyfrom[0],
                    'phpcms_table' => 'cw_news',
                    'phpcms_id' => $oldid,
                    'ecms_table' => $indexTable,
                    'ecms_id' => $copyfromid
                ];
                PhpcmsMigrationHelper::create($migration);
            }
        }

        $this->output->progressFinish();
    }
}
