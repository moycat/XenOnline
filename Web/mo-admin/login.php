<?php
session_start();
define( 'ABSPATH', dirname( __FILE__ ). '/../' );
define( 'MOINC', ABSPATH. 'mo-includes/' );
define( 'MOCON', ABSPATH. 'mo-content/' );
if (isset($_POST['username'], $_POST['password']) && $_POST['username'] && $_POST['password'])
{
	require_once ABSPATH. 'mo-config.php';
	require_once MOINC. 'functions.php';
	require_once MOINC. 'class-db.php';
	if ( defined( 'MEM' ) && MEM == True )
	{
		$mem = new Memcached( 'moyoj' );
		$mem->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
		if ( !count( $mem->getServerList() ) )
		{
			$mem->addServer(MEM_HOST, MEM_PORT);
		}
	}
	$db = new DB();
	$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$db->connect();
	$sql = 'SELECT `id`, `username`, `password`, `nickname`, `role` FROM `mo_admin` WHERE `username` = ? AND `role` > 0 LIMIT 1';
	$db->prepare($sql);
	$db->bind('s', $_POST['username']);
	$result = $db->execute();
	if ($result && password_verify($_POST['password'], $result[0]['password']))
	{
		$result = $result[0];
		$_SESSION['aid'] = $result['id'];
		$_SESSION['admin_password'] = $result['password'];
	}
	else
	{
		$loginfail = True;
	}
}
if (isset($_GET['action']) && $_GET['action'] == 'logout')
{
	unset($_SESSION['aid']);
	unset($_SESSION['admin_password']);
	$logout = True;
}
if (isset($_SESSION['aid']))
{
	header("Location: index.php");
	exit(0);
}
?>
<!DOCTYPE html>
<html>
   <head>
      <title>管理登录</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="//cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
      <link href="inc/admin.css" rel="stylesheet">
      <script src="//cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
      <script src="//cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
   </head>
	<body>
		<div class="section">
			<div class="container logincon">
				<div class="row">
					<div class="col-md-12 login">
						<h1 class="text-center">
							后台登录
						</h1>
						 <?php
							if (isset($loginfail))
							{
							?>
								<div class="alert alert-danger">登录失败！请检查用户名和密码是否正确。</div>
							<?php
							}
							if (isset($logout))
							{
							?>
								<div class="alert alert-success">你已成功登出。</div>
							<?php
							}
						 ?>
						<form method="post" action="/mo-admin/login.php">
							<div class="form-group" id="username">
								<label class="control-label" for="username">
									用户名
								</label>
								<input id="username" class="form-control" type="text" name="username" placeholder="Username">
							</div>
							<div class="form-group" id="password">
								<label class="control-label" for="password">
									密码
								</label>
								<input id="password" class="form-control" type="password" name="password" placeholder="Password" />
							</div>
							 <button type="submit" class="btn btn-default">登录</button>
						</form>
					</div>
				</div>
			<br>
			<a href="/" title="误入深处？">← 返回主页</a>
			</div>
		</div>
	</body>
</html>
