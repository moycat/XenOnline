<?php
	/*
	 * mo-includes/load.php @ MoyOJ
	 * 
	 * This file load the basic information normally.
	 * Others can be found in their related files.
	 * 
	 */

	require_once( MOINC. 'class-db.php' );
	require_once( MOINC. 'class-user.php' );
	$db = new DB();
	$user = new User();
	$mo_settings = array();
	
	// TODO: Load plugin hooks
	// TODO: Load theme hooks
	
	function loadBasic()
	{
		global $db, $user, $mo_settings;
		
		// To connect to the database
		$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		$db->connect();
		$mo_settings = mo_load_settings();
		
		if ( $user->autoLogin() )
		{
			$user->loadAll( $_SESSION['uid'] );
		}
		
		
		
		$_POST['auto_login'] = 1;
		//$user->login('moycat', '123456');
		//var_dump($user);
		//echo password_hash('123456', PASSWORD_DEFAULT, ['cost' => 5 ] ) . "<br>";
		//echo serialize( $mo_settings );
		echo mo_time();

	}
