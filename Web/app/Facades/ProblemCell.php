<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class ProblemCell extends Facade {
    protected static function getFacadeAccessor()
    {
        return 'ProblemCell';
    }
}