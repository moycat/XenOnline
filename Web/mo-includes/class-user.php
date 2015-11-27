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
		
		public function autoAuth()
		{
			
		}
		
		public function sessionAuth()
		{
			
		}
		
		public function login( $login_name, $password )
		{
			if ( strlen( $login_name ) > 50 || strlen( $password ) > 50 || !$login_name || !$password )
			{
				return;
			}
			global $db;
			$sql = 'SELECT `id`, `username`, `password`, `sex`, `phone`, `email`, `qq`, `show_phone`, `show_email`, `show_qq`, `url`, `school`, `nickname`, `reg_time`, `last_time`,  `user_group`, `last_ip`, `intro`, `title` FROM `mo_user` WHERE ';
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
				return;
			}
			$this->info['uid'] = $result[0]['id'];
			$this->info['username'] = $result[0]['username'];
			$this->info['nickname'] = $result[0]['nickname'] ? $result[0]['nickname'] : $result[0]['username'];
			$this->info['sex'] = $result[0]['sex'];
			$this->info['email'] = $result[0]['email'];
			$this->info['qq'] = $result[0]['qq'];
			$this->info['phone'] = $result[0]['phone'];
			$this->info['intro'] = $result[0]['intro'];
			$this->info['school'] = $result[0]['school'];
			$this->info['url'] = $result[0]['url'];
			$this->info['group'] = $result[0]['user_group'];
			$this->info['title'] = $result[0]['title'];
			$this->info['reg_time'] = $result[0]['reg_time'];
			$this->info['last_ip'] = $result[0]['last_ip'];
			
			print_r($result);
			echo mo_runTime();
		}
	}
