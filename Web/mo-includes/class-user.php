<?php
	/*
	 * mo-includes/class-user.php @ MoyOJ
	 * 
	 * This file provides the classes ralated to the user system.
	 * 
	 */
	
	class User
	{
		private $uid;
		private $status;
		
		// Functions ralated to operate information
		public function get( $category, $key )
		{
			if ( isset( $this->status[$category][$key] ) )
			{
				return $this->status[$category][$key];
			}
			else
			{
				return False;
			}
		}
		public function setUID( $uid )
		{
			$this->uid = $uid;
			$this->status = array();
		}
		
		// Functions related to login & logout
		public function autoLogin()
		{
			if ( isset( $_POST['login'] ) )
			{
				if ( !isset( $_POST['login_name'] ) && !isset( $_POST['password'] ) )
				{
					return False;
				}
				return $this->login( $_POST['login_name'], $_POST['password'] );
			}
			else if ( isset( $_SESSION['uid'] ) && is_numeric( $_SESSION['uid'] ) )
			{
				mo_write_note( 'Logged in within the session.' );
				return $_SESSION['uid'];
			}
			else if ( isset( $_COOKIE['mo_auth'] ) )
			{
				return $this->memAuth( $_COOKIE['mo_auth'] );
			}
			else
			{
				return False;
			}
		}
		public function memAuth( $cookie )
		{
			$input = explode( '&', $cookie );
			if ( count( $input ) != 3 || !is_numeric( $input[0] ) || !is_numeric( $input[1] ) )
			{
				return False;
			}
			$uid = (int)$input[0];
			$random = $input[1];
			$pass = $input[2];
			global $db;
			$sql = 'SELECT `id`, `password` FROM `mo_user` WHERE `id` = ?';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			$check = md5( $result[0]['password']. $random );
			if ( $check == $pass )
			{
				mo_write_note( 'Logged in with a cookie.' );
				$_SESSION['uid'] = $result[0]['id'];
				// TODO: Write log
				return $_SESSION['uid'];
			}
			return False;
		}
		public function login( $login_name, $password )
		{
			if ( strlen( $login_name ) > 50 || strlen( $password ) > 50 || strlen( $password ) < 6 || !$login_name || !$password )
			{
				return False;
			}
			global $db;
			$sql = 'SELECT `id`, `password` FROM `mo_user` WHERE ';
			if ( strstr( $login_name , '@' ) )
			{
				$sql .= '`email` = ? LIMIT 1';
			}
			else
			{
				$sql .= '`username` = ? LIMIT 1';
			}
			$db->prepare( $sql );
			$db->bind( 's', $login_name );
			$result = $db->execute();
			if ( !$result || !password_verify( $password, $result[0]['password'] ) )
			{
				return False;
			}
			$this->uid = $result[0]['id'];
			$_SESSION['uid'] = $this->uid;
			if ( $_POST['auto_login'] )
			{
				$random = (string)rand( 10000, 99999 );
				$cookie_to_write = $this->uid. '&'. $random. '&'. md5( $this->status['info']['password']. $random );
				setcookie( 'mo_auth', $cookie_to_write, time() + 31536000 );
			}
			// TODO: Write log
			mo_write_note( 'Logged in with a password.' );
		}
		public function logout()
		{
			setcookie( 'mo_auth', '', time() - 3600 );
			unset( $_SESSION['uid'] );
			$this->uid = '';
			$this->status = array();
		}
		
		// Functions related to loading information
		public function loadAll( $uid )
		{
			$this->loadInfo( $uid );
			$this->loadPrefer( $uid );
			$this->loadRecord( $uid );
		}
		public function loadInfo( $uid )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_user` WHERE `id` = ? LIMIT 1';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			foreach ( $result[0] as $key => $value )
			{
				$this->status['info'][$key] = $value;
			}
			$this->uid = $this->status['info']['id'];
			$this->status['info']['password'] = '';
		}
		public function loadPrefer( $uid )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_user_preference` WHERE `uid` = ? LIMIT 1';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			foreach ( $result[0] as $key => $value )
			{
				$this->status['preference'][$key] = $value;
			}
		}
		public function loadRecord( $uid )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_user_record` WHERE `uid` = ? LIMIT 1';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			foreach ( $result[0] as $key => $value )
			{
				$this->status['record'][$key] = $value;
			}
		}
	}
