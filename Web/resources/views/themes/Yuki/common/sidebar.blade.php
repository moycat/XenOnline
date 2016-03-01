@if(Auth::check())
    <a href="/user"><img src="{{ $user->avatar }}" alt="Avatar" id="side-avatar" class="img-circle side-avatar"></a>
    <p id="side-username" class="tight">Hi, {{ $user->nickname }}</p>
@else
    <a href="/user"><img src="/static/img/cat.png" alt="anonymous" id="side-avatar" class="img-circle side-avatar"></a>
    <p id="side-username" class="tight">Anonymous</p>
    <div class="btn-group btn-group-xs" role="group" id="side-userinfo" aria-label="登录/注册">
        <button type="button" class="btn btn-danger">登录</button>
        <button type="button" class="btn btn-warning">注册</button>
    </div>
@endif