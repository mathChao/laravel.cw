<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;
use App\Services\Image;
use Config;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::singleton('Image',function(){
            $setting = Config::get('image.setting');
            return new Image($setting['host'],$setting['key']);
        });
    }
}
