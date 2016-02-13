<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

$worker_tasker->onWorkerStart = function($worker_tasker)
{
	global $db, $mem;
	$db = new DB();
	$db->init(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	while (!$db->connect())
	{
		sleep(1);
	}
	if (MEM)
	{
		$mem = new Memcached;
		$mem->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
	}
	Timer::add(5, 'check_lost'); // 每5秒，检查无响应的评测请求
	Timer::add(20, 'check_forgotten'); // 每20秒，在数据库中寻找丢失的请求
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
		if (!isset($data['task']['action']))
		{
			$solution = New Solution($data['task']);
			$solution->push();
			return;
		}
		else
		{
			switch ($data['task']['action'])
			{
				case 'kill':
					kill_client($data['task']['cid']);
					break;
			}
		}
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
	global $cid, $ava_client, $client_sorted;
	if ($connection->deadline)
	{
		Timer::del($connection->deadline);
	}
	if (isset($cid[$connection->cid]))
	{
		unset($cid[$connection->cid]);
		$client_sorted = False;
	}
    p("A client closed the connection. ( cid = $connection->cid, IP = $connection->IP )");
};
