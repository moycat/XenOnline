<?php
	/*
	 * MoyOJ Theme: Basic
	 * Author: Moycat
	 * 
	 * The theme is very simple and pretty ugly.
	 * Just for test.
	 * 
	 */
	 
	function Basic()
	{
		global $mo_request, $mo_theme_floder;
		require_once( $mo_theme_floder. 'header.php' );
		
		switch ( $mo_request[0] )
		{
			case 'index':
			require_once( $mo_theme_floder. 'index.php' );
			break;
			case 'problem':
			require_once( $mo_theme_floder. 'problem.php' );
			break;
			case 'solution':
			require_once( $mo_theme_floder. 'solution.php' );
			break;
			case 'user':
			require_once( $mo_theme_floder. 'user.php' );
			break;
			case 'discuss':
			require_once( $mo_theme_floder. 'discuss.php' );
			break;
			default:
			require_once( $mo_theme_floder. '404.php' );
		}
		
		require_once( $mo_theme_floder. 'footer.php' );
		
		add_action( 'loadDone', 'buggy');
		
	}

	
	function buggy()
	{
		echo '<br>DEBUG INFO:<br>';
		//mo_list_discussions( 1, 1 );
		//mo_log_user( 1, 1, 'adsdfa' );
		//$user->login('moycat', '123456');
		//echo password_hash('123456', PASSWORD_DEFAULT, ['cost' => 5 ] ) . "<br>";
		//echo serialize( $mo_settings );
		//mo_del_user( 21 );
		//mo_add_user('asdf553', 'dsfdffdff', 'g24g234g');
		//$user->refresh_login();
		//mo_set_option( '123', array());
		//$p = mo_list_problems(1, 2, 'qwertyuio');
		//mo_add_new_discussion( 4, 'cha', 'dsfsdfg发发', $pid = 0 );
		//$p = mo_list_solutions(1, 2);
		//var_dump($p);
		//echo mo_get_problem_count('qwertyuio');
		global $mo_request;
		print_r($mo_request);
		echo "\n<br>". mo_time();
	}
