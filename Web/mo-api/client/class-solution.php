<?php

class Solution
{
	public $sid;
	public $cid = -1;
	public $pid;
	public $uid;
	public $turn = -1;
	public $version;
	
	public $last_time = 0;
	public $got = 0;
	public $state = 0;
	public $send = array('action' => 'judge');
	
	public function __construct($data)
	{
		global $db, $task;
		if (!isset($data['sid'], $data['pid'], $data['uid'], $data['lang'], $data['code']))
		{
			p("Get a bad solution.");
			return;
		}
		list($this->sid, $this->pid, $this->uid, $this->send['lang'], $this->send['code']) = array((int)$data['sid'], (int)$data['pid'], $data['uid'], $data['lang'], $data['code']);
		$result = get_prob($this->pid);
		list($this->send['sid'], $this->send['pid'], $this->send['version'], $this->send['hash'], $this->send['time_limit'], $this->send['memory_limit'], $this->send['test_turn']) = 
				array($this->sid, $this->pid, $result[0]['ver'], $result[0]['hash'], $result[0]['time_limit'], $result[0]['memory_limit'], $result[0]['test_turn']);
		$task[$this->sid] = &$this;
		p("Get a new solution! ( sid = $this->sid )");
	}
	
	public function push()
	{
		global $worker_tasker, $cid;
		global $ava_client, $client_sorted;
		if (!$this->sid || time() - $this->last_time < 60)
		{
			return False;
		}
		$this->last_time = time();
		if (!$client_sorted)
		{
			$to_choose_from = array();
			$client_count = 0;
			foreach ($cid as $now_client)
			{
				if ($now_client->cid)
				{
					$to_choose_from[] = $now_client;
					$client_count++;
				}
			}
			if (!$client_count)
			{
				$this->cid = -1;
				return False;
			}
			$client_sorted = True;
			$ava_client = $to_choose_from;
		}
		else
		{
			$to_choose_from = $ava_client;
			$client_count = count($ava_client);
		}
		if ($this->cid == -1)
		{
			$turn = $this->sid % $client_count;
		}
		else
		{
			$turn = ($this->turn + 1) % $client_count;
		}
		$this->turn = $turn;
		$this->cid = $to_choose_from[$turn]->cid;
		sendMsg($to_choose_from[$turn], $this->send);
		p("The solution ( sid = $this->sid ) was sent to the client ( cid = $this->cid )");
		return True;
	}
}
