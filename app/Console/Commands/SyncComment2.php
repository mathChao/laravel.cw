<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncDuoShuoComments2;

class SyncComment2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:comment2 {number=1000}';

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
        $handler  = new SyncDuoShuoComments2();
        $number = $this->argument('number');

        while($number){
            $result = $handler->syncComment($number);
            if($result['result'] === 'success'){
                $this->info('success:'.$result['number']);
            }else{
                $this->info('fail:'.$result['error']);
            }
            $number = $result['number'];
        }
    }

}
