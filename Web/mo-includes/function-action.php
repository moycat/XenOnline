<?php
	/*
	 * mo-includes/function-action.php @ MoyOJ
	 * 
	 * This file provides hook functions.
	 * Thanks to WordPress.
	 * 
	 */
	
	$mo_actions = array();
	
	// Add a function to a hook
	function add_action( $hook, $func, $priority = 100 )
	{
		global $mo_actions;
		$mo_actions[$hook][$priority][$func] = 1;
		mo_write_note( 'Function "'. $func. '" has been added to Hook "'. $hook. '".' );
		return True;
	}
	
	// Process all functions on the hook
	function do_action( $hook, $arg = array(), $filter = False )
	{
		global $mo_actions;
		if ( $filter )
		{
			foreach ( $mo_actions[$hook] as $priority )
			{
				foreach ( $priority as $func )
				{
					$filter = call_user_func( $func, $arg );
				}
			}
			mo_write_note( 'Hook "'. $hook. '" has been run as a filter.' );
			return $arg;
		}
		if ( !isset( $mo_actions[$hook] ) )
		{
			return False;
		}
		$rt = array();
		ksort( $mo_actions[$hook] );
		foreach ( $mo_actions[$hook] as $priority )
		{
			foreach ( $priority as $func => $value )
			{
				if ( isset( $arg[$func] ) )
				{
					$rt[$func] = call_user_func_array( $func, $arg[$func] );
				}
				else
				{
					$rt[$func] = call_user_func( $func );
				}
			}
		}
		mo_write_note( 'Hook "'. $hook. '" has been run.' );
		return $rt;
	}
