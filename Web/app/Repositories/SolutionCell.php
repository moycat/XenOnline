<?php

namespace App\Repositories;

use Auth;
use pRedis;

use Cell;
use App\Solution;
use App\SolutionPending;
use App\Problem;

class SolutionCell extends Cell
{
    protected $load;
    protected $failedToLoad;

    public function index($size, $startID, $filter = array())
    {
        $result = Solution::where('_id', '>', $startID)->take($size)->get();

        return $result;
    }

    public function find($id)
    {
        $solution = Solution::findOrFail($id);

        $user = Auth::user();
        if ($solution->user_id != $user->id) {
            abort(404);
        }

        return $solution;
    }

    public function add($info, $option)
    {
        $user = Auth::user();
        $problem = Problem::findOrFail($pid);
        $solution = new Solution;
        $solution_pending = new SolutionPending;

        $solution->problem_id = $problem->id;
        $solution->user_id = $user->id;
        $solution->language = $info->input('language');
        $solution->code = $info->input('code');
        $solution->code_length = strlen($info->input('code'));
        $solution->save();

        $solution_pending->solution_id = $solution->id;
        $solution_pending->user_id = $user->id;
        $solution_pending->language = $info->input('language');
        $solution_pending->code = $info->input('code');
        $solution_pending->hash = $problem->hash;
        $solution_pending->ver = $problem->ver;
        $solution_pending->test_turn = $problem->test_turn;
        $solution_pending->time_limit = $problem->time_limit;
        $solution_pending->memory_limit = $problem->memory_limit;
        $solution_pending->save();

        $channel = 'mo://MoyOJ/ClientServer';
        pRedis::publish($channel, $solution_pending->toJson());

        $result = ['ok'=>True, 'sid'=>$solution->id];
        return $result;
    }

    public function count($filter = array())
    {
        return Solution::count();
    }

}