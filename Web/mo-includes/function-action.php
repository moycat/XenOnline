<?php
	/*
	 * mo-includes/function-action.php @ MoyOJ
	 * 
	 * This file provides hook functions.
	 * Thanks to WordPress.
	 * 
	 */
	
	$mo_actions = array();
	$mo_actions_sorted = array();
	
	// Add or remove a function in a hook
	function add_action( $hook, $func, $priority = 100, $arg = 0 )
	{
		global $mo_actions;
		$mo_actions[$hook][$priority][$func] = $arg;
		$mo_actions_sorted[$hook] = false;
		mo_write_note( 'Function "'. $func. '" has been added to Hook "'. $hook. '".' );
		return True;
	}
	function add_filter( $hook, $func, $priority = 100, $arg = 1 )
	{
		return add_action( $hook, $func, $priority, $arg );
	}
	function remove_action( $hook, $func, $priority = 100 )
	{
		global $mo_actions;
		if ( isset( $mo_actions[$hook][$priority][$func] ) )
		{
			unset( $mo_actions[$hook][$priority][$func] );
			return True;
		}
		else
		{
			return False;
		}
	}
	function remove_filter( $hook, $func, $priority = 100 )
	{
		return remove_action( $hook, $func, $priority );
	}
	
	// Process all functions on the hook
	function do_action( $hook, $arg = array() )
	{
		global $mo_actions;
		if ( !isset( $mo_actions[$hook] ) )
		{
			return False;
		}
		$rt = array();
		if ( $mo_actions_sorted[$hook] == false )
		{
			ksort( $mo_actions[$hook] );
			$mo_actions_sorted[$hook] = true;
		}
		foreach ( $mo_actions[$hook] as $priority )
		{
			foreach ( $priority as $func => $value )
			{
				if ( isset( $arg[$func] ) && count( $arg[$func] ) == $value )
				{
					$rt[$func] = call_user_func_array( $func, $arg[$func] );
				}
				elseif ( $value == 0 )
				{
					$rt[$func] = call_user_func( $func );
				}
			}
		}
		mo_write_note( 'Hook "'. $hook. '" has been run.' );
		return $rt;
	}
	function apply_filter( $hook, $content, $assist = array() )
	{
		global $mo_actions;
		if ( !isset( $mo_actions[$hook] ) )
		{
			return False;
		}
		if ( $mo_actions_sorted[$hook] == false )
		{
			ksort( $mo_actions[$hook] );
			$mo_actions_sorted[$hook] = true;
		}
		foreach ( $mo_actions[$hook] as $priority )
		{
			foreach ( $priority as $func => $value )
			{
				if ( $value == 1 )
				{
					$content = call_user_func( $func, $content );
				}
				elseif ( isset( $assist[$func] ) && $value == count( $assist[$func] ) + 1 )
				{
					$arg[] = $content;
					$arg = array_merge( $arg, $assist[$func] );
					$content = call_user_func_array( $func , $arg );
				}
			}
		}
		mo_write_note( 'Hook "'. $hook. '" has been run as a filter.' );
		return $content;
	}
