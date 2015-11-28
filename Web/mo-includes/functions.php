<?php
	/*
	 * mo-includes/functions.php @ MoyOJ
	 * 
	 * This file provides all basic functions used by MoyOJ.
	 * Others can be found in their related files.
	 * 
	 */
	
	function mo_init()
	{
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
	}
	
	function mo_analyze()
	{
		$request = array();
		if ( !isset( $_GET['r'] ) || !$_GET['r'] )
		{
			$request[] = 'index';
			return $request;
		}
		$arg = explode( '/', $_GET['r'] );
		array_filter( $arg );
		$arg = array_merge( $arg );
		if ( !$arg[0] )
		{
			$request[] = 'index';
			return $request;
		}
		if ( !file_exists( MOINC. 'load-request-'. $arg[0]. '.php' ) )
		{
			$arg[0] = '404';
		}
		// TODO: Request pages of themes and plugins
		return $arg;
	}
	
	function mo_runTime( $p = 3 )
	{
		global $mo_time;
		$t = microtime();
		list( $m0, $s0 ) = explode( ' ', $mo_time );
		list( $m1, $s1 ) = explode( ' ', $t );
		return round( ( $s1 + $m1 - $s0 - $m0 ) * 1000, 3 );
	}
	
	function mo_debugTime()
	{
		return ' Time:'. mo_runTime();
	}
	
	function mo_write_note( $note )
	{
		if ( defined( 'DEBUG' ) && DEBUG == True )
			echo "\n<!-- Note: ". $note. mo_debugTime(). " -->\n";
	}
	
	function mo_get_url()
	{
		$url = MO_URL. '/'. $_SERVER['PHP_SELF'];
		return $url;
	}
