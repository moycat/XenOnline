<?php

namespace App\Repositories;

use Cell;
use App\Problem;

class ProblemCell extends Cell
{
    protected $load;
    protected $failedToLoad;

    public function index($size, $startID, $filter = array())
    {
        $result = Problem::where('_id', '>', $startID)->take($size)->get();

        return $result;
    }

    public function find($id)
    {
        return Problem::findOrFail($id);
    }

    public function add($info, $option)
    {
        // TODO: Implement add() method.
    }

    public function count($filter = array())
    {
        return Problem::count();
    }
}