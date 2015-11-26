<?php
	/*
	 * mo-includes/functions.php @ MoyOJ
	 * 
	 * This file provides all basic functions used by MoyOJ.
	 * Others can be found in their related files.
	 * 
	 */
	
	function init()
	{
		if ( DEBUG == True )
		{
			error_reporting( E_ALL );
			mo_write_note('DEBUG ENABLED');
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
	 
	function add_action( $hook, $func, $priority = 100, $arg )
	{
		 
	}
	
	function do_action( $hook, $arg )
	{
		 
	}
	
	function mo_time( $p = 3 )
	{
		global $time;
		$t = microtime();
		list( $m0, $s0 ) = explode( ' ', $time );
		list( $m1, $s1 ) = explode( ' ', $t );
		return round( ( $s1 + $m1 - $s0 - $m0 ) * 1000, 3 );
	}
	
	function mo_write_note( $note )
	{
		if ( defined( 'DEBUG' ) && DEBUG == True )
			echo "\n<!-- Note: ". $note. " -->\n";
	}
	
	function mo_get_url()
	{
		$url = MO_URL. '/'. $_SERVER['PHP_SELF'];
		return $url;
	}
