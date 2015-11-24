<?php
	/*
	 * mo-includes/class-db.php @ MoyOJ
	 * 
	 * This file provides the classes ralated to database operations.
	 * 
	 */

	class DB
	{
		private $host = 'localhost';
		private $name = 'moyoj';
		private $user = 'moyoj';
		private $pass = 'moyoj';
		
		private $conn;
		
		function init($db_host, $db_name, $db_user, $db_pass)
		{
			$this->host = $db_host;
			$this->name = $db_name;
			$this->user = $db_user;
			$this->pass = $db_pass;
		}
		function connect()
		{
			mysql_connect($this->host, $this->user, $this->pass);
			if ( !$conn )
			{
				die( '<h1>Error Connecting to the database</h1>' );
			}
			mysql_select_db( $this->name );
		}
		function __construct()
		{
			mysql_close($this->conn);
		}
	}

	class Query
	{
		
	}
