<?php
/*
 * mo-includes/function-action.php @ MoyOJ
 *
 * This file provides hook functions.
 * Thanks to WordPress.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

$mo_actions = array();
$mo_actions_sorted = array();

// Add or remove a function in a hook
function add_action( $hook, $func, $priority = 100, $arg = 0 )
{
	global $mo_actions, $mo_actions_sorted;
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
function do_action( $hook, $arg = '' )
{
	global $mo_actions, $mo_actions_sorted;
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
	$all_args = func_get_args();
	foreach ( $mo_actions[$hook] as $priority )
	{
		foreach ( $priority as $func => $value )
		{
				$rt[$func] = call_user_func_array( $func, array_slice( $all_args, 1, $value ) );
		}
	}
	mo_write_note( 'Hook "'. $hook. '" has been run.' );
	return $rt;
}

function apply_filter( $hook, $content )
{
	global $mo_actions, $mo_actions_sorted;
	if ( !isset( $mo_actions[$hook] ) )
	{
		return $content;
	}
	if ( $mo_actions_sorted[$hook] == false )
	{
		ksort( $mo_actions[$hook] );
		$mo_actions_sorted[$hook] = true;
	}
	$all_args = func_get_args();
	foreach ( $mo_actions[$hook] as $priority )
	{
		foreach ( $priority as $func => $value )
		{
				$arg[] = $content;
				if ( $value > 1 )
				{
					$arg = array_merge( $arg, array_slice( $all_args, 2, $value ) );
				}
				$content = call_user_func_array( $func , $arg );
		}
	}
	mo_write_note( 'Hook "'. $hook. '" has been run as a filter.' );
	return $content;
}
