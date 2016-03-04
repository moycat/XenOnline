<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class CellFacadeProvider extends ServiceProvider
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
        App::bind('ProblemCell', function()
        {
            return new \App\Repositories\ProblemCell();
        });
        App::bind('SolutionCell', function()
        {
            return new \App\Repositories\SolutionCell();
        });
        App::bind('UserCell', function()
        {
            return new \App\Repositories\UserCell();
        });
        App::bind('CacheCell', function()
        {
            return new \App\Repositories\CacheCell();
        });
    }
}
