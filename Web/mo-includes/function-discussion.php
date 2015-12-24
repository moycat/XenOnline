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
			$sql .= " AND `category` = $category";
		}
		if ( is_numeric( $uid ) )
		{
			$sql .= " AND `uid` = $uid";
		}
		$piece = $end - $start + 1;
		$sql .= " ORDER BY `id` DESC LIMIT $start,$piece";
		$db->prepare( $sql );
		$db->bind( 'ii', $parent, $status );
		$result = $db->execute();
		return $result;
	}
	function mo_add_new_discussion( $category, $title, $content, $parent = 0, $uid = 0, $extra = array() )
	{
		global $user;
		if ( !$uid )
		{
			$uid = $user->getUID();
		}
		if ( !( $uid && ( $parent || $title ) && $content ) )
		{
			return False;
		}
		global $db;
		$sql = 'INSERT INTO `mo_discussion` (`uid`, `parent`, `title`, `category`, `content`, `post_time`, `extra`, `ip`) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?)';
		$db->prepare( $sql );
		$db->bind( 'iisissi', $uid, $parent, $title, $category, $content, serialize($extra), mo_get_user_ip() );
		$db->execute();
		$did = $db->getInsID();
		mo_write_note( 'A new discussion has been added.' );
		mo_log_user( "User added a new discussion (DID = $did)." );
		return $did;
	}
