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
        // Add the current user to every view
        view()->share('user', Auth::user());
        // Add theme feature & Bring some values for the views
        Response::macro('theme', function($view, $data=array()) {
            // Prepare the theme path
            $theme = Config::get('theme', 'Yuki');
            $path = 'themes.'.$theme.'.'.$view;

            // Add some values for the view
            $data['siteName'] = Config::get('app.site_name');
            $data['user'] = Auth::user();
            if (isset($data['title'])) {
                $data['title'] .= ' - '.$data['siteName'];
            } else {
                $data['title'] = $data['siteName'];
            }

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
