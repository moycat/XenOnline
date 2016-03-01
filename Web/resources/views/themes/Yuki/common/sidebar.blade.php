@if(Auth::check())
    <img src="{{ UserCell::getAvatar() }}" alt="Avatar" class="img-circle side-avatar">
@else
    <img src="/static/img/cat.png" alt="anonymous" class="img-circle side-avatar">
    <p class="tight">尚未登录</p>
    <div class="btn-group btn-group-xs btna" role="group" aria-label="登录/注册">
        <button type="button" class="btn btn-danger">登录</button>
        <button type="button" class="btn btn-warning">注册</button>
    </div>
@endif