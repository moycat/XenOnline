<?php
	/*
	 * mo-api/client.php @ MoyOJ
	 * 
	 * This file provides apis of communication with the
	 * database through http.
	 * 
	 */
	
	define( 'RUN', True );
	
	require_once( '../mo-includes/functions.php' );
	require_once( '../mo-includes/class-db.php' );
	require_once( '../mo-config.php' );
	
	function heart_beat()
	{
		global $db;
		
	}
	
	function get_data()
	{
		global $db;
		
	}
	
	function update_data()
	{
		global $db;
		
	}
	
	function check( $cid, $hash )
	{
		global $db;
		
	}
	
	$db = new DB();
	$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$db->connect();
	
	$cid = isset( $_GET['cid'] ) ? $_GET['cid'] : die();
	$hash = isset( $_GET['hash'] ) ? $_GET['hash'] : die();
	$op = isset( $_GET['op'] ) ? $_GET['op'] : die();
	
	if ( !is_numeric( $cid ) || count( $hash ) != 40 || !$op || !check( $cid, $hash ) )
	{
		exit(0);
	}
	
	switch ( $op )
	{
		case 'heartbeat':
		
		break;
		case 'getdata':
		
		break;
		case 'update':
		
		break;
	}