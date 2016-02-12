<?php
/*
 * mo-includes/class-db.php @ MoyOJ
 *
 * This file provides the classes ralated to database operations.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

class DB
{
	private $host = '127.0.0.1';
	private $name = 'moyoj';
	private $user = 'moyoj';
	private $pass = 'moyoj';

	private $mysqli;
	private $query;
	private $insID;

	private $count = 0;

	// Get the information of the database
	function init( $db_host, $db_user, $db_pass, $db_name )
	{
		$this->host = $db_host;
		$this->name = $db_name;
		$this->user = $db_user;
		$this->pass = $db_pass;
	}

	// Connect to the database with a persistent connection
	function connect()
	{
		$this->mysqli = new mysqli( 'p:'. $this->host, $this->user,
							$this->pass, $this->name );
		if ( mysqli_connect_errno() )
		{
			die( '<h1>Error Connecting to the Database</h1>' );
		}
		$this->mysqli->set_charset( 'utf8' );
		mo_write_note( 'Connected to the database successfully.' );
	}

	function prepare( $sql )
	{
		$this->query = $this->mysqli->prepare( $sql );
	}

	// Bind the params of the query if needed
	function bind()
	{
		$input = func_get_args();
		$cnt = count( $input );
		if ( $cnt < 2 )
		{
			throw new Exception('Wrong Binding!');
			return;
		}
		for( $i = 1; $i < $cnt; ++$i )
		{
			$input[$i] = &$input[$i];
		}
		call_user_func_array( array( $this->query, 'bind_param' ), $input );
	}

	// Execute the query and return the result
	function execute()
	{
		$this->query->execute();
		$this->count ++;
		$this->insID = $this->query->insert_id;
		if ( !$this->query->field_count )
		{
			$this->query->close();
			return True;
		}
		$result = array();
		$meta = $this->query->result_metadata();
		while ( $field = $meta->fetch_field() )
		{
			$params[] = &$row[$field->name];
		}
		$this->query->store_result();
		call_user_func_array( array( $this->query, 'bind_result' ), $params );
		while ( $this->query->fetch() )
		{
			foreach( $row as $key => $val )
			{
				$c[$key] = $val;
			}
			$result[] = $c;
		}
		$this->query->free_result();
		$this->query->close();
		return $result;
	}

	// Return how many queries has been executed
	public function getCount()
	{
		return $this->count;
	}

	// Return the new ID of the data inserted if existing
	public function getInsID()
	{
		return $this->insID;
	}
}
