<?php
	
	function b_get_user_count()
	{
		global $db;
		$sql = 'SELECT COUNT(*) AS total FROM `mo_user`';
		$db->prepare( $sql );
		$result = $db->execute();
		return (int)$result[0]['total'];
	}
	
	function b_check_code()
	{
		if ( !isset( $_POST['code'] ) || strlen( $_POST['code'] ) > 102400 || !strlen( $_POST['code'] ) ||
			!is_numeric( $_POST['lang']) || (int)$_POST['lang'] < 1 || $_POST['lang'] > 3 )
		{
			return False;
		}
		else
		{
			return True;
		}
	}