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
		
		private $mysqli;
		private $query;
		
		function init( $db_host, $db_user, $db_pass, $db_name )
		{
			$this->host = $db_host;
			$this->name = $db_name;
			$this->user = $db_user;
			$this->pass = $db_pass;
		}
		function connect()
		{
			$this->mysqli = new mysqli( $this->host, $this->user,
								$this->pass, $this->name );
			if ( mysqli_connect_errno() )
			{
				die( '<h1>Error Connecting to the Database</h1>' );
			}
			$this->mysqli->set_charset( 'utf8' );
		}
		function prepare( $sql )
		{
			$this->query = $this->mysqli->prepare( $sql );
		}
		function bind()
		{
			$input = func_get_args();
			$cnt = count( $input );
			if ( $cnt < 2 )
			{
				return;
			}
			for( $i = 1; $i < $cnt; ++$i )
			{
				$input[$i] = &$input[$i];
			}
			call_user_func_array( array($this->query, 'bind_param'), $input );
		}
		function execute()
		{
			$this->query->execute();
			$result = array();
			$meta = $this->query->result_metadata();   
			while ($field = $meta->fetch_field())
			{
				$params[] = &$row[$field->name];
			}
			call_user_func_array(array($this->query, 'bind_result'), $params);
			while ($this->query->fetch())
			{
				foreach($row as $key => $val)
				{
					$c[$key] = $val;
				}
				$result[] = $c;
			}
			$this->query->close();
			return $result;
		}
	}