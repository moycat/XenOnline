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
        view()->share('siteName', Config::get('app.site_name'));
        view()->composer('*', function ($view) {
            $view->with('user', Auth::user());
            $data = $view->getData();
            $header_title = isset($data['title']) ? $data['title'].' - '.Config::get('app.site_name') : Config::get('app.site_name');
            $view->with('header_title', $header_title);
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
