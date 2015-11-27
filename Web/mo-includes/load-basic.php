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
