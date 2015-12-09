<?php
	global $user;
	if ( isset( $mo_request[1] ) && $mo_request[1] == 'logout' )
	{
		if( $user->logout() )
		{
			echo '登出成功！<br>';
		}
	}
	if ( isset( $mo_request[1] ) && $mo_request[1] == 'register' && isset( $_POST['username'], $_POST['password'], $_POST['email'] ) )
	{
		if( mo_add_user( $_POST['username'], $_POST['password'], $_POST['email'] ) )
		{
			echo '注册成功！<br>';
		}
		else
		{
			echo '注册失败……检查输入是否有误，或重名<br>';
		}
	}
	if ( $user->getUID() )
	{
		echo $user->get( 'status', 'nickname' ). '，你已登录。<br>';
		echo '<a href="/?r=user/logout">点此退出</a><br>';
	}
	else
	{
		echo '你尚未登录！<br>';
		?>
		<form name="form2" method="post" action="/?r=user">
		  <p>登录名
			<input name="login_name" type="text" required id="login_name">
		  </p>
		  <p>密　码
			<input type="password" name="password" id="password">
			<input type="hidden" name="login" id="login">
		  </p>
		  <p>
			<input type="checkbox" name="auto_login" id="auto_login" checked>
			<label for="auto_login">自动登录 </label>
		  </p>
		  <p>
			<input type="submit" name="submit" id="submit" value="登录">
		  </p>
		</form>
		<form name="form3" method="post" action="/?r=user/register">
		  用户名
		  <input name="username" type="text" required id="username">
		  <p>密　码
			<input type="password" name="password" id="password">
		  </p>
		  <p>邮　箱
			<input name="email" type="text" required id="email">
		  </p>
		  <p>
			<input type="submit" name="submit" id="submit" value="注册">
		  </p>
		</form>
		<?php
	}