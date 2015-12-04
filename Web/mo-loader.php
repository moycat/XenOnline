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
	else
	{
//		require_once( MOINC. 'setup.php' );
		exit(0);
	}
	
	// Just init
	mo_init();
	
	// Init & Load Basic Settings
	require_once( MOINC. 'load-basic.php' );
	
	loadBasic();
	do_action( 'loadBasic' );
	
	if ( count( $mo_plugin ) )
	{
		foreach ( $mo_plugin as $plugin )
		{
			require_once( $plugin );
		}
	}
	if ( $mo_theme_file )
	{
		require_once( $mo_theme_file );
	}
	
	do_action( 'loadTheme' );
	
//	if ( defined('OUTPUT') && OUTPUT == True && $mo_theme_file )
		//call_user_func( $mo_theme );
	
	do_action( 'loadDone' );
	
	mo_write_note( 'The page has been processed successfully.' );
?>
