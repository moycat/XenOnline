<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use Input;
use Response;
use pRedis;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return view('user.index');
    }

    public function login(Request $request)
    {
        $auth = false;
        $stamp = $request->only('email', 'password');
        if (Auth::attempt($stamp, $request->has('forgetmenot'))) {
            $auth = true;
        }
        $user = Auth::user();
        if ($request->ajax()) {
            return response()->json([
                'auth' => $auth,
                'avatar' => $user ? $user->avatar : null,
                'nickname' => $user ? $user->nickname : null
            ]);
        } else {
            return redirect('/user');
        }
    }
}
