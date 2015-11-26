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
	$db->init( DB_HOST, DB_NAME, DB_USER, DB_PASS );
	$db->connect();
