<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Input;
use Response;
use SolutionCell;

class SolutionController extends Controller
{

    public function apiList(SolutionCell $cell)
    {
        $size = (int)Input::get('size', 20);
        $startID = (string)Input::get('startID', '000000000000000000000000');
        $list = $cell->index($size, $startID);

        return response()->json($list);
    }

    public function show(SolutionCell $cell, $sid)
    {
        $solution = $cell->find($sid);

        return Response::theme('solution.view', ['solution'=>$solution]);
    }

    public function submit()
    {

    }

}
