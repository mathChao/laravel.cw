<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\ModelHelpers\PhpcmsMigrationHelper;


class MigrationUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $edbPrefix = '';
    protected $pdbPrefix = '';
    protected $eMainTable = '';
    protected $eSideTable = '';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->edbPrefix = config('cwzg.edbPrefix');
        $this->pdbPrefix = config('cwzg.pdbPrefix');
        $this->eMainTable = $this->edbPrefix.'enewsmember';
        $this->eSideTable = $this->edbPrefix.'enewsmemberadd';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $this->migrateMember();
    }

    private function migrateMember(){
        $this->info('迁移用户信息');

        $migrateInfo = DB::table('phpcms_migration')
            ->where(['type'=>'member', 'phpcms_table'=>$this->pdbPrefix.'member'])
            ->select(['phpcms_id','ecms_id'])
            ->get()
            ->keyBy('phpcms_id')
            ->toArray();
        $puserids = array_keys($migrateInfo);
        $members = DB::table($this->pdbPrefix.'member')->get();

        $this->output->progressStart(count($members));

        foreach($members as $member){
            $this->output->progressAdvance();

            $newpwd = substr(md5($member->password), 0, 8);
            $encrypt = $this->generateUserPwd($newpwd);
            $eMemberMainData = [
                'username' => $member->username,
                'password' => $encrypt['password'],
                'rnd' => $encrypt['rnd'],
                'salt' => $encrypt['salt'],
                'userkey' => $encrypt['userkey'],
                'password2' => $newpwd,
                'email' => $member->email,
                'registertime' => $member->regdate,
                'groupid' => 1,
                'userfen' => $member->point,
                'money' => $member->amount,
                'checked' => 1,
                'actived' => 1,
            ];

            $eMemberSideData = [
                'regip' => $member->regip,
                'lasttime'=> $member->lastdate,
                'lastip' => $member->lastip,
                'loginnum' => $member->loginnum,
                'saytext' => '',
                'spacegg'=> '',
            ];

            if(in_array($member->userid, $puserids)){
                $euserid = $migrateInfo[$member->userid]->ecms_id;
                DB::table($this->eMainTable)->where('userid', $euserid)->update($eMemberMainData);
                DB::table($this->eSideTable)->where('userid', $euserid)->update($eMemberSideData);
            }else{
                $puserids[] = $member->userid;

                $euserid = DB::table($this->eMainTable)->insertGetId($eMemberMainData);

                $eMemberSideData['userid'] = $euserid;
                DB::table($this->eSideTable)->insert($eMemberSideData);

                $migration = [
                    'type'=>'member',
                    'name'=>$member->username,
                    'phpcms_table' => $this->pdbPrefix.'member',
                    'phpcms_id' => $member->userid,
                    'ecms_table' => $this->edbPrefix.'enewsmember',
                    'ecms_id' => $euserid,
                ];
                PhpcmsMigrationHelper::create($migration);
            }
        }

        $this->output->progressFinish();
    }

    private function generateUserPwd($pwd){
        $rnd = $this->make_password(20);
        $userkey = $this->make_password(12);
        $salt = $this->make_password(6);
        $newpwd=md5(md5($pwd).$salt);
        return [
            'rnd' => $rnd,
            'userkey' => $userkey,
            'salt' => $salt,
            'password' => $newpwd,
        ];
    }

    //取得随机数
    private function make_password($pw_length){
        $low_ascii_bound=48;
        $upper_ascii_bound=122;
        $notuse=array(58,59,60,61,62,63,64,91,92,93,94,95,96);
        $i = 0;
        $password1 = '';
        while($i<$pw_length)
        {
            if(PHP_VERSION<'4.2.0')
            {
                mt_srand((double)microtime()*1000000);
            }
            $randnum=mt_rand($low_ascii_bound,$upper_ascii_bound);
            if(!in_array($randnum,$notuse))
            {
                $password1=$password1.chr($randnum);
                $i++;
            }
        }
        return $password1;
    }
}

