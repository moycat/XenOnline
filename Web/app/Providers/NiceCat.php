<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Auth;
use Config;
use Response;

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
        // Add some to every view
        view()->share('user', Auth::user());
        view()->share('siteName', Config::get('app.site_name'));
        view()->share('title', Config::get('app.site_name'));
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
