<?php
	/*
	 * mo-includes/class-user.php @ MoyOJ
	 * 
	 * This file provides the classes ralated to the user system.
	 * 
	 */
	
	class User
	{
		// Basic information
		private $info = array();
		
		// Problem information
		private $record = array();
		
		// User preferance
		private $preferance = array();
		
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
			if ( isset( $_SESSION['uid'] ) )
			{
				$this->loadAll( $_SESSION['uid'] );
				return True;
			}
			if ( isset( $_COOKIE['mo_auth'] ) )
			{
				$this->memAuth( $_COOKIE['mo_auth'] );
			}
		}
		
		public function memAuth( $cookie )
		{
			$input = explode( ' ', $cookie );
			if ( count( $input ) != 3 || !is_numeric( $input[0] ) || !is_numeric( $input[2] ) )
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
				$this->loadAll( $uid );
				return True;
			}
			return False;
		}
		
		public function login( $login_name, $password )
		{
			if ( strlen( $login_name ) > 50 || strlen( $password ) > 50 || !$login_name || !$password )
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
			$this->loadAll( $result[0]['id'] );
			$_SESSION['uid'] = $this->uid;
			if ( $_POST['auto_login'] )
			{
				$random = (string)rand( 10000, 99999 );
				$cookie_to_write = $this->uid. ' '. $random. ' '. md5( $this->password. $random );
				setcookie( 'mo_auth', $cookie_to_write, time() + 31536000 );
			}
		}
		
		private function loadAll( $uid )
		{
			$this->loadInfo( $uid );
//			$this->loadPrefer( $uid );
//			$this->loadRecord( $uid );
		}
		private function loadInfo( $uid )
		{
			global $db;
			$sql = 'SELECT `id`, `username`, `password`, `sex`, `phone`, `email`, `qq`, `show_phone`, `show_email`, `show_qq`, `url`, `school`, `nickname`, `reg_time`, `last_time`,  `user_group`, `last_ip`, `intro`, `title` FROM `mo_user` WHERE `uid` = ?';
			$db->prepare( $sql );
			$db->bind( 'i', $uid );
			$result = $db->execute();
			foreach ( $result[0] as $key => $value )
			{
				$this->info[$key] = $value;
			}
		}
	}
