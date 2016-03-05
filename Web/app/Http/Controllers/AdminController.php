<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use ClientCell;
use ProblemCell;
use SolutionCell;
use UserCell;

class AdminController extends Controller
{
    public function index()
    {
        $data = [
            'active' => 'overview',
            'problemCount' => ProblemCell::count(),
            'solutionCount' => SolutionCell::count(),
            'clientCount' => ClientCell::count(),
            'clientOnCount' => count(ClientCell::getOn()),
            'userCount' => UserCell::count(),
        ];

        return view('admin.index', $data);
    }

    public function hitokoto()
    {
        $url = 'http://api.hitokoto.us/rand?encode=js&charset=utf-8';
        $content = file_get_contents($url);
        $content = explode('"', $content);
        $content = 'function hitokoto(){$(".hitokoto").append("'.$content[1].'");}';
        return response($content)
            ->header('Content-Type', 'text/javascript;charset=utf-8');
    }
}
