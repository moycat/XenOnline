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
    public function index()
    {

    }

    public function apiList()
    {
        $size = (int)Input::get('size', 20);
        $startID = (string)Input::get('startID', '000000000000000000000000');
        $list = ProblemCell::index($size, $startID);

        return response()->json($list);
    }

    public function show($pid)
    {
        $problem = ProblemCell::find($pid);

        return Response::theme('problem.view', ['problem'=>$problem]);
    }

    public function submit(Request $request, $pid)
    {
        $result = SolutionCell::add($request, ['pid'=>$pid]);

        return Response::json($result);
    }
}
