<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class CacheCell extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'CacheCell';
    }
}