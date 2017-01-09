<?php

namespace App\Console\Commands;

use App\ModelHelpers\PhpcmsMigrationHelper;
use Illuminate\Console\Command;

use DB;
use Cache;


class GenerateLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:link';

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
        $this->info('迁移友情链接');
        $pLinkTable = $this->_pdbPrefix.'link';
        $eLinkTable = $this->_edbPrefix.'enewslink';

        $this->output->progressStart(DB::table($pLinkTable)->count());
        $links = DB::table($pLinkTable)->get();
        $migrations = DB::table('phpcms_migration')
            ->select(['phpcms_id', 'ecms_id'])
            ->where(['phpcms_table'=>$pLinkTable, 'type'=>'link', 'ecms_table'=>$eLinkTable])
            ->get()
            ->keyBy('phpcms_id')
            ->toArray();

        $typeMap = [
            0 => 0,
            58 => 4,
            59 => 5,
            60 => 6
        ];
        foreach($links as $link){
            $this->output->progressAdvance();
            $data = [
                'lname'=>$link->name,
                'lpic'=>0,
                'lurl'=>$link->url,
                'ltime'=>date('Y-m-d h:m:s',$link->addtime),
                'width'=>0,
                'height'=>0,
                'target'=>'_blank',
                'myorder'=>$link->listorder,
                'lsay'=>$link->introduce,
                'ltype'=>0,
                'classid'=>isset($typeMap[$link->typeid]) ? $typeMap[$link->typeid] : 0,
            ];

            if(isset($migrations[$link->linkid])){
                DB::table($eLinkTable)->where('lid', $migrations[$link->linkid]->ecms_id)->update($data);
            }else{
                $id = DB::table($eLinkTable)->insertGetId($data);

                $migration = [
                    'type'=>'link',
                    'name'=>$link->name,
                    'phpcms_table' => $pLinkTable,
                    'phpcms_id' => $link->linkid,
                    'ecms_table' => $eLinkTable,
                    'ecms_id' => $id,
                ];

                PhpcmsMigrationHelper::create($migration);
            }
        }


        $this->output->progressFinish();
    }
}
