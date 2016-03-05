<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Client extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'intro', 'load_1', 'load_5', 'load_15', 'memory', 'memory', 'hash',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['hash'];

}
