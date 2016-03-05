@extends('page')
@section('container')
    <div class="row">
        <div class="col-md-12">
            <div id="main-card" class="card">
                <h1>{{ $siteName }} <small class="mysbr">欢迎你的到来</small></h1>
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
            @if(Auth::check())
            <div id="user-card" class="card top-border-red">
                <h5>
                    Hi, {{ $user->nickname }} ヽ(✿ﾟ▽ﾟ)ノ
                </h5>
                <h5><small>//TODO: 用户信息板尚未上线</small></h5>
            </div>
            @else
            <div id="login-card" class="card top-border-red">
                <h5>
                    登入网站ヽ(✿ﾟ▽ﾟ)ノ
                </h5>
                <form action="/user" method="post">
                    <div class="form-group">
                        <label for="Email">Email地址</label>
                        <input type="email" class="form-control" name="email" id="Email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="Password">密码</label>
                        <input type="password" class="form-control" name="password" id="Password" placeholder="Password">
                    </div>
                    <div class="form-submit-group">
                        <button type="submit" class="btn btn-danger">登录</button>　
                        <input type="checkbox" name="forgetmenot" class="forgetmenot"> 记住我
                        <div class="pull-right">
                            <input class="btn btn-warning" type="button" value="还没注册？" onclick="toggleCard('login-card','register-card')">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div id="register-card" class="card top-border-orange back-hidden" style="display: none;">
                <h5>
                    加入我们ヽ(๑•̀ω•́)ノ
                </h5>
                <form action="/user" method="post">
                    <div class="form-group">
                        <label for="Email">Email地址</label>
                        <small class="pull-right">就是你的Email地址……啦</small>
                        <input type="email" class="form-control" name="email" id="Email" placeholder="juruo@example.com">
                    </div>
                    <div class="form-group">
                        <label for="nickname">昵称</label>
                        <small class="pull-right">3-10字，中英文不限</small>
                        <input type="text" class="form-control" name="nickname" id="nickname" placeholder="神犇">
                    </div>
                    <div class="form-group">
                        <label for="Password">密码</label>
                        <small class="pull-right">最小长度6位</small>
                        <input type="password" class="form-control" name="password" id="Password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="RetypePassword">重复密码</label>
                        <small class="pull-right">再来一遍啦</small>
                        <input type="password" class="form-control" name="password_confirmation" id="RetypePassword" placeholder="Password Again" required>
                    </div>
                    <div class="form-submit-group">
                        <button type="submit" class="btn btn-warning">注册</button>　
                        <div class="pull-right">
                            <input class="btn btn-danger" type="button" value="需要登录？" onclick="toggleCard('login-card','register-card')">
                        </div>
                    </div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="card top-border-blue">
                <h5><small>//TODO: 我也不知道是啥的信息板尚未上线</small></h5>
            </div>
        </div>
    </div>
@stop
