<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

function heartbeat($connection, $data)
{
	if(!$connection->cid || !isset($data['mem_ratio'], $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15'], $data['timestamp']))
	{
		p("Bad heartbeat. ( cid = $connection->cid, IP = $connection->IP )");
		return False;
	}
	$connection->last_ping = (int)$data['timestamp'];
	global $db;
	$timestamp = date('Y-m-d G:i:s');
	$sql = 'UPDATE `mo_judge_client` SET `load_1` = ?, `load_5` = ?, `load_15` = ?, `memory` = ?, `last_ping` = ? WHERE `id` = ?';
	$db->prepare($sql);
	$db->bind('sssssi', $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15'], $data['mem_ratio'], $timestamp, $connection->cid);
	$db->execute();
	p("Get a heartbeat. ( cid = $connection->cid, IP = $connection->IP )");
	
	debuggy();
	
	return True;
}

function update_state($connection, $data)
{
	global $db, $task;
	if (!$connection->cid || !isset($data['timestamp'], $data['sid'], $data['state']))
		return False;
	$sid = (int)$data['sid'];
	if (!isset($task[$sid]) || $task[$sid]->cid != $connection->cid || $task[$sid]->last_time > (int)$data['timestamp'])
		return False;
	$task[$sid]->last_time = (int)$data['timestamp'];
	if (MEM)
	{
		set('solution-state-'. $sid, $data['state']);
	}
	else
	{
		$sql = 'UPDATE `mo_judge_solution` SET `state` = ? WHERE `mo_judge_solution`.`id` = ?';
		$db->prepare($sql);
		$db->bind('ii', $data['state'], $data['sid']);
		$db->execute();
	}
	return True;
}

function login($connection, $data)
{
	global $db, $cid;
	if (!isset($data['client_id'], $data['client_hash'] ) || isset($cid[$data['client_id']]) || $connection->cid)
	{
		p("Bad Login Action ( IP = $connection->IP )");
		cut($connection, 'refuse');
		return False;
	}
	$sql = 'SELECT name FROM mo_judge_client WHERE id = ? AND hash = ?';
	$db->prepare($sql);
	$db->bind('is', $data['client_id'], $data['client_hash']);
	$result = $db->execute();
	if (!$result)
	{
		p("Bad Client ID or Hash ( IP = $connection->IP )");
		cut($connection, 'refuse');
		return False;
	}
	Timer::del($connection->deadline);
	$connection->deadline = 0;
	$connection->cid = (string)$data['client_id'];
	$connection->name = $result[0]['name'];
	$cid[$connection->cid] = $connection;
	sendMsg($connection, array('action' => 'admit', 'client_name' => $result[0]['name']));
	p("The client <$connection->name> has joined. ( cid = $connection->cid, IP = $connection->IP )");
	return True;
}

function sendMsg(&$connection, $msg)
{
	$msg = json_encode($msg)."\n";
	$connection->send($msg);
};

function cut(&$connection, $reason)
{
	sendMsg($connection, array('action' => $reason));
	$connection->close();
}

function check_lost()
{
	global $task;
	foreach ($task as $now)
		if (time() - $now->last_time > (5 + $now->got * 55))
			$now->push();
}

function get($key)
{
	if (!MEM)
		return False;
	global $mem;
	return $mem->get($key);
}

function set($key, $data)
{
	if (!MEM)
		return False;
	global $mem;
	if (!$mem->set($key, $data))
		$mem->replace($key, $data);
	return True;
}

function del($key)
{
	if (!MEM)
		return False;
	global $mem;
	return $mem->delete($key);
}

function p($to_write)
{
	$time = date("Y-m-d H:i:s",time());
	echo "[$time] $to_write\n";
}
