<?php
	/*
	 * mo-loader.php @ MoyOJ
	 * 
	 * This file sets basic variables and call other files to load the site.
	 * It also check if the site has been installed.
	 * 
	 */
	
	define( 'ABSPATH', dirname( __FILE__ ). '/' );
	define( 'MOINC', ABSPATH. 'mo-includes/' );
	define( 'MOCON', ABSPATH. 'mo-content/' );
	require_once( MOINC. 'functions.php' );
	require_once( MOINC. 'class-basic.php' );
	
	if ( file_exists( ABSPATH. 'mo-config.php' ) )
	{
		require_once( 'mo-config.php' );
	}
	// mo-config.php doesn't exist
	else
	{
		require_once( MOINC. 'setup.php' );
		exit(0);
	}
	
	$setting = array();
	$action = array();
	$time = microtime();
	
	// Just init
	init();
	
	// TODO: Load plugin hooks
	// TODO: Load theme hooks
	
	// Init & Load Basic Settings
	require_once( MOINC. 'load-basic.php' );
	// Load Data of Requests
	//require_once( MOINC. 'load-request.php' );
	// Load Theme & Output
	//require_once( MOINC. 'load-theme.php' );
	
	
	mo_write_note('The page has been processed successfully.');
?>
