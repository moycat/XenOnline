<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Response;
use SolutionCell;

class SolutionController extends Controller
{
    public function show($sid)
    {
        $solution = SolutionCell::find($sid);

        return view('solution.view', ['solution'=>$solution]);
    }

    public function submit(Request $request)
    {
        $result = SolutionCell::add($request);

        return Response::json($result);
    }

}
