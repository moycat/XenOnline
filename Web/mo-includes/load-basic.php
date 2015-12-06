<?php
	/*
	 * mo-includes/load.php @ MoyOJ
	 * 
	 * This file load the basic information normally.
	 * Others can be found in their related files.
	 * 
	 */

	require_once( MOINC. 'class-db.php' );
	require_once( MOINC. 'class-discussion.php' );
	require_once( MOINC. 'class-user.php' );
	require_once( MOINC. 'class-problem.php' );
	require_once( MOINC. 'function-action.php' );
	require_once( MOINC. 'function-discussion.php' );
	require_once( MOINC. 'function-data.php' );
	require_once( MOINC. 'function-log.php' );
	require_once( MOINC. 'function-problem.php' );
	require_once( MOINC. 'function-user.php' );
	$db = new DB();
	$user = new User();
	$mo_settings = array();
	$mo_request = '';
	$mo_plugin = array();
	$mo_theme = '';
	$mo_theme_floder = '';
	$mo_theme_file = '';
	
	function loadBasic()
	{
		global $db, $user, $mo_settings, $mo_request;
		
		// To connect to the database
		$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		$db->connect();
		mo_load_settings();
		
		getPT();
		
		// Check if logged in or trying to
		if ( $user->autoLogin() )
		{
			$user->loadAll( $_SESSION['uid'] );
			$user->check();
		}
		
		$mo_request = mo_analyze();
	}