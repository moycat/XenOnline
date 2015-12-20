<?php
session_start();
if (!isset($_SESSION['aid']))
{
	header("Location: login.php");
	exit(0);
}
define( 'ABSPATH', dirname( __FILE__ ). '/../' );
define( 'MOINC', ABSPATH. 'mo-includes/' );
define( 'MOCON', ABSPATH. 'mo-content/' );
define( 'MOCACHE', MOCON. 'cache/' );
require_once ABSPATH. 'mo-config.php';
require_once MOINC. 'functions.php';
require_once MOINC. 'class-db.php';
require_once MOINC. 'class-discussion.php';
require_once MOINC. 'class-user.php';
require_once MOINC. 'class-problem.php';
require_once MOINC. 'function-action.php';
require_once MOINC. 'function-discussion.php';
require_once MOINC. 'function-data.php';
require_once MOINC. 'function-log.php';
require_once MOINC. 'function-problem.php';
require_once MOINC. 'function-user.php';
require_once 'functions.php';

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
$admin_info = mo_read_cache('mo-admin-'. $_SESSION['aid']);
if (!$admin_info)
{
	$sql = 'SELECT `id`, `username`, `password`, `nickname`, `role` FROM `mo_admin` WHERE `id` = ? AND `role` > 0';
	$db->prepare($sql);
	$db->bind('i', $_SESSION['aid']);
	$result = $db->execute();
	if (!$result || $result[0]['password'] != $_SESSION['admin_password'])
	{
		unset($_SESSION['aid']);
		header("Location: login.php");
		exit(0);
	}
	mo_write_cache('mo-admin-'. $_SESSION['aid'] , $result[0]);
}
$mo_settings = array();
mo_load_settings();
if (!isset($active))
{
	$active = '';
}
?>

<html>
   <head>
      <title><?php echo mo_get_option('site_name'); ?> - 管理后台</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
      <link href="inc/admin.css" rel="stylesheet">
      <script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
      <script src="http://apps.bdimg.com/libs/bootstrap/3.3.0/js/bootstrap.min.js"></script>
      <?php if (isset($head)) echo $head; ?>
   </head>
<body class="bg">
<div class="navbar navbar-default navbar-static-top">
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
			<a class="navbar-brand" href="#">
				<?php echo mo_get_option('site_name'); ?> - 管理后台
			</a>
		</div>
		<div class="collapse navbar-collapse" id="navbar-ex-collapse">
			<ul class="nav navbar-nav">
				<li<?php ($active == 'overview')?print(' class="active"'):0; ?>>
					<a href="index.php">
						概览
					</a>
				</li>
				<li<?php ($active == 'problem')?print(' class="active"'):0; ?>>
					<a href="problem.php">
						题库
					</a>
				</li>
				<li<?php ($active == 'data')?print(' class="active dropdown" '):print(' class="dropdown" '); ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						数据 
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li><a href="solution.php">提交</a></li>
						<li><a href="discussion.php">讨论</a></li>
						<li><a href="file.php">文件</a></li>
					</ul>
				</li>
				<li<?php ($active == 'user')?print(' class="active"'):0; ?>>
					<a href="user.php">
						用户
					</a>
				</li<?php ($active == 'setting')?print(' class="active"'):0; ?>>
				<li>
					<a href="setting.php">
						设置
					</a>
				</li>
				<li>
					<a href="login.php?action=logout">
						退出后台
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
