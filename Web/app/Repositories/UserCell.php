<?php

namespace App\Repositories;

use Cell;
use App\User;

class UserCell implements Cell
{
    public function index($size, $startID, $filter = array())
    {
        $result = User::where('_id', '>', $startID)->take($size)->get();

        return $result;
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function add($info, $option)
    {
        // TODO: Implement add() method.
    }

    public function count($filter = array())
    {
        return User::count();
    }
}