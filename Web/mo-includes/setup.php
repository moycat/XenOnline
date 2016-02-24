<?php
/*
 * mo-includes/setup.php @ MoyOJ
 *
 * This file is to install MoyOJ web & database.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

session_start();

// Autoload MongoDB lib
function mongodb($classname) {
  $class = explode('\\', $classname);
  $file = 'mongolib/src';
  $c = count($class);
  for ($i = 1; $i < $c; ++$i) {
    $file .= '/'.$class[$i];
  }
  $file .= '.php';
  if (is_file($file)) {
    require $file;
  }
}

if (isset($_GET['action']) && $_GET['action'] == 'check') {
  if (!isset($_SESSION['mo_install']) || $_SESSION['mo_install'] < 2) {
    exit(0);
  }
  
  // Register autoload MongoDB library function
  spl_autoload_register('mongodb');
  require_once 'mongolib/src/functions.php';
  
  check_info();
  
  exit(0);
  // Check section ends
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MoyOJ安装向导</title>
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link href="//cdn.bootcss.com/flat-ui/2.2.2/css/flat-ui.min.css" rel="stylesheet">
<link href="static/html/common.css" rel="stylesheet">
<link href="static/html/setup.css" rel="stylesheet">
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="static/html/common.js"></script>
<script src="static/html/setup.js"></script>
<!--[if lt IE 9]>
  <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<?php
define('HAVE_INSTALLED', 100);
define('STEP1', 1);
define('STEP2', 2);
define('STEP3', 3);

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if (!isset($_SESSION['mo_install'])) {
  $_SESSION['mo_install'] = 1;
}
if ($_SESSION['mo_install'] < $step) {
  $step = $_SESSION['mo_install'];
}
if (file_exists('../mo-content/install.lock')) {
  $step = HAVE_INSTALLED;
}
?>

<body>
<div class="container">
  <div class="row">
    <div class="card">
      <h2>MoyOJ安装向导</h2>
      <div class="progress">
        <div id="progress" class="progress-bar" style="width: <?php echo ($step-1)*25; ?>%;"></div>
      </div>
      <?php
      switch ($step) {
        case HAVE_INSTALLED: ?>
      <div class="alert alert-danger">
        <p>检测到一把锁，说明MoyOJ已经安装过了！( º﹃º )</p>
        <p>要重新安装请删除<code>/mo-content/install.lock</code>文件 _(:3 」∠ )_</p>
      </div>
      <script>
      done_percent=100;
      </script>
      <?php
        break;
        case STEP1:
      ?>
      <script>
      done_percent=25;
      </script>
      <p>欢迎使用MoyOJ<s>这一天坑作品~</s>ヽ(✿ﾟ▽ﾟ)ノ</p>
      <p>MoyOJ利用PHP、MongoDB、Redis、Docker等软件，以推送式、分布式评测等技术，实现高效率高可靠的Online Judge。</p>
      <p>本向导将带领你完成MoyOJ<b>数据库及网页端</b>的安装。</p>
      <p>
      <h5>先来一发环境检测吧~</h5>
      </p>
      <?php
      // Check environment
      $fail = 0;
      $now['php'] = phpversion();
      $php_ver = explode('.', $now['php']);
      if ($php_ver[0] == 5 && $php_ver[1] >= 6) {
        $advice['php'] = '升级到7.0+';
        $info['php'] = 'warning';
        $fail++;
      } elseif (($php_ver[0] == 5 && $php_ver[1] < 6) || $php_ver[0] == 4) {
        $advice['php'] = '要求5.6或更高版本';
        $info['php'] = 'danger';
      } else {
        $advice['php'] = '无';
        $info['php'] = 'success';
      }
      $now['mongodb'] = phpversion('mongodb');
      if (!$now['mongodb']) {
        $now['mongodb'] = '无';
        $advice['mongodb'] = '安装MongoDB扩展';
        $info['mongodb'] = 'danger';
        $fail++;
      } else {
        $advice['mongodb'] = '无';
        $info['mongodb'] = 'success';
      }
      $now['redis'] = phpversion('redis');
      if (!$now['redis']) {
        $now['redis'] = '无';
        $advice['redis'] = '安装Redis扩展';
        $info['redis'] = 'danger';
        $fail++;
      } else {
        $advice['redis'] = '无';
        $info['redis'] = 'success';
      }
      $now['permission'] = is_writeable('../mo-content/') && is_writeable('../mo-content/upload/') &&
                            is_writeable('../mo-content/data/');
      if (!$now['permission']) {
        $now['permission'] = '不可写';
        $advice['permission'] = '调整权限';
        $info['permission'] = 'danger';
        $fail++;
      } else {
        $now['permission'] = '可写';
        $advice['permission'] = '无';
        $info['permission'] = 'success';
      }
      ?>
      <table class="table">
        <thead>
          <tr>
            <th>项目</th>
            <th>当前</th>
            <th>推荐</th>
            <th>建议</th>
          </tr>
        </thead>
        <tbody>
          <tr class="<?php echo $info['php']; ?>">
            <td>PHP版本</td>
            <td><code><?php echo $now['php']; ?></code></td>
            <td><code>7.0+</code></td>
            <td><?php echo $advice['php']; ?></td>
          </tr>
          <tr class="<?php echo $info['mongodb']; ?>">
            <td>MongoDB扩展</td>
            <td><code><?php echo $now['mongodb']; ?></code></td>
            <td><code>有</code></td>
            <td><?php echo $advice['mongodb']; ?></td>
          </tr>
          <tr class="<?php echo $info['redis']; ?>">
            <td>Redis扩展</td>
            <td><code><?php echo $now['redis']; ?></code></td>
            <td><code>有</code></td>
            <td><?php echo $advice['redis']; ?></td>
          </tr>
          <tr class="<?php echo $info['permission']; ?>">
            <td>目录权限</td>
            <td><?php echo $now['permission']; ?></td>
            <td><code>/mo-content</code>、<br>
              <code>/mo-content/upload</code>、<br>
              <code>/mo-content/data</code><br>
              可写</td>
            <td><?php echo $advice['permission']; ?></td>
          </tr>
        </tbody>
      </table>
      <?php
      if ($fail > 0) {
          $_SESSION['mo_install'] = 1;
      ?>
      <h5>噫(つд⊂)，环境检测中有不通过的地方。<br>
        解决后才能继续安装。</h5>
      <?php
      } else {
        if ($_SESSION['mo_install'] < 2) {
          $_SESSION['mo_install'] = 2;
        }
      ?>
      <h5>环境检测通过~可以继续了</h5>
      <a href="setup.php?step=2" class="btn btn-block btn-lg btn-info">开始配置各种库</a>
      <?php
      }
      break;
      case STEP2:
      ?>
      <script>
      done_percent=50;
      </script>
      <h5>接下来请提供数据库等一堆信息……</h5>
      <div id="info"></div>
      <form class="form-horizontal" role="form">
        <h6><span class="label label-default">MongoDB配置</span></h6>
        <div class="form-group">
          <label for="mongodb_host" class="col-sm-2 control-label">主机地址</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="mongodb_host" name="mongodb_host" 
            placeholder="localhost" value="localhost">
          </div>
        </div>
        <div class="form-group">
          <label for="mongodb_port" class="col-sm-2 control-label">主机端口</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="mongodb_port" name="mongodb_port" 
            placeholder="27017" value="27017">
          </div>
        </div>
        <div class="form-group">
          <label for="mongodb_user" class="col-sm-2 control-label">用户名</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="mongodb_user" name="mongodb_user" 
            placeholder="默认无须验证">
          </div>
        </div>
        <div class="form-group">
          <label for="mongodb_pwd" class="col-sm-2 control-label">密码</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="mongodb_pwd" name="mongodb_pwd" 
            placeholder="默认无须验证">
          </div>
        </div>
        <h6><span class="label label-default">Redis配置</span></h6>
        <div class="form-group">
          <label for="redis_host" class="col-sm-2 control-label">主机地址</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="redis_host" name="redis_host" 
            placeholder="localhost" value="localhost">
          </div>
        </div>
        <div class="form-group">
          <label for="redis_port" class="col-sm-2 control-label">主机端口</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="redis_port" name="redis_port" 
            placeholder="6379" value="6379">
          </div>
        </div>
        <div class="form-group">
          <label for="redis_pwd" class="col-sm-2 control-label">密码</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="redis_pwd" name="redis_pwd" 
            placeholder="默认无须验证">
          </div>
        </div>
        <h6><span class="label label-default">评测端Server配置</span></h6>
        <div class="form-group">
          <label for="server_host" class="col-sm-2 control-label">主机地址</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="server_host" name="server_host" 
            placeholder="localhost" value="localhost">
          </div>
        </div>
        <div class="form-group">
          <label for="server_port" class="col-sm-2 control-label">主机端口</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="server_port" name="server_port" 
            placeholder="6666" value="6666">
          </div>
        </div>
        <h6><span class="label label-default">添加一只管理员</span></h6>
        <div class="form-group">
          <label for="admin_name" class="col-sm-2 control-label">用户名</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="admin_name" name="admin_name" 
            placeholder="可用字母/数字">
          </div>
        </div>
        <div class="form-group">
          <label for="admin_pwd" class="col-sm-2 control-label">密码</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="admin_pwd" name="admin_pwd" 
            placeholder="至少8位，可用字母/数字/标点">
          </div>
        </div>
        <input type="button" class="btn btn-block btn-lg btn-info" onClick="check_info()" value="验证配置信息">
      </form>
      <?php
      break;
      case STEP3:
      ?>
      <script>
      done_percent=75;
      </script>
      <h5>正在安装……</h5>
      <p>正在设置MongoDB数据……<?php install_db() ?>完成！</p>
      <p>正在生成配置文件……完成！</p>
      <p>正在加锁……完成！</p>
      <?php
      break;
      default: ?>
      <div class="alert alert-warning">未定义操作 (눈‸눈)</div>
      <?php
       }
      ?>
      <div class="footer"> MoyOJ是一个开源项目，托管于GitHub。<a href="https://github.com/moycat/MoyOJ">项目地址</a> </div>
    </div>
  </div>
</div>
</body>
<script>
$(function(){  
    change_progress('progress', done_percent);
});
</script>
</html>

<?php

function install_db(){
  global $m;
}

function check_info(){
  global $m;
  $result = array('ok' => True, 'detail' => array(), 'loc' => array());
  $_GET['admin_name'] = base64_decode($_GET['admin_name']);
  $_GET['admin_pwd'] = base64_decode($_GET['admin_pwd']);
  $_GET['mongodb_pwd'] = base64_decode($_GET['mongodb_pwd']);
  $_GET['redis_pwd'] = base64_decode($_GET['redis_pwd']);
  // Check MongoDB
  $m_q = 'mongodb://';
  if ($_GET['mongodb_user'] || $_GET['mongodb_pwd']) {
    $m_q .= $_GET['mongodb_user'].':'.$_GET['mongodb_pwd'].'@';
  }
  $m_q .= $_GET['mongodb_host'].':'.$_GET['mongodb_port'];
  try
  {
    $m = new MongoDB\Client($m_q);
    $m->listDatabases();
  }
  catch(Exception $e)
  {
    $result['ok'] = False;
    $result['detail'][] = '<b>MongoDB</b><br>'.$e->getMessage();
    $result['loc'][] = 'mongodb_host';
    $result['loc'][] = 'mongodb_port';
    $result['loc'][] = 'mongodb_user';
    $result['loc'][] = 'mongodb_pwd';
  }
  // Check Redis
  $r = new Redis();
  if (!$r->pconnect($_GET['redis_host'], $_GET['redis_port'])) {
    $result['ok'] = False;
    $result['detail'][] = '<b>Redis</b><br>创造到'.$_GET['redis_host'].':'.$_GET['redis_port'].'的连接失败。';
    $result['loc'][] = 'redis_host';
    $result['loc'][] = 'redis_port';
  } elseif ($_GET['redis_pwd'] && !$r->auth($_GET['redis_pwd'])) {
    $result['ok'] = False;
    $result['detail'][] = '<b>Redis</b><br>'.$_GET['redis_host'].':'.$_GET['redis_port'].'的密码错误。';
    $result['loc'][] = 'redis_pwd';
  }
  // Check cilent server
  if(!$_GET['server_host']) {
    $result['ok'] = False;
    $result['loc'][] = 'server_host';
    $result['detail'][] = '必须填入评测端Server地址。';
  }
  if($_GET['server_port'] < 1 || $_GET['server_port'] > 65535) {
    $result['ok'] = False;
    $result['loc'][] = 'server_port';
    $result['detail'][] = '必须填入合法的评测端Server端口。';
  }
  // Check the new admim
  if(!$_GET['admin_name']) {
    $result['ok'] = False;
    $result['loc'][] = 'admin_name';
    $result['detail'][] = '必须填入管理员的用户名。';
  }
  if(!$_GET['admin_pwd'] || strlen($_GET['admin_pwd']) < 8) {
    $result['ok'] = False;
    $result['loc'][] = 'admin_pwd';
    $result['detail'][] = '必须设定合法的管理员密码。';
  }
  // Record
  if ($result['ok']) {
    $_SESSION['mo_install'] = 3;
    $_SESSION['mongodb_user'] = $_GET['mongodb_user'];
    $_SESSION['mongodb_pwd'] = $_GET['mongodb_pwd'];
    $_SESSION['mongodb_host'] = $_GET['mongodb_host'];
    $_SESSION['mongodb_port'] = $_GET['mongodb_port'];
    $_SESSION['redis_host'] = $_GET['redis_host'];
    $_SESSION['redis_port'] = $_GET['redis_port'];
    $_SESSION['redis_pwd'] = $_GET['redis_pwd'];
    $_SESSION['server_host'] = $_GET['server_host'];
    $_SESSION['server_port'] = $_GET['server_port'];
    $_SESSION['admin_name'] = $_GET['server_host'];
    $_SESSION['admin_pwd'] = $_GET['server_port'];
  }
  echo json_encode($result);
}