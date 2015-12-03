<?php
	/*
	 * mo-includes/function-data.php @ MoyOJ
	 * 
	 * This file provides functions of operating the database.
	 * 
	 */
	
	function mo_load_settings()
	{
		$settings = mo_read_cache( 'mo_cache_settings' );
		if ( !$settings )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_site_options`';
			$db->prepare( $sql );
			$result = $db->execute();
			foreach ( $result as $value )
			{
				$settings[$value['item']] = $value['value'];
			}
			mo_write_cache( 'mo_cache_settings', $settings );
		}
		mo_write_note( 'Site settings have been loaded.' );
		return $settings;
	}