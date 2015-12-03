<?php
	/*
	 * mo-includes/function-problem.php @ MoyOJ
	 * 
	 * This file provides the functions of viewing problems, submitting 
	 * a new solution and getting the status of a solution;
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
