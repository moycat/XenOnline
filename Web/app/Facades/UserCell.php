<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class UserCell extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'UserCell';
    }
}