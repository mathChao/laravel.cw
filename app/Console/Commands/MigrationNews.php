<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\ModelHelpers\PhpcmsMigrationHelper;


class MigrationNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:news {--classname=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $_dbPrefix = null;
    protected $_mainTable = null;
    protected $_sideTable = null;
    protected $_indexTable = null;
    protected $_befromTable = null;
    protected $_writerTable = null;
    protected $_publicTable = null;
    protected $_eclass = null;
    protected $_pclass = null;
    protected $_befrom_class = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_edbPrefix = config('cwzg.edbPrefix');
        $this->_publicTable = $this->_edbPrefix.'enewspublic';
        $this->_befromTable = $this->_edbPrefix.'ecms_copyfrom';
        $this->_writerTable  = $this->_edbPrefix.'ecms_author';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $classname = $this->option('classname');

        $this->_eclass = DB::table($this->_edbPrefix.'enewsclass')->where('classname', $classname)->first();
        $this->_pclass = DB::table('cw_category')->where('catname', $classname)->first();
        $this->_befrom_class = DB::table($this->_edbPrefix.'enewsclass')->where('classid', 11)->first();

        if (!empty($this->_eclass) && !empty($this->_pclass)) {
            $this->_mainTable = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname;
            $this->_sideTable  = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname.'_data_1';
            $this->_indexTable  = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname.'_index';
        }else{
            $this->error('class ' . $classname . ' does not exist!');
            return 0;
        }

