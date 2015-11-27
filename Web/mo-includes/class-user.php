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
		private $uid;
		private $username;
		private $nickname;
		private $sex;
		private $email;
		private $qq;
		private $phone;
		private $intro;
		private $group;
		
		// Problem information
		private $try_num;
		private $ac_num;
		private $try_problems;
		private $solved_problems;
		
		// User preferance
		private $language;
		private $show_tag;
		private $send_code;
		private $css;
		private $js;
		
		public function autoAuth()
		{
			
		}
	}
