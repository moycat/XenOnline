<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

$worker_tasker->onWorkerStart = function($worker_tasker)
{
	global $db, $mem;
	$db = new DB();
	$db->init(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	while (!$db->connect()) // 未连接自动重连
		sleep(1);
	if (MEM)
	{
		$mem = new Memcached;
		$mem->addServer(MEM_HOST, MEM_PORT);
	}
	Timer::add(3, function()use($worker_tasker) // 每3秒，自动发送心跳包
	{
		foreach($worker_tasker->connections as $connection)
			sendMsg($connection, array('action' => 'online'));
	});
	Timer::add(10, 'check_lost'); // 每10秒，检查无响应的评测请求
	Timer::add(10, 'check_forgotten'); // 每30秒，在数据库中寻找丢失的请求
	p('The server <Tasker> has started.');
 };

$worker_tasker->onConnect = function($connection)
{
	$connection->IP = $connection->getRemoteIp();
	$connection->cid = 0;
	$connection->name = '';
	$connection->last_ping = 0;
	$connection->deadline = Timer::add(5, function()use($connection) // 登录限时5秒，超时断开连接
	{
		Timer::del($connection->deadline);
		$connection->deadline = 0;
		sendMsg($connection, array('action' => 'refuse'));
		$connection->close();
		p("A client timeout logging in. ( IP = $connection->IP )");
	});
	p("A new client has joined. ( IP = $connection->IP )");
};

$worker_tasker->onMessage = function($connection, $data)
{
	$data = json_decode($data, True);
	if ($connection->IP == '127.0.0.1' && isset($data['pass'], $data['task']) && $data['pass'] == sha1(DB_PASS))
	{
		$solution = New Solution($data['task']);
		$solution->push();
		return;
	}
	if ($data == NULL || !isset($data['action']))
	{
		p("Json decoding failed or in bad format. ( cid = $connection->cid, IP = $connection->IP )");
		return;
	}
	switch ($data['action'])
	{
		case 'heartbeat': // 评测端信息更新
			heartbeat($connection, $data);
			break;
		case 'update_state': // 评测中更新状态
			update_state($connection, $data);
			break;
		case 'update': // 评测完更新结果
			update($connection, $data);
			break;
		case 'login': // 评测端登录
			login($connection, $data);
			break;
		default:
			p("Unknown Action ( cid = $connection->cid, IP = $connection->IP )");
	}
};

$worker_tasker->onClose = function($connection)
{
	global $cid;
	if($connection->deadline)
		Timer::del($connection->deadline);
	if (isset($cid[$connection->cid]))
		unset($cid[$connection->cid]);
    p("A client closed the connection. ( cid = $connection->cid, IP = $connection->IP )");
};
