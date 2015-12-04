<?php
	/*
	 * mo-includes/function-log.php @ MoyOJ
	 * 
	 * This file provides the functions to write and read logs in the database.
	 * 
	 */
	 
	 /*
	  * 登录mode
	  * 0：密码认证
	  * 1：cookies认证
	  */
	 
	 function mo_log_login( $uid, $mode, $seccess = True )
	 {
		 global $db;
		 $sql = 'INSERT INTO `mo_log_login` (`uid`, `time`, `mode`, `success`, `ip`, `agent`) VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?)';
		 $db->prepare( $sql );
		 $db->bind( 'iiiis', $uid, $mode, $seccess, mo_get_user_ip(), $_SERVER['HTTP_USER_AGENT'] );
		 $db->execute();
		$timestamp = date('Y-m-d G:i:s');
		$sql = 'UPDATE `mo_user` SET `last_time` = ? WHERE `mo_user`.`id` = ?';
		$db->prepare( $sql );
		$db->bind( 'si', $timestamp, $uid );
		$db->execute();
	 }
	 function mo_log_user( $uid, $op, $detail )
	 {
		 global $db;
		 $sql = 'INSERT INTO `mo_log_user` (`uid`, `ip`, `time`, `op`, `detail`) VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?)';
		 $db->prepare( $sql );
		 $db->bind( 'iiis', $uid, mo_get_user_ip(), $op, $detail );
		 $db->execute();
	 }
