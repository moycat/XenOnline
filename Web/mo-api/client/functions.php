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
	$mark = $db->prepare($sql);
	$db->bind($mark, 'sssssi', $data['loadavg']['lavg_1'], $data['loadavg']['lavg_5'], $data['loadavg']['lavg_15'], $data['mem_ratio'], $timestamp, $connection->cid);
	$db->execute($mark);
	p("Get a heartbeat. ( cid = $connection->cid, IP = $connection->IP )");
	return True;
}

function update($connection, $data)
{
	global $db, $task;
	if (!$connection->cid || !isset($data['state'], $data['used_time'], $data['used_memory'], $data['detail'], $data['detail_result'], 
			$data['detail_time'], $data['detail_memory'], $data['sid']))
	{
		return False;
	}
	$sid = (int)$data['sid'];
	if (!isset($task[$sid]) || $task[$sid]->cid != $connection->cid)
	{
		p("Bad update. ( cid = $connection->cid, IP = $connection->IP )");
		return False;
	}
	$sql = 'UPDATE `mo_judge_solution` SET `client` = ?, `state` = ?, `used_time` = ?, `used_memory` = ?, `detail` = ?, '.
				'`detail_result` = ?, `detail_time` = ?, `detail_memory` = ? WHERE `id` = ?';
	$mark = $db->prepare($sql);
	$db->bind($mark, 'iiiissssi',$connection->cid, $data['state'], $data['used_time'], $data['used_memory'], $data['detail'], $data['detail_result'], 
						$data['detail_time'], $data['detail_memory'], $data['sid']);
	$db->execute($mark);
	$uid = (int)$task[$sid]->uid;
	$pid = (int)$task[$sid]->pid;
	if ((int)$data['state'] == 10)
	{
		$sql = 'SELECT `ac`, `solved` FROM `mo_judge_problem` WHERE `id` = ?';
		$mark = $db->prepare($sql);
		$db->bind($mark, 'i', $pid);
		$prob = $db->execute($mark);
		$prob_accept = (int)$prob[0]['ac'] + 1;
		$prob_solved = (int)$prob[0]['solved'];
		$sql = 'SELECT `ac_problem`, `accept`, `solve` FROM `mo_user_record` WHERE `uid` = ?';
		$mark = $db->prepare($sql);
		$db->bind($mark, 'i', $uid);
		$user = $db->execute($mark);
		$user_ac = $user[0]['ac_problem'];
		$user_accept = (int)$user[0]['accept'] + 1;
		$user_solve = (int)$user[0]['solve'];
		$tmp = explode(' ', $user_ac);
		if (!in_array((string)$pid, $tmp))
		{
			$user_ac .= "$pid ";
			$prob_solved++;
			$user_solve++;
		}
		$sql = 'UPDATE `mo_judge_problem` SET `solved` = ?, `ac` = ? WHERE `id` = ?';
		$mark = $db->prepare($sql);
		$db->bind($mark, 'iii', $prob_solved, $prob_accept, $pid);
		$db->execute($mark);
		$sql = 'UPDATE `mo_user_record` SET `ac_problem` = ?, `accept` = ?, `solve` = ? WHERE `uid` = ?';
		$mark = $db->prepare($sql);
		$db->bind($mark, 'siii', $user_ac, $user_accept, $user_solve, $uid);
		$db->execute($mark);
	}
	p("Get a update. The solution is done. ( sid = $sid, cid = $connection->cid, IP = $connection->IP )");
	unset($task[$sid]);
	return True;
}

function update_state($connection, $data)
{
	global $db, $task;
	if (!$connection->cid || !isset($data['timestamp'], $data['sid'], $data['state']))
		return False;
	$sid = (int)$data['sid'];
	if (!isset($task[$sid]) || $task[$sid]->cid != $connection->cid || $task[$sid]->state < (int)$data['state'])
	{
		p("Bad update-state. ( cid = $connection->cid, IP = $connection->IP )");
		return False;
	}
	$task[$sid]->last_time = (int)$data['timestamp'];
	$task[$sid]->got = 1;
	$task[$sid]->state = (int)$data['state'];
	if (MEM)
	{
		set('solution-state-'. $sid, $data['state']);
	}
	else
	{
		$sql = 'UPDATE `mo_judge_solution` SET `state` = ? WHERE `mo_judge_solution`.`id` = ?';
		$mark = $db->prepare($sql);
		$db->bind($mark, 'ii', $data['state'], $data['sid']);
		$db->execute($mark);
	}
	p("Get a update-state. ( sid = $sid, cid = $connection->cid, IP = $connection->IP )");
	return True;
}

