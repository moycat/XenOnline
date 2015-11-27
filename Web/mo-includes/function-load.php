<?php
	/*
	 * mo-includes/function-load.php @ MoyOJ
	 * 
	 * This file provides the functions to load data from
	 * the database.
	 * 
	 */
	
	function mo_load_settings()
	{
		global $db;
		$sql = 'SELECT * FROM `mo_site_options`';
		$db->prepare( $sql );
		$result = $db->execute();
		$rt = array();
		foreach ( $result as $value )
		{
			$rt[$value['name']] = $value['value'];
		}
		return $rt;
	}
