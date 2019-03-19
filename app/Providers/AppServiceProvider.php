<?php

namespace App\Providers;

use Faker\Provider\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Filesystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $confDir = '../config/config.json';
        if (file_exists($confDir)) {
            $config = json_decode(file_get_contents($confDir), true);

            View::share($config);
        }
    }
}
