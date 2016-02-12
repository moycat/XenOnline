<?php
/*
 * mo-includes/function-problem.php @ MoyOJ
 *
 * This file provides the functions of viewing problems, submitting
 * a new solution.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

function mo_list_problems( $start, $end, $tag = '' )
{
	global $db;
	$start -= 1;
	$sql = 'SELECT `id`, `title`, `tag`, `extra`, `solved`, `try` FROM `mo_judge_problem` WHERE `state` = 1 ';
	$piece = $end - $start + 1;
	if ( $tag )
	{
		$sql .= "AND (MATCH ($tag) AGAINST (?)) ORDER BY `id` DESC LIMIT $start,$piece";
		$db->prepare( $sql );
		$db->bind( 's', $tag );
	}
	else
	{
		$sql .= "ORDER BY `id` DESC LIMIT $start,$piece";
		$db->prepare( $sql );
	}
	$result = $db->execute();
	return $result;
}

function mo_list_solutions( $start, $end, $pid = 'all', $uid = 'all', $state = 'all' )
{
	global $db;
	$start -= 1;
	$sql = 'SELECT `id`, `pid`, `uid`, `post_time`, `state`, `language`, `code_length`, `used_time`, `used_memory` FROM `mo_judge_solution` WHERE 1=1 ';
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
		$sql .= " AND `uid` = $state";
	}
	$piece = $end - $start + 1;
	$sql .= " ORDER BY `id` DESC LIMIT $start,$piece";
	$db->prepare( $sql );
	$result = $db->execute();
	return $result;
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
	$sql = 'SELECT `submit`, `try`, `submit_problem` FROM `mo_user_record` WHERE `uid` = ?';
	$db->prepare( $sql );
	$db->bind( 'i', $uid );
	$result = $db->execute();
	$submit_problem = explode( ' ', $result[0]['submit_problem'] );
	if ( !in_array( (string)$pid, $submit_problem ) )
	{
		$result[0]['submit_problem'] .= "$pid ";
		$result[0]['try'] = (int)$result[0]['try'] + 1;
		mo_problem_add_submit( $pid, True );
	}
	else
	{
		mo_problem_add_submit( $pid );
	}
	$result[0]['submit'] = (int)$result[0]['submit'] + 1;
	$sql = 'UPDATE `mo_user_record` SET `submit` = ?, `try` = ?, `submit_problem` = ? WHERE `uid` = ?';
	$db->prepare( $sql );
	$db->bind( 'iisi', $result[0]['submit'], $result[0]['try'], $result[0]['submit_problem'],  $uid );
	$db->execute();
	$sql = 'INSERT INTO `mo_judge_solution` (`pid`, `uid`, `code`, `post_time`, `language`, `code_length`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?)';
	$db->prepare( $sql );
	$db->bind( 'iisii', $pid, $uid, $post, $lang, $length );
	$db->execute();
	$sid = $db->getInsID();
	$data = array('sid' => $sid, 'pid' => $pid, 'uid' => $uid, 'lang' => $lang, 'code' => $post);
	mo_write_note( 'A new solution has been added.' );
	mo_log_user( "User added a new solution (SID = $sid)." );
	socket_push($data);
	return $sid;
}

function socket_push( $data )
{
	if ( !mo_com_socket( $data ) )
	{
		mo_log_user( 'Solution Failed Pushing (SID = '. $data['sid']. ').' );
		return False;
	}
	else
	{
		return True;
	}
}

function mo_problem_add_submit( $pid, $add_try = False )
{
	global $db;
	$sql = 'SELECT `try`, `submit` FROM `mo_judge_problem` WHERE `id` = ?';
	$db->prepare( $sql );
	$db->bind( 'i', $pid );
	$result = $db->execute();
	if ( !$result )
	{
		return False;
	}
	$new_try = $add_try ? (int)$result[0]['try'] + 1 : $add_try;
	$new_submit = (int)$result[0]['submit'] + 1;
	$sql = 'UPDATE `mo_judge_problem` SET `try` = ?, `submit` = ? WHERE `id` = ?';
	$db->prepare( $sql );
	$db->bind( 'iii', $new_try, $new_submit, $pid );
	$db->execute();
	return True;
}
