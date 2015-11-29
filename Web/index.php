<?php
	/*
	 * index.php @ MoyOJ
	 * 
	 * This file is the entrance to all non-admin pages.
	 * It do nothing but to require other files.
	 * 
	 */
	
	// Whether to run
	define( 'RUN', True );
	// Whether to output normally.
	define( 'OUTPUT', True );
	
	require( dirname( __FILE__ ). '/mo-loader.php' );
	