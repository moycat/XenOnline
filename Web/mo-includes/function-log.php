<?php
/*
 * mo-includes/function-log.php @ MoyOJ
 *
 * This file provides the functions to write and read logs in the database.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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
	$ip = mo_get_user_ip();
	$sql = 'UPDATE `mo_user` SET `last_time` = ?, `last_ip` = ? WHERE `id` = ?';
	$db->prepare( $sql );
	$db->bind( 'sii', $timestamp, $ip, $uid );
	$db->execute();
 }

 function mo_log_user( $detail, $uid = 0 )
 {
	 global $db;
	 if ( !$uid )
	 {
		global $user;
		$uid = $user->getUID();
	 }
	 $sql = 'INSERT INTO `mo_log_user` (`uid`, `ip`, `time`, `detail`) VALUES (?, ?, CURRENT_TIMESTAMP, ?)';
	 $db->prepare( $sql );
	 $db->bind( 'iis', $uid, mo_get_user_ip(), $detail );
	 $db->execute();
 }
