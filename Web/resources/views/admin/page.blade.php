<html>
@include('admin.header')
<body>
@include('admin.nav')
<div class="section">
    <div class="container">
    @section('container')
    @show
    </div>
</div>
<p class="text-center hitokoto">
    <script>
        $().ready(function() {
            hitokoto();
        });
    </script>
</p>
</body>
<script src="//cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="/static/html/admin.js"></script>
<script type="text/javascript" src="/hitokoto"></script>
</html>