<body>
<div id="Yuki">
    <nav class="left-nav">
        <div class="sidebar">
            @include('themes.Yuki.common.sidebar')
        </div>
        <ul>
            <li><a href="/">{{ $siteName }}
                <span class="glyphicon glyphicon-home" aria-hidden="true" href="/"></span></a></li>
            <li><a href="/problem">题库
                <span class="glyphicon glyphicon-pencil" aria-hidden="true" href="/"></span></a></li>
            <li><a href="/solution">提交
                <span class="glyphicon glyphicon-check" aria-hidden="true" href="/"></span></a></li>
            <li><a href="/user">用户
                <span class="glyphicon glyphicon-user" aria-hidden="true" href="/"></span></a></li>
        </ul>
    </nav>
    <div class="openNav" onclick="toggleMenu()" title="开关导航栏">
        <div class="icon"></div>
    </div>
    <div class="wrapper">
        <div class="user-op-card card top-border-red" id="side-login">
            <h5>
                登入网站ヽ(✿ﾟ▽ﾟ)ノ
                <small class="pull-right">
                    <button type="button" class="btn btn-danger btn-xs" onclick="close_side_card('#side-login')">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true">
                        </span>
                    </button>
                </small>
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
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
        <div class="user-op-card card top-border-orange" id="side-register">
            <h5>
                加入我们ヽ(๑•̀ω•́)ノ
                <small class="pull-right">
                    <button type="button" class="btn btn-warning btn-xs" onclick="close_side_card('#side-register')">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true">
                        </span>
                    </button>
                </small>
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
                    <small class="pull-right">最小长度8位</small>
                    <input type="password" class="form-control" name="password" id="Password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label for="RetypePassword">重复密码</label>
                    <small class="pull-right">再来一遍啦</small>
                    <input type="password" class="form-control" name="password_confirmation" id="RetypePassword" placeholder="Password Again" required>
                </div>
                <div class="form-submit-group">
                    <button type="submit" class="btn btn-warning">注册</button>
                </div>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
        <div class="container">
            @section('container')
            @show
        </div>
        @section('footer')
        @show
        @include('themes.Yuki.common.footer')
    </div>
    <a class="gotop tool-button" onclick="gotop()" href="#" title="返回顶部">
        <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
    </a>
</body>
<script src="//cdn.bootcss.com/jquery/2.2.1/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.js"></script>
<script src="/static/html/Yuki/common.js"></script>
@yield('extra_js')
<script src="//cdn.bootcss.com/ace/1.2.3/ace.js"></script>
<script src="//cdn.bootcss.com/iCheck/1.0.2/icheck.min.js"></script>
<script src="//cdn.bootcss.com/js-cookie/2.1.0/js.cookie.min.js"></script>
<script src="//cdn.bootcss.com/jquery.sticky/1.0.3/jquery.sticky.min.js"></script>

<script type="text/javascript">
    jQuery(function($) {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        $(document).ready( function() {
            @section('startup_js')
            @show
        });
    });
</script>
