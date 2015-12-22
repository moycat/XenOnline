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
	
	// Whether to use memcached
	define( 'MEM', True);
	// The address of your memcached server
	define( 'MEM_HOST' , 'the_mem_addr' );
	// The port of the memcached service
	define( 'MEM_PORT' , 11211 );
	
	// The address of your socket server
	define( 'SOCK_HOST' , 'the_socket_addr' );
	// The port of the memcached service
	define( 'SOCK_PORT' , 6666 );
	
	// The cost used when crypting password
	// At least 4, and 5 is recommond
	define( 'CRYPT_COST', 4 );
	
	// If debugging, set it to True to output details
	define( 'DEBUG', False );
