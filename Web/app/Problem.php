<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Problem extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'tag', 'test_turn', 'time_limit', 'memory_limit',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'hash', 'ver',
    ];

    public function solutions()
    {
        return $this->hasMany('App\Solution');
    }

}
