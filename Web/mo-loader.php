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
	
	if ( !file_exists( ABSPATH. 'mo-config.php' ) )
	{
//		require_once( MOINC. 'setup.php' );
		exit(0);
	}
	
	require_once( 'mo-config.php' );
	
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
	
	// Just init
	session_start();
	if ( DEBUG == True )
	{
		error_reporting( E_ALL );
		mo_write_note( 'DEBUG ENABLED' );
	}
	else
	{
		error_reporting( E_ERROR | E_WARNING | E_PARSE );
	}
	
	// Check if closed
	if ( file_exists( MOCON. 'closed.lock' ) )
	{
		die( '<h1>Site Closed Temporarily</h1>' );
	}
	
	if ( defined( 'MEM' ) && MEM == True )
	{
		$mem = new Memcached( 'moyoj' );
		$mem->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
		if ( !count( $mem->getServerList() ) )
		{
			$mem->addServer(MEM_HOST, MEM_PORT);
		}
	}
	$db = new DB();
	$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	$db->connect();
	
	$user = new User();
	$mo_settings = array();
	$mo_request = '';
	$mo_plugin = array();
	$mo_theme = '';
	$mo_theme_floder = '';
	$mo_theme_file = '';
	
	mo_load_settings();
	$mo_request = mo_analyze();
	
	getPT();
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
	do_action( 'loadPT' );
	
	// Check if logged in or trying to
	if ( $user->autoLogin() )
	{
		$user->loadAll( $_SESSION['uid'] );
		$user->check();
	}
	
	do_action( 'loadBasic' );
	
	if ( defined('OUTPUT') && OUTPUT == True && $mo_theme_file )
	{
		call_user_func( $mo_theme );
	}
	
	do_action( 'loadDone' );
	
	mo_write_note( 'The page has been processed successfully.' );
?>
