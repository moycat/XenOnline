@extends('themes.Yuki.common.page')
@section('container')
    <div class="row">
        <div class="col-md-12">
            <div id="main-card" class="card">
                <h1>{{ $siteName }} <br class="visible-xs"><small>欢迎你的到来</small></h1>
                <h4>这是一个新建的Online Judge网站，欢迎你的参与。</h4>
                <p class="alert-sign">迄今为止，我们已经有：</p>
                <ul class="list-inline h6">
                    <li>
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                        题目x{{ ProblemCell::count() }}
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                        提交x{{ SolutionCell::count() }}
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        用户x{{ UserCell::count() }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="user-card" class="card top-border-orange">
                @if(Auth::check())
                @else
                <h4>登入……或者加入我们<small>ヽ(✿ﾟ▽ﾟ)ノ</small></h4>
                @endif
            </div>
        </div>
    </div>
@stop