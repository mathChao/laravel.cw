<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Mail\NoticePassword as EmailNoticePassword;

use DB;
use Mail;


class NoticePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notice:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected $_edbPrefix = null;

    public function __construct()
    {
        parent::__construct();
        $this->_edbPrefix = config('cwzg.edbPrefix');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('向用户发用密码更新的邮件提醒');

        $table = $this->_edbPrefix.'enewsmember';


        $this->output->progressStart(DB::table($table)->where('password2','!=', '')->count());

        $users = DB::table($table)->where('password2','!=', '')->get()->toArray();
        foreach ($users as $user){
            //Mail::to($user->email)->send(new EmailNoticePassword($user));
            Mail::to('1508066846@qq.com')->send(new EmailNoticePassword($user));
            $this->output->progressAdvance();
            break;
        }

        $this->output->progressFinish();
    }
}
