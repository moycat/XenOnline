<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';
require_once './db.php';
require_once './functions.php';
require_once '../../mo-config.php';

$worker = new Worker("Text://0.0.0.0:6666");
$worker->name = 'Tasker';

$worker->onWorkerStart = function($worker)
{
	global $db, $cid;
	$db = new DB();
	$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$cid = array();
	while ( !$db->connect() ) // 未连接自动重连
	{
		sleep(1);
	}
	Timer::add(3, function()use($worker) // 每3秒，自动发送心跳包
	{
		foreach($worker->connections as $connection)
			$connection->send("online\n");
	});
 };

$worker->onConnect = function($connection)
{
	$connection->IP = $connection->getRemoteIp();
	$connection->auth = False;
	$connection->cid = 0;
	$connection->name = '';
	$connection->deadline = Timer::add(5, function()use($connection) // 登录限时5秒，超时断开连接
	{
		Timer::del($connection->deadline);
		$connection->destroy();
		echo "A client timeout logging in. ( IP = $connection->IP )\n";
	});
	echo "A new client has joined. ( IP = $connection->IP )\n";
};

$worker->onMessage = function($connection, $data)
{
	$data = json_decode($data, True);
	if ($data == NULL || !isset($data['action']))
	{
		echo "Json decoding failed or in bad format. ( cid = $connection->cid, IP = $connection->IP )\n";
		return;
	}
	switch ($data['action'])
	{
		case 'login': // 评测端登录
			if (isset($data['client_id'], $data['client_hash'] ) && !isset($cid[$data['client_id']]) && !$connection->auth)
			{
		#		$result = login($connection, $data['client_id'], $data['client_hash']);
		#		sendMsg($connection, $result);
			}
			else
				echo "Bad Login Action ( IP = $connection->IP )\n";
			return;
		default:
			echo "Unknown Action ( cid = $connection->cid, IP = $connection->IP )\n";
	}
	
	sendMsg($connection, 'refuse');
};

function sendMsg($connection, $msg)
{
	$msg = json_encode($msg);
	$connection->send($msg);
};

$worker->onClose = function($connection)
{
    echo "A client closed the connection. ( cid = $connection->cid, IP = $connection->IP )\n";
};

Worker::runAll();
