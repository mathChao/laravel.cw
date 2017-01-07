<?php

namespace App\Console\Commands;

use App\Models\Author;
use Illuminate\Console\Command;
use Overtrue\Pinyin\Pinyin;


class ChangeAuthorToPinyin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:author';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $this->info('transfer author name from chinese to pinyin');

        $authors = Author::all();

        foreach($authors as $author){
            $pinyin = new Pinyin();  // 小内存型(默认)
            $author->filename = $pinyin->convert($author->title);
            $author->filename = implode('',$author->filename);
            $author->titleurl = str_replace($author->id,$author->filename,$author->titleurl);
            $author->save();
        }

        $this->info('finished');
    }

}
