<?php
use Workerman\Worker;
use Workerman\Lib\Timer;

function heartbeat($connection, $data)
{
	if(!$connection->cid)
		return False;
	if(!isset($data['mem_ratio'], $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15']))
	{
		p("Heartbeat in bad format. ( cid = $connection->cid, IP = $connection->IP )");
		return False;
	}
	global $db;
	$timestamp = date('Y-m-d G:i:s');
	$sql = 'UPDATE `mo_judge_client` SET `load_1` = ?, `load_5` = ?, `load_15` = ?, `memory` = ?, `last_ping` = ? WHERE `id` = ?';
	$db->prepare($sql);
	$db->bind('sssssi', $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15'], $data['mem_ratio'], $timestamp, $connection->cid);
	$db->execute();
	p("Get a heartbeat. ( cid = $connection->cid, IP = $connection->IP )");
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

function p($to_write)
{
	$time = date("Y-m-d H:i:s",time());
	echo "[$time] $to_write\n";
}
