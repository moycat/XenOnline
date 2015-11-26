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
		
		function init( $db_host, $db_user, $db_pass, $db_name )
		{
			$this->host = $db_host;
			$this->name = $db_name;
			$this->user = $db_user;
			$this->pass = $db_pass;
		}
		function connect()
		{
			$this->conn = mysqli_connect( $this->host, $this->user,
								$this->pass, $this->name );
			if ( !$this->conn )
			{
				die( '<h1>Error Connecting to the Database</h1>' );
			}
		}
		function getConn()
		{
			return $this->conn;
		}
	}

	class Query
	{
		private $sql;
		
		function __construct( $query )
		{
			$this->sql = $query;
		}
	}
