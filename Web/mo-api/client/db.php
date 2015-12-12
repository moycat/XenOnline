<?php
	/*
	 * mo-api/client/db.php @ MoyOJ
	 * 
	 * This file provides the classes ralated to database operations.
	 * For client api only.
	 * 
	 */

	class DB
	{
		private $host = 'localhost';
		private $name = 'moyoj';
		private $user = 'moyoj';
		private $pass = 'moyoj';
		
		private $mysqli = NULL;
		private $query;
		private $insID;
		
		private $count = 0;
		
		function init($db_host, $db_user, $db_pass, $db_name)
		{
			$this->host = $db_host;
			$this->name = $db_name;
			$this->user = $db_user;
			$this->pass = $db_pass;
		}
		function connect()
		{
			$this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->name);
			if (mysqli_connect_errno())
			{
				$error = mysqli_connect_errno();
				p("Error Connecting to the Database #$error");
				return False;
			}
			$this->mysqli->set_charset('utf8');
			p("Connected to the database successfully.");
			return True;
		}
		function prepare($sql)
		{
			$this->query = $this->mysqli->prepare($sql);
			return $this->query ? True : False;
		}
		function bind()
		{
			$input = func_get_args();
			$cnt = count($input);
			if ($cnt < 2)
			{
				throw new Exception('Wrong Binding!');
				return;
			}
			for($i = 1; $i < $cnt; ++$i)
			{
				$input[$i] = &$input[$i];
			}
			call_user_func_array(array($this->query, 'bind_param'), $input);
		}
		function execute()
		{
			$this->query->execute();
			$this->count ++;
			$this->insID = $this->query->insert_id;
			if (!$this->query->field_count)
			{
				$this->query->close();
				return 0;
			}
			$result = array();
			$meta = $this->query->result_metadata();   
			while ($field = $meta->fetch_field())
			{
				$params[] = &$row[$field->name];
			}
			$this->query->store_result();
			call_user_func_array(array($this->query, 'bind_result'), $params);
			while ($this->query->fetch())
			{
				foreach($row as $key => $val)
				{
					$c[$key] = $val;
				}
				$result[] = $c;
			}
			$this->query->free_result();
			$this->query->close();
			return $result;
		}
		
		public function getCount()
		{
			return $this->count;
		}
		public function getInsID()
		{
			return $this->insID;
		}
	}
