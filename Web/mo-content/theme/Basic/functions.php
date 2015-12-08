<?php
	
	function b_get_user_count()
	{
		global $db;
		$sql = 'SELECT COUNT(*) AS total FROM `mo_user`';
		$db->prepare( $sql );
		$result = $db->execute();
		return (int)$result[0]['total'];
	}