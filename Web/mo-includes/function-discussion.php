<?php
	/*
	 * mo-includes/function-discussion.php @ MoyOJ
	 * 
	 * This file provides the functions of viewing discussions, submitting 
	 * a new discussion.
	 * 
	 */
	
	function mo_list_discussions( $start, $end, $parent = 0, $category = 'all', $uid = 'all', $status = 1 )
	{
		global $db;
		$start -= 1;
		$sql = 'SELECT `id`, `uid`, `parent`, `title`, `status`, `category`, `post_time`, `extra`, `ip` FROM `mo_discussion`  WHERE `parent` = ? AND `status` = ?';
		if ( is_numeric( $category ) )
		{
			$sql .= " `category` = $category";
		}
		if ( is_numeric( $uid ) )
		{
			$sql .= " `uid` = $uid";
		}
		$sql .= " LIMIT $start,$end";
		$db->prepare( $sql );
		$db->bind( 'ii', $parent, $status );
		$result = $db->execute();
		var_dump( $result );
		return $result;
	}
	/*
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
*/
