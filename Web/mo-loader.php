<?php
	/*
	 * mo-loader.php @ MoyOJ
	 * 
	 * This file sets basic variables and call other files to load the site.
	 * 
	 */
	 
	define( 'ABSPATH', dirname( __FILE__ ). '/' );
	define( 'MOINC', ABSPATH. 'mo-includes/' );
	define( 'MOCON', ABSPATH. 'mo-content/' );
	require_once( MOINC. 'functions.php' );
	if ( file_exists( ABSPATH . 'mo-config.php' ) )
	{
		require_once( 'mo-config.php' );
	}
	// mo-config.php doesn't exist
	else
	{
		require_once( MOINC. 'setup.php' );
		exit(0);
	}
	if ( DEBUG == True )
	{
		error_reporting( E_ALL );
		mo_write_note('DEBUG ENABLED');
	}
	else
	{
		error_reporting( E_ERROR | E_WARNING | E_PARSE );
	}
//	require_once()
?>
