<?php

namespace App\Repositories;

use Auth;
use Cell;
use App\User;

class UserCell extends Cell
{
    protected $load;
    protected $failedToLoad;

    public function index($size, $startID, $filter = array())
    {
        $result = User::where('_id', '>', $startID)->take($size)->get();

        return $result;
    }

    public function find($uid)
    {
        return User::findOrFail($uid);
    }

    public function add($info, $option)
    {
        // TODO: Implement add() method.
    }

    public function count($filter = array())
    {
        return User::count();
    }

    public function save($user)
    {
        if (is_object($user)) {
            $user->save();
            // TODO: Update the cache
        } else {

        }
    }
}