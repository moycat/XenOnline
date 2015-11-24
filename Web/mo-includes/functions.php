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
		$mo_basic = array();
		
		// TODO: Timer
		// TODO: Check if closed
	}
	 
	 function add_action($hook, $func, $arg = 0, $priority = 100)
	 {
		 
	 }
	 
	 function do_action($hook, $arg)
	 {
		 
	 }
	 
	 function mo_write_note($note)
	 {
		if ( defined( 'DEBUG' ) && DEBUG == True )
			echo "\n<!-- Note: ". $note. " -->\n";
	 }
	 
	 function mo_get_url()
	 {
		 $url = MO_URL. '/'. $_SERVER['PHP_SELF'];
		 return $url;
	 }
?>
