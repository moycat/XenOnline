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
        </div>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>
</div>