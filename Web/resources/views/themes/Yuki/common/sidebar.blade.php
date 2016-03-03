@if(Auth::check())
    <a href="/user"><img src="{{ $user->avatar }}" alt="Avatar" id="side-avatar" class="img-circle side-avatar"></a>
    <p id="side-username" class="tight">Hi, {{ $user->nickname }}</p>
    <div role="group" id="side-userinfo">
        <a href="/logout">
            <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
        </a>
    </div>
@else
    <a href="/user"><img src="/static/img/cat.png" alt="anonymous" id="side-avatar" class="img-circle side-avatar"></a>
    <p id="side-username" class="tight">Anonymous</p>
    <div role="group" id="side-userinfo">
        <div class="btn-group btn-group-xs">
            <button type="button" class="btn btn-danger" onclick="side_card('#side-login', '#side-register')">登录</button>
            <button type="button" class="btn btn-warning" onclick="side_card('#side-register', '#side-login')">注册</button>
        </div>
    </div>
@endif