<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use Input;
use Response;
use ProblemCell;
use SolutionCell;

class ProblemController extends Controller
{
    public function index(ProblemCell $cell)
    {

    }

    public function apiList(ProblemCell $cell)
    {
        $size = (int)Input::get('size', 20);
        $startID = (string)Input::get('startID', '000000000000000000000000');
        $list = $cell->index($size, $startID);

        return response()->json($list);
    }

    public function show(ProblemCell $cell, $pid)
    {
        $problem = $cell->find($pid);

        return Response::theme('problem.view', ['problem'=>$problem]);
    }

    public function submit(SolutionCell $cell, Request $request, $pid)
    {
        $result = $cell->add($request, ['pid'=>$pid]);

        return Response::json($result);
    }
}
