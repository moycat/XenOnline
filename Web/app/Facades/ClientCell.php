<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class ClientCell extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'ClientCell';
    }
}