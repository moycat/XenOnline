<?php

namespace App\Repositories;

use Auth;
use pRedis;
use Cell;
use Response;
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

    public function add($request, $option = array())
    {
        $user = Auth::user();
        $pid = $request->input('pid');
        $problem = Problem::findOrFail($pid);
        $solution = new Solution;
        $solution_pending = new SolutionPending;

        // 验证
        // 用户处理

        $solution->problem_id = $problem->id;
        $solution->user_id = $user->id;
        $solution->language = $request->input('language');
        $solution->code = $request->input('code');
        $solution->code_length = strlen($request->input('code'));
        $solution->save();

        $solution_pending->solution_id = $solution->id;
        $solution_pending->user_id = $user->id;
        $solution_pending->language = $request->input('language');
        $solution_pending->code = $request->input('code');
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

    static public function state($state, $short = false, $with_label = true)
    {
        switch ((int) $state) {
            case 10:$rt = $short ? 'AC' : 'Accepted';
                $label = 'success';
                break;
            case 6:$rt = $short ? 'WA' : 'Wrong Answer';
                $label = 'danger';
                break;
            case 4:$rt = $short ? 'RE' : 'Runtime Error';
                $label = 'danger';
                break;
            case 0:$rt = $short ? 'WAIT' : 'Waiting...';
                $label = 'primary';
                break;
            case 1:$rt = $short ? 'CE' : 'Compile Error';
                $label = 'warning';
                break;
            case 2:$rt = $short ? 'MLE' : 'Memory Limit Exceed';
                $label = 'danger';
                break;
            case 3:$rt = $short ? 'TLE' : 'Time Limit Exceed';
                $label = 'danger';
                break;
            case -3:$rt = $short ? 'RUN' : 'Running...';
                $label = 'info';
                break;
            case -2:$rt = $short ? 'COM' : 'Compiling...';
                $label = 'info';
                break;
            default:$rt = $short ? '???' : 'Unknown Status';
                $label = 'default';
                break;
        }
        if ($with_label) {
            return '<span class="label label-'.$label.'">'.$rt.'</span>';
        }
        return $rt;
    }

    static public function language($lang, $code = true)
    {
        switch ($lang) {
            case 1:$rt = 'C++';break;
            case 2:$rt = 'Pascal';break;
            case 3:$rt = 'Java';break;
            default:$rt = 'Unknown';break;
        }
        if ($code) {
            return '<code>'.$rt.'</code>';
        }
        return $rt;
    }

}