<?php
	/*
	 * mo-includes/function-problem.php @ MoyOJ
	 * 
	 * This file provides the functions of viewing problems, submitting 
	 * a new solution.
	 * 
	 */
	
	function mo_list_problems( $start, $end, $tag = '' )
	{
		global $db;
		$start -= 1;
		$sql = 'SELECT `id`, `title`, `tag`, `extra`, `solved`, `try` FROM `mo_judge_problem` WHERE `state` = 1 ';
		if ( $tag )
		{
			$sql .= "AND (MATCH (tag) AGAINST (?)) LIMIT $start,$end";
			$db->prepare( $sql );
			$db->bind( 's', $tag );
		}
		else
		{
			$sql .= "LIMIT $start,$end";
			$db->prepare( $sql );
		}
		$result = $db->execute();
		return $result;
	}
	
	function mo_list_solutions( $start, $end, $pid = 0, $uid = 0, $state = 'all' )
	{
		
	}
	
	function mo_add_new_solution( $pid, $lang, $post, $uid = 0 )
	{
		global $user;
		if ( !$uid )
		{
			$uid = $user->getUID();
		}
		if ( !( $uid && $pid && $post ) )
		{
			return False;
		}
		global $db;
		$length = strlen( $post );
		$post = base64_encode( $post );
		$client = mo_get_client();
		$sql = 'SELECT `submit`, `try`, `submit_problem` FROM `mo_user_record` WHERE `uid` = ?';
		$db->prepare( $sql );
		$db->bind( 'i', $uid );
		$result = $db->execute();
		$submit_problem = explode( ' ', $result[0]['submit_problem'] );
		if ( !in_array( (string)$pid, $submit_problem ) )
		{
			$result[0]['submit_problem'] .= "$pid ";
			$result[0]['try'] = (int)$result[0]['try'] + 1;
		}
		$result[0]['submit'] = (int)$result[0]['submit'] + 1;
		$sql = 'UPDATE `mo_user_record` SET `submit` = ?, `try` = ?, `submit_problem` = ? WHERE `uid` = ?';
		$db->prepare( $sql );
		$db->bind( 'iisi', $result[0]['submit'], $result[0]['try'], $result[0]['submit_problem'],  $uid );
		$db->execute();
		$sql = 'INSERT INTO `mo_judge_solution` (`pid`, `uid`, `client`, `post_time`, `language`, `code_length`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?)';
		$db->prepare( $sql );
		$db->bind( 'iiiii', $pid, $uid, $client, $lang, $length );
		$db->execute();
		$sid = $db->getInsID();
		$sql = 'INSERT INTO `mo_judge_code` (`sid`, `code`) VALUES (?, ?)';
		$db->prepare( $sql );
		$db->bind( 'is', $sid, $post );
		$db->execute();
	}
	
	function mo_get_client() // TODO
	{
		return 1;
	}
