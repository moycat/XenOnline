<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

$worker_fetcher->onWorkerStart = function($worker_tasker)
{
	p('The server <Fetcher> has started.');
};

$worker_fetcher->onConnect = function($connection)
{
	$connection->IP = $connection->getRemoteIp();
	if ($connection->IP != '127.0.0.1')
		$connection->close();
	$connection->deadline = Timer::add(3, function()use($connection)
	{
		Timer::del($connection->deadline);
		$connection->deadline = 0;
		$connection->close();
	});
};

$worker_fetcher->onMessage = function($connection, $data)
{
	$data = json_decode($data, True);
	if ($data == NULL || !isset($data['pass'], $data['task']) || $data['pass'] != DB_PASS)
	{
		p("Bad Giving. ( IP = $connection->IP )");
		$connection->close();
		return;
	}
	$solution = New Solution($data);
	$solution->push();
};

$worker_fetcher->onClose = function($connection)
{
	if ($connection->deadline)
		Timer::del($connection->deadline);
};
