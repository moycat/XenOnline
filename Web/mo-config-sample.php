<?php
	/*
	 * mo-config.php @ MoyOJ
	 * 
	 * This file gives information to allow MoyOJ to connect to your database.
	 * Also, it sets some simple things needed.
	 * 
	 */
	 
	 // The address of your database server
	 define( 'DB_HOST' , 'the_db_host' );
	 // The name of your database
	 define( 'DB_NAME' , 'the_db_name' );
	 // The username of your databse account
	 define( 'DB_USER' , 'the_db_user' );
	 // The password of the account
	 define( 'DB_PASS' , 'the_db_pass' );
	 
	 // The salt to encrypt some secret data
	 define( 'SALT', 'a_random_salt_the_longger_the_better_well_just_kidding' );
	 
	// If debugging, set it to True to output details.
	define( 'DEBUG', False );