<div class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
				<span class="sr-only">
					Toggle navigation
				</span>
				<span class="icon-bar">
				</span>
				<span class="icon-bar">
				</span>
				<span class="icon-bar">
				</span>
            </button>
            <a class="navbar-brand" href="/admin">
                {{ $siteName }}
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
            <ul class="nav navbar-nav">
                @if(isset($active) && $active == 'overview')
                <li class="active">
                @else
                <li>
                @endif
                    <a href="/admin">
                        概览
                    </a>
                @if(isset($active) && $active == 'problem')
                    <li class="active">
                @else
                    <li>
                @endif
                    <a href="/admin/problem">
                        题库
                    </a>
                </li>
                @if(isset($active) && $active == 'data')
                <li class="active">
                @else
                <li>
                @endif
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        数据
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/solution">提交</a></li>
                        <li><a href="/admin/discussion">讨论</a></li>
                        <li><a href="/admin/file">文件</a></li>
                    </ul>
                </li>
                @if(isset($active) && $active == 'user')
                <li class="active">
                @else
                <li>
                @endif
                    <a href="/admin/user">
                        用户
                    </a>
                </li>
                @if(isset($active) && $active == 'client')
                <li class="active">
                @else
                <li>
                @endif
                    <a href="/admin/client">
                        评测端
                    </a>
                </li>
                @if(isset($active) && $active == 'setting')
                <li class="active">
                @else
                <li>
                @endif
                    <a href="/admin/setting">
                        设置
                    </a>
                </li>
                <li>
                    <a href="/">
                        返回前台
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>