<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SolutionPending extends Eloquent
{
    protected $table = 'solutions_pending';

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = base64_encode($value);
    }

}
