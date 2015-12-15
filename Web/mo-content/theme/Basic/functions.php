<?php
	
	function b_check_code()
	{
		if ( !isset( $_POST['code'] ) || strlen( $_POST['code'] ) > 102400 || !strlen( $_POST['code'] ) ||
			!is_numeric( $_POST['lang']) || (int)$_POST['lang'] < 1 || $_POST['lang'] > 3 )
		{
			return False;
		}
		else
		{
			return True;
		}
	}
	
	function b_login()
	{
		if ( isset( $_POST['login'] ) )
		{
			if ( isset( $_POST['login_name'] ) && isset( $_POST['password'] ) )
			{
				global $user;
				return $user->login( $_POST['login_name'], $_POST['password'] );
			}
		}
	}