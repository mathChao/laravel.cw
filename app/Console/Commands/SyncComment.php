<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncDuoShuoComments;

class SyncComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $handler  = new SyncDuoShuoComments();
        $result = $handler->syncComment(5000);

        if($result['result'] === 'success'){
            $this->info('success:'.$result['number']);
        }else{
            $this->info('fail:'.$result['error']);
        }
    }

}
