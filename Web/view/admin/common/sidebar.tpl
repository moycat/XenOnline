<div class="sidebar">
    <a href="/"><img src="/static/image/logo.png" alt="site logo" id="site logo" class="img-circle side-avatar"></a>
    <h6 class="tight">{$site_name}</h6>
    <p class="tight">管理员：<code>{$user['username']}</code></p>
    <div role="group" id="side-userinfo">
        <a class="btn btn-info btn-xs" href="/" role="button">
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
            首页
        </a>
        <a class="btn btn-danger btn-xs" href="/user/logout" role="button">
            <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
            登出
        </a>
    </div>
</div>
<ul>
    <li>
        <a href="/admin/problem">题库管理
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </a>
    </li>
    <li>
        <a href="/admin/solution">提交管理
            <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
        </a>
    </li>
    <li>
        <a href="/admin/discussion">讨论管理
            <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>
        </a>
    </li>
    <li>
        <a href="/admin/user">用户管理
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
        </a>
    </li>
    <li>
        <a href="/admin/client">评测端管理
            <span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>
        </a>
    </li>
</ul>