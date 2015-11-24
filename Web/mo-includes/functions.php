<?php
	/*
	 * mo-includes/functions.php @ MoyOJ
	 * 
	 * This file provides all basic functions used by MoyOJ.
	 * Others can be found in their related files.
	 * 
	 */
	 
	 function add_action($hook, $func, $priority = 100)
	 {
		 
	 }
	 
	 function do_action($hook, $arg)
	 {
		 
	 }
	 
	 function mo_write_note($note)
	 {
		echo "\n<!-- Note: ". $note. " -->\n";
	 }
	 
	 function mo_get_url()
	 {
		 $url = MO_URL. '/'. $_SERVER['PHP_SELF'];
		 return $url;
	 }
?>
