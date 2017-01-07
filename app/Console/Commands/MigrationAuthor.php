<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\ModelHelpers\PhpcmsMigrationHelper;


class MigrationAuthor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:author';

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
    protected $_indexTable = null;

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
        $this->_indexTable = $this->_edbPrefix.'ecms_author_index';
        $this->_mainTable = $this->_edbPrefix.'ecms_author';
        $this->_sideTable = $this->_edbPrefix.'ecms_author_data_1';

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->info('迁移作者信息');

        $eAuthorClass = DB::table($this->_edbPrefix.'enewsclass')->where('classid', 16)->first();
        $authors = DB::table($this->_pdbPrefix.'category')->where('parentid', 36)->get();
        $system = DB::table($this->_edbPrefix.'enewspublic')->first();
        $sitePrefix = $system->newsurl ? $system->newsurl : '/';
        $migrationInfo = DB::table('phpcms_migration')
            ->where(['type'=>'author', 'phpcms_table'=> $this->_pdbPrefix.'category'])
            ->select(['phpcms_id','ecms_id'])
            ->get()
            ->keyBy('phpcms_id')
            ->toArray();

        $this->output->progressStart(count($authors));

        $pinyins = [];

        foreach($authors as $author){
            $this->output->progressAdvance();

            $timestamp = time();
            $indexData = [
                'classid' => $eAuthorClass->classid,
                'checked' => 1,
                'newstime' => $timestamp,
                'truetime' => $timestamp,
                'lastdotime' => $timestamp,
                'havehtml' => 0,
            ];

            if(isset($migrationInfo[$author->catid])){
                $id = $migrationInfo[$author->catid]->ecms_id;
                DB::table($this->_indexTable)->where('id', $id)->update($indexData);
            }else{
                $id = DB::table($this->_indexTable)->insertGetId($indexData);
            }

            $pinyin= $pinyinTmp = pinyin($author->catname);
            $i = 2;
            while(in_array($pinyinTmp, $pinyins)){
                $pinyinTmp = $pinyin.$i;
                $i++;
            }

            $pinyin= $pinyinTmp;
            $mainData = [
                'classid' => $eAuthorClass->classid,
                'newspath' => date($eAuthorClass->newspath),
                'ismember' => 0,
                'titleurl' => $sitePrefix.$eAuthorClass->classpath.'/'.date($eAuthorClass->newspath).'/'.$id.$eAuthorClass->filetype,
                'filename' => $id,
                'title' => $author->catname,
                'titlepic' => $this->imgPathTransfer($sitePrefix, $author->image),
                'havehtml' => 0,
                'newstime' => $timestamp,
                'lastdotime' => $timestamp,
                'truetime' => $timestamp,
                'ispic' => empty($author->image) ? 0 : 1,
                'lastudtime' => $timestamp,
                'isurl' => 0,
                'smalltext' => $author->description,
                'stb' => 1,
                'infozm' => $pinyin,
            ];

            $sideData = [
                'classid' => $eAuthorClass->classid,
                'description' => $author->description,
            ];

            if(isset($migrationInfo[$author->catid])){
                DB::table($this->_mainTable)->where('id', $id)->update($mainData);
                DB::table($this->_sideTable)->where('id', $id)->update($sideData);
            }else{
                $mainData['id'] = $id;
                DB::table($this->_mainTable)->insert($mainData);

                $sideData['id'] = $id;
                DB::table($this->_sideTable)->insert($sideData);

                //插入迁移表信息
                $migration = [
                    'type'=>'author',
                    'name'=> $author->catname,
                    'phpcms_table' => $this->_pdbPrefix.'category',
                    'phpcms_id' => $author->catid,
                    'ecms_table' => $this->_mainTable,
                    'ecms_id' => $id,
                ];
                PhpcmsMigrationHelper::create($migration);
            }

        }

        //todo list
        /*
         * 1. titlepic 附件的处理
         * 2. 处理硬编码的文件路径 eg,titlepic(thumb),newstext(content),
         * 3. 投票的处理
         * 5. template 数据的搜集
         * 6. 置顶和头条推荐
         * 7. 评论的处理
         * */

        $authorNum = DB::table($this->_indexTable)->count();
        DB::table($this->_edbPrefix.'enewsclass')->where('classid', $eAuthorClass->classid)->update(['allinfos'=>$authorNum, 'infos'=>$authorNum]);

        $this->output->progressFinish();

    }

    private function keywordsTransfer($keywords){
        return str_replace(' ', ',', $keywords);
    }


    private function imgPathTransfer($sitePrefix, $str){
        return str_replace('http://www.cwzg.cn/uploadfile', 'http://static.cwzg.webbig.cn/p', $str);
    }
}
