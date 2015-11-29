<?php
	/*
	 * mo-includes/load.php @ MoyOJ
	 * 
	 * This file load the basic information normally.
	 * Others can be found in their related files.
	 * 
	 */

	require_once( MOINC. 'class-db.php' );
	// To connect to the database
	$db = new DB();
	$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$db->connect();
	$mo_settings = mo_load_settings();
	
	// TODO: Load plugin hooks
	// TODO: Load theme hooks
	
	$user = new User();
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
