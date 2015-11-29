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
	define( 'MOCACHE', MOCON. 'cache/' );
	require_once( MOINC. 'functions.php' );
	
	mo_in_check();
	
	if ( file_exists( ABSPATH. 'mo-config.php' ) )
	{
		require_once( 'mo-config.php' );
	}
	// mo-config.php doesn't exist
	else
	{
//		require_once( MOINC. 'setup.php' );
		exit(0);
	}
	
	$mo_time = microtime();
	$mo_settings = array();
	$mo_actions = array();
	
	require_once( MOINC. 'function-action.php' );
	require_once( MOINC. 'function-load.php' );
	
	// Just init
	mo_init();
	
	// Init & Load Basic Settings
	require_once( MOINC. 'load-basic.php' );
	// Process POST Requests & Load Data of Requests
	require_once( MOINC. 'load-request.php' );
	// Load Theme & Output
//	if ( defined('OUTPUT') && OUTPUT == True )
//		require_once( MOINC. 'load-theme.php' );
	
	loadBasic();
	loadRequest();
//	loadTheme();
	
	mo_write_note( 'The page has been processed successfully.' );
?>
