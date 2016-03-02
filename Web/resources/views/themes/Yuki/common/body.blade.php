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
            $(document).ready( function() {
                @section('startup_js')
                @show
            });
        });
    </script>