        if ($this->_eclass->islast && in_array($this->_eclass->modid, [1,9])) {
            $this->info('迁移'.$classname.'的相关文章信息');
            $this->output->progressStart(DB::table('cw_news')->where('catid', $this->_pclass->catid)->count());

            $system = DB::table($this->_publicTable)->first();
            $migrationInfo = DB::table('phpcms_migration')
                ->select(['phpcms_id', 'ecms_id'])
                ->where(['phpcms_table'=>'cw_news', 'type'=>'news', 'name'=>$this->_eclass->classname,])
                ->get()
                ->keyBy('phpcms_id')
                ->toArray();

            $moodTable = $this->_edbPrefix.'ecmsextend_mood';
            $moods = DB::table($moodTable)->select('id')->get()->keyBy('id')->keys()->toArray();

            $data = [
                'system' => $system,
                'sitePrefix' => $system->newsurl ? $system->newsurl : '/',
                'migrationInfo' => $migrationInfo,
                'moods' => $moods,
            ];

            DB::table('cw_news')->where('catid', $this->_pclass->catid)->chunk(1000, function ($newses) use ($data) {
                extract($data);
                foreach ($newses as $news) {
                    $this->output->progressAdvance();

                    $newsData = DB::table('cw_news_data')->where('id', $news->id)->first();

                    if(!$newsData){
                        $this->info('news '.$news->id.' does not has news data in cw_news_data');
                        continue;
                    }

                    $newsPosition = DB::table('cw_position_data')->select('posid')->where('id', $news->id)->get()->keyBy('posid')->keys()->toArray();
                    $newsMood = DB::table('cw_mood')->where('contentid', $news->id)->get();

                    $this->_mainTable = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname;
                    $this->_sideTable  = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname.'_data_1';

                    $checked = 1;
                    if( $news->status != 99){
                        $this->_mainTable = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname.'_check';
                        $this->_sideTable = $this->_edbPrefix.'ecms_'.$this->_eclass->tbname.'_check_data';
                        $checked = 0;
                    }

                    //插入index表信息
                    $indexData = [
                        'classid' => $this->_eclass->classid,
                        'checked' => $checked,
                        'newstime' => $news->inputtime,
                        'truetime' => $news->inputtime,
                        'lastdotime' => $news->updatetime,
                        'havehtml' => 0,
                    ];

                    if(isset($migrationInfo[$news->id])){
                        $id = $migrationInfo[$news->id]->ecms_id;
                        DB::table($this->_indexTable)->where('id', $id)->update($indexData);
                    }else{
                        $indexData['id'] = $news->id;
                        DB::table($this->_indexTable)->insertGetId($indexData);
                        $id = $news->id;
                    }

                    $firsttitle = 0;
                    $isgood = 0;
                    $ttid = 0;
                    //头条
                    if(in_array(2, $newsPosition)){
                        $firsttitle = 2;
                    }

                    //推荐
                    if(in_array(36, $newsPosition)){
                        $isgood += 3;
                    }

                    //独家图片和推荐图片
                    if(in_array(39, $newsPosition) || in_array(40,$newsPosition)){
                        $isgood += 1;
                    }

                    if(in_array(37, $newsPosition)){
                        //深度
                        $isgood = 5;
                    }

                    if(in_array(42, $newsPosition)){
                        //智库
                        $ttid = 4;
                    }elseif(in_array(38, $newsPosition)){
                        //争鸣
                        $ttid = 5;
                    }elseif(in_array(35, $newsPosition) || in_array(43, $newsPosition)){
                        //独家 时评
                        $ttid = 3;
                    }

                    $copyfrom = explode('|', $newsData->copyfrom);
                    $copyfrom = array_map('trim', $copyfrom);
                    //向news主表插入信息
                    $mainData = [
                        'classid' => $this->_eclass->classid,
                        'title' => $news->title,
                        'keyboard' => $this->keywordsTransfer($news->keywords),
                        'ftitle' => $news->subtitle,
                        'smalltext' => $news->description,
                        'titlepic' => $this->imgPathTransfer($sitePrefix, $news->thumb),
                        'username' => $news->username,
                        'author' => $newsData->author,
                        'editor' => $newsData->responsibleeditor,
                        'newspath' => date($this->_eclass->newspath, $news->inputtime),
                        'filename'=>$id,
                        'newstime' => $news->inputtime,
                        'truetime' => $news->inputtime,
                        'lastdotime' => $news->updatetime,
                        'titleurl' => $news->islink ? $news->url : $sitePrefix.$this->_eclass->classpath.'/'.date($this->_eclass->newspath, $news->inputtime).'/'.$id.$this->_eclass->filetype,
                        'userfen' => $newsData->readpoint,
                        'titlefont' => str_replace([';', 'bold'], [',', 'b'], $news->style),
                        'ispic' => $news->thumb ? 1 : 0,
                        'isurl' => $news->islink,
                        'copyfrom' => $copyfrom[0],
                        'fromurl' => $news->copyfromlink,
                        'ismember' => $news->sysadd == 1 ? 0 : 1,
                        'firsttitle' => $firsttitle,
                        'isgood' => $isgood,
                        'ttid' => $ttid,
                        'stb' => 1,
                    ];

                    //插入副表信息
                    $sideData = [
                        'classid' => $this->_eclass->classid,
                        'closepl' => $newsData->allow_comment == 1 ? 0 : 1,
                        'newstext' => $this->imgPathTransfer($sitePrefix, $newsData->content),
                    ];

                    //插入mood表信息
                    $moodTable = $this->_edbPrefix.'ecmsextend_mood';
                    $moodData = [
                        'mood1' => $newsMood->sum('n1'),
                        'mood2' => $newsMood->sum('n2'),
                        'mood3' => $newsMood->sum('n3'),
                        'mood4' => $newsMood->sum('n6'),
                        'mood5' => $newsMood->sum('n7'),
                        'mood6' => $newsMood->sum('n8'),
                    ];

                    if(array_sum($moodData)>0){
                        $moodData['id'] = $id;
                        $moodData['classid'] = $this->_eclass->classid;

                        if(in_array($id, $moods)){
                            DB::table($moodTable)->where(['classid'=>$this->_eclass->classid, 'id'=>$id])->update($moodData);
                        }else{
                            DB::table($moodTable)->insert($moodData);
                            $moods[] = $id;
                        }
                    }

                    $count = DB::table($this->_mainTable)->where('id', $id)->count();

                    if($count > 0){
                        DB::table($this->_mainTable)->where('id', $id)->update($mainData);
                        DB::table($this->_sideTable)->where('id', $id)->update($sideData);

                    }else{
                        $mainData['id'] = $id;
                        DB::table($this->_mainTable)->insert($mainData);

                        $sideData['id'] = $id;
                        DB::table($this->_sideTable)->insert($sideData);
                    }

                    if(!isset($migrationInfo[$news->id])){
                        //插入迁移表信息
                        $migration = [
                            'type'=>'news',
                            'name'=>$this->_eclass->classname,
                            'phpcms_table' => 'cw_news',
                            'phpcms_id' => $news->id,
                            'ecms_table' => $this->_mainTable,
                            'ecms_id' => $id,
                        ];
                        PhpcmsMigrationHelper::create($migration);
                    }

                }
            });


            $ckinfos = DB::table($this->_indexTable)->where(['classid'=>$this->_eclass->classid, 'checked'=>0])->count();
            $infos = DB::table($this->_indexTable)->where(['classid'=>$this->_eclass->classid, 'checked'=>1])->count();
            DB::table($this->_edbPrefix.'enewsclass')->where('classid', $this->_eclass->classid)->update(['allinfos'=>$ckinfos + $infos, 'infos'=>$infos]);

            $befromNum = DB::table($this->_befromTable)->count();
            DB::table($this->_edbPrefix.'enewsclass')->where('classid', $this->_befrom_class->classid)->update(['allinfos'=>$befromNum, 'infos'=>$befromNum]);

            $this->output->progressFinish();
        }
    }


    private function keywordsTransfer($keywords){
        return str_replace(' ', ',', $keywords);
    }

    private function imgPathTransfer($sitePrefix, $str){
        return str_replace('http://www.cwzg.cn/uploadfile', 'http://static.cwzg.cn/p', $str);
    }
}
