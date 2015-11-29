<?php
	/*
	 * mo-includes/load-request.php @ MoyOJ
	 * 
	 * This file analyzes the request sent by the user,
	 * and send the request to a specific file to
	 * prepare the data to be shown.
	 *
	 */
	
	$mo_request = NULL;
	
	function loadRequest()
	{
		global $mo_request;
		$mo_request = mo_analyze();
		// TODO: Request pages of themes and plugins
		require_once( MOINC. 'load-request-'. $mo_request[0]. '.php' );
	}