function login($connection, $data)
{
	global $db, $cid, $ava_client, $client_sorted;
	if (!isset($data['client_id'], $data['client_hash'] ) || $connection->cid)
	{
		p("Bad Login Action ( IP = $connection->IP )");
		cut($connection, 'refuse');
		return False;
	}
	$sql = 'SELECT name FROM mo_judge_client WHERE id = ? AND hash = ?';
	$mark = $db->prepare($sql);
	$db->bind($mark, 'is', $data['client_id'], $data['client_hash']);
	$result = $db->execute($mark);
	if (!$result)
	{
		p("Bad Client ID or Hash ( IP = $connection->IP )");
		cut($connection, 'refuse');
		return False;
	}
	Timer::del($connection->deadline);
	$connection->deadline = 0;
	if (isset($cid[$data['client_id']]))
	{
		cut($cid[$data['client_id']], 'another');
		unset($cid[$data['client_id']]);
	}
	$connection->cid = (string)$data['client_id'];
	$connection->name = $result[0]['name'];
	$cid[$connection->cid] = $connection;
	sendMsg($connection, array('action' => 'admit', 'client_name' => $result[0]['name']));
	$client_sorted = False;
	p("The client <$connection->name> has joined. ( cid = $connection->cid, IP = $connection->IP )");
	return True;
}

function sendMsg(&$connection, $msg)
{
	$msg = json_encode($msg)."\n";
	$connection->send($msg);
};

function kill_client($client)
{
	global $cid;
	$client = (string)$client;
	if (!isset($cid[$client]))
	{
		return False;
	}
	cut($cid[$client], 'refuse');
}

function cut(&$connection, $reason)
{
	sendMsg($connection, array('action' => $reason));
	$connection->close();
}

function check_forgotten()
{
	global $db, $task;
	$sql = 'SELECT `id`, `pid`, `uid`, `code`, `state`, `language` FROM `mo_judge_solution` WHERE `state` = 0';
	$mark = $db->prepare($sql);
	$result = $db->execute($mark);
	if (!count($result))
	{
		return 0;
	}
	foreach ($result as $solution)
	{
		if (!isset($task[(int)$solution['id']]))
		{
			$data = array('sid' => $solution['id'], 'pid' => $solution['pid'], 'uid' => $solution['uid'], 'lang' => $solution['language'], 'code' => $solution['code']);
			$new_solution = new Solution($data);
			$new_solution->push();
		}
	}
	return True;
}

function check_lost()
{
	global $task;
	foreach ($task as $now)
	{
		if (time() - $now->last_time > (5 + $now->got * 55))
		{
			$now->push();
		}
	}
}

function get_prob($sid)
{
	global $db;
	$result = get('client-problem-'. $sid);
	if (!$result)
	{
		$sql = 'SELECT `id`, `hash`, `ver`, `time_limit`, `memory_limit`, `test_turn` FROM `mo_judge_problem` WHERE `id` = ?';
		$mark = $db->prepare($sql);
		$db->bind($mark, 'i', $sid);
		$result = $db->execute($mark);
		set('client-problem-'. $sid, $result);
	}
	return $result;
}

function get($key)
{
	if (!MEM)
	{
		return False;
	}
	global $mem;
	return $mem->get($key);
}

function set($key, $data)
{
	if (!MEM)
	{
		return False;
	}
	global $mem;
	if (!$mem->set($key, $data))
	{
		$mem->replace($key, $data);
	}
	return True;
}

function del($key)
{
	if (!MEM)
	{
		return False;
	}
	global $mem;
	return $mem->delete($key);
}

function p($to_write)
{
	$time = date("Y-m-d H:i:s",time());
	echo "[$time] $to_write\n";
}
