<?php
	/*
	 * mo-includes/function-data.php @ MoyOJ
	 * 
	 * This file provides functions of operating the database.
	 * 
	 */
	
	function mo_load_settings()
	{
		global $mo_settings;
		$mo_settings = mo_read_cache( 'mo_cache_settings' );
		if ( !$mo_settings )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_site_options`';
			$db->prepare( $sql );
			$result = $db->execute();
			foreach ( $result as $value )
			{
				$mo_settings[$value['item']] = $value['value'];
			}
			mo_write_cache( 'mo_cache_settings', $mo_settings );
		}
		mo_write_note( 'Site settings have been loaded.' );
		return $mo_settings;
	}
	
	function mo_get_option( $option )
	{
		global $mo_settings;
		if ( isset( $mo_settings[$option] ) )
		{
			if ( is_serialized( $mo_settings[$option] ) )
			{
				$mo_settings[$option] = unserialize( $mo_settings[$option] );
			}
			return $mo_settings[$option];
		}
		else
		{
			return NULL;
		}
	}
	function mo_set_option( $option, $data )
	{
		global $mo_settings, $db;
		if ( is_array( $data ) || is_object( $data ) )
		{
			$to_write = serialize( $data );
		}
		else
		{
			$to_write = $data;
		}
		if ( isset( $mo_settings[$option] ) )
		{
			$rt = $mo_settings[$option];
			$mo_settings[$option] = $data;
			$sql = 'UPDATE `mo_site_options` SET `value` = ? WHERE `item` = ?';
			$db->prepare( $sql );
			$db->bind( 'ss', $to_write, $option );
		}
		else
		{
			$rt = True;
			$mo_settings[$option] = $data;
			$sql = 'INSERT INTO `mo_site_options` (`item`, `value`) VALUES (?, ?)';
			$db->prepare( $sql );
			$db->bind( 'ss', $option, $to_write );
		}
		$db->execute();
		mo_write_cache( 'mo_cache_settings', $mo_settings );
		mo_write_note( "Site option: '$option' has been update." );
		return $rt;
	}
	
	function mo_get_solution_count( $pid = 'all', $uid = 'all', $state = 'all' )
	{
		$count = mo_read_cache( 'mo_solution_count' );
		if ( !$count )
		{
			global $db;
			$sql = 'SELECT COUNT(*) AS total FROM mo_judge_solution WHERE 1=1';
			if ( is_numeric( $pid ) )
			{
				$sql .= " AND `pid` = $pid";
			}
			if ( is_numeric( $uid ) )
			{
				$sql .= " AND `uid` = $uid";
			}
			if ( is_numeric( $state ) )
			{
				$sql .= " AND `state` = $state";
			}
			$db->prepare( $sql );
			$result = $db->execute();
			$count = (int)$result[0]['total'];
			mo_write_cache( 'mo_solution_count', $count );
		}
		return $count;
	}
	function mo_get_problem_count( $tag = '' )
	{
		$count = mo_read_cache( 'mo_problem_count_tag'. $tag );
		if ( !$count )
		{
			global $db;
			$sql = 'SELECT COUNT(*) AS total FROM `mo_judge_problem` WHERE `state` = 1';
			if ( $tag )
			{
				$sql .= ' AND (MATCH (tag) AGAINST (?))';
				$db->prepare( $sql );
				$db->bind( 's', $tag );
			}
			else
			{
				$db->prepare( $sql );
			}
			$result = $db->execute();
			$count = (int)$result[0]['total'];
			mo_write_cache( 'mo_problem_count_tag'. $tag, $count );
		}
		return $count;
	}
	function mo_get_discussion_count( $parent = 0, $category = 'all', $uid = 'all', $status = 1 )
	{
		$count = mo_read_cache( "mo_discussion_count_p$parent_c$category_u$uid_s$status" );
		if ( !$count )
		{
			global $db;
			$start -= 1;
			$sql = 'SELECT COUNT(*) AS total FROM `mo_discussion`  WHERE `parent` = ? AND `status` = ?';
			if ( is_numeric( $category ) )
			{
				$sql .= " AND `category` = $category";
			}
			if ( is_numeric( $uid ) )
			{
				$sql .= " AND `uid` = $uid";
			}
			$db->prepare( $sql );
			$db->bind( 'ii', $parent, $status );
			$result = $db->execute();
			$count = (int)$result[0]['total'];
			mo_write_cache( "mo_discussion_count_p$parent_c$category_u$uid_s$status", $count );
		}
		return $count;
	}
	function mo_get_user_count()
	{
		$count = mo_read_cache( 'mo_user_count' );
		if ( !$count )
		{
			global $db;
			$sql = 'SELECT COUNT(*) AS total FROM `mo_user`';
			$db->prepare( $sql );
			$result = $db->execute();
			$count = (int)$result[0]['total'];
			mo_write_cache( 'mo_user_count', $count );
		}
		return $count;
	}
