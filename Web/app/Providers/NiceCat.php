<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

class NiceCat extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Add some features for MoyOJ
     *
     * @return void
     */
    public function boot()
    {
        // Add theme feature & Bring some values for the views
        Response::macro('theme', function($templet, $data=array()) {
            // Prepare the theme path
            $theme = Config::get('theme', 'Yuki');
            $path = 'themes.'.$theme.'.'.$templet;
            // Add some values for the view
            $data['siteName']=Config::get('site_name');
            return Response::view($path, $data);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
