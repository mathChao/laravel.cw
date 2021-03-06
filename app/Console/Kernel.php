<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\MigrationNews',
        'App\Console\Commands\MigrationUser',
        'App\Console\Commands\MigrationAuthor',
        'App\Console\Commands\MigrationSpecial',
        'App\Console\Commands\MigrationLink',
        'App\Console\Commands\MigrationComments',
        'App\Console\Commands\GenerateKeywords',
        'App\Console\Commands\GenerateOnclick',
        'App\Console\Commands\MigrationBefrom',
        'App\Console\Commands\GenerateTags',
        'App\Console\Commands\ChangeAuthorToPinyin',
        'App\Console\Commands\SyncComment',
        'App\Console\Commands\SyncComment2',
        'App\Console\Commands\SyncTags',
        'App\Console\Commands\NoticePassword',
        'App\Console\Commands\genChangYanComment',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
