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
		
		add_action( 'loadDone', 'buggy');
	}
	
	function buggy()
	{
		//mo_list_discussions( 1, 1 );
		//mo_log_user( 1, 1, 'adsdfa' );
		//$user->login('moycat', '123456');
		//echo password_hash('123456', PASSWORD_DEFAULT, ['cost' => 5 ] ) . "<br>";
		//echo serialize( $mo_settings );
		//mo_del_user( 21 );
		//mo_add_user('asdf553', 'dsfdffdff', 'g24g234g');
		//$user->refresh_login();
		//mo_set_option( '123', array());
		//$p = mo_list_problems(1, 2, 'qwertyuio');
		//mo_add_new_discussion( 4, 'cha', 'dsfsdfg发发', $pid = 0 );
		//$p = mo_list_solutions(1, 2);
		//var_dump($p);
		//echo mo_get_problem_count('qwertyuio');
		global $mo_request;
		print_r($mo_request);
		echo "\n<br>". mo_time();
	}
