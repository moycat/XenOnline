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
		
		private $status = array();
		private $info = array();
		private $preference = array();
		private $record;
		private $other = array();
		
		// Functions ralated to operate information
		public function get( $category, $key )
		{
			switch ( $category )
			{
				case 'status':
					return isset( $this->status[$key] ) ? $this->status[$key] : NULL;
					break;
				case 'info':
					return isset( $this->info[$key] ) ? $this->info[$key] : NULL;
					break;
				case 'preference':
					return isset( $this->preference[$key] ) ? $this->preference[$key] : NULL;
					break;
				case 'record':
					return isset( $this->record[$key] ) ? $this->record[$key] : NULL;
					break;
				default:
					return isset( $this->other[$category][$key] ) ? $this->other[$category][$key] : NULL;
			}
		}
		public function set( $category, $key, $value )
		{
			switch ( $category )
			{
				case 'status':
					if ( isset( $this->status[$key] ) )
					{
						$old = $this->status[$key];
						$this->status[$key] = $value;
						return $old;
					}
					else
					{
						return False;
					}
					break;
				case 'info':
					if ( isset( $this->info[$key] ) )
					{
						$old = $this->info[$key];
						$this->info[$key] = $value;
						return $old;
					}
					else
					{
						return False;
					}
					break;
				case 'preference':
					if ( isset( $this->preference[$key] ) )
					{
						$old = $this->preference[$key];
						$this->preference[$key] = $value;
						return $old;
					}
					else
					{
						return False;
					}
					break;
				case 'record':
					if ( isset( $this->record[$key] ) )
					{
						$old = $this->record[$key];
						$this->record[$key] = $value;
						return $old;
					}
					else
					{
						return False;
					}
					break;
				default:
					if ( isset( $this->other[$category][$key] ) )
					{
						$old = $this->other[$category][$key];
						$this->other[$category][$key] = $value;
						return $old;
					}
					else
					{
						$this->other[$category][$key] = $value;
						return $value;
					}
			}
		}
		public function getUID()
		{
			return $this->uid;
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
			$this->loadStatus( $uid );
			$this->loadInfo( $uid );
			$this->loadRecord( $uid );
		}
		public function loadStatus( $uid )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_user` WHERE `id` = ? LIMIT 1';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			foreach ( $result[0] as $key => $value )
			{
				$this->status[$key] = $value;
			}
			$this->uid = $this->status['id'];
			unset( $this->status['password'] );
			unset( $this->status['id'] );
		}
		public function loadInfo( $uid )
		{
			global $db;
			$sql = 'SELECT * FROM `mo_user_info` WHERE `uid` = ? LIMIT 1';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			$this->info = unserialize( $result[0]['info'] );
			$this->preference = unserialize( $result[0]['preference'] );
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
				$this->record[$key] = $value;
			}
		}
		// Functions related to saving information
		public function save( $category )
		{
			global $db;
			$sql = 'UPDATE ';
			switch ( $category )
				{
					case 'status':
						$sql .= '`mo_user` SET `id` = '. $this->uid;
						$bind = array();
						$bind[0] = '';
						foreach ( $this->status as $key => $value)
						{
							$sql .= ", `$key` = ?";
							$bind[] = $value;
							$bind[0] .= 's';
						}
						$sql .= ' WHERE `id` = '. $this->uid;
						$db->prepare( $sql );
						call_user_func_array( array( $db, 'bind' ), $bind );
						break;
					case 'info':
						$sql .= '`mo_user_info` SET `info` = ? WHERE `uid` = '. $this->uid;
						$db->prepare( $sql );
						$db->bind( 's', serialize( $this->info ) );
						break;
					case 'preference':
						$sql .= '`mo_user_info` SET `preference` = ? WHERE `uid` = '. $this->uid;
						$db->prepare( $sql );
						$db->bind( 's', serialize( $this->preference ) );
						break;
					case 'record':
						$sql .= '`mo_user_record` SET `uid` = '. $this->uid;
						$bind = array();
						$bind[0] = '';
						foreach ( $this->record as $key => $value)
						{
							$sql .= ", `$key` = ?";
							$bind[] = $value;
							$bind[0] .= 's';
						}
						$sql .= ' WHERE `uid` = '. $this->uid;
						$db->prepare( $sql );
						call_user_func_array( array( $db, 'bind' ), $bind );
						break;
					default:
						return False;
				}
			$db->execute();
			return True;
		}
	}
