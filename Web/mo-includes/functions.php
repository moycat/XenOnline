<?php
	/*
	 * mo-includes/functions.php @ MoyOJ
	 * 
	 * This file provides all basic functions used by MoyOJ.
	 * Others can be found in their related files.
	 * 
	 */
	
	$mo_time = microtime();
	$mo_settings = array();
	
	function mo_init()
	{
		session_start();
		if ( DEBUG == True )
		{
			error_reporting( E_ALL );
			mo_write_note( 'DEBUG ENABLED' );
		}
		else
		{
			error_reporting( E_ERROR | E_WARNING | E_PARSE );
		}
		
		// Check if closed
		if ( file_exists( MOCON. 'closed.lock' ) )
		{
			die( '<h1>Site Closed Temporarily</h1>' );
		}
	}
	
	function mo_analyze()
	{
		$request = array();
		if ( !isset( $_GET['r'] ) || !$_GET['r'] )
		{
			$request[] = 'index';
			return $request;
		}
		$arg = explode( '/', $_GET['r'] );
		array_filter( $arg );
		$arg = array_merge( $arg );
		if ( !$arg[0] )
		{
			$request[] = 'index';
			return $request;
		}
		if ( !file_exists( MOINC. 'load-request-'. $arg[0]. '.php' ) )
		{
			$arg[0] = '404';
		}
		return $arg;
	}
	
	function getPT()
	{
		global $mo_plugin, $mo_theme, $mo_theme_file;
		$mo_theme = mo_get_option( 'theme' );
		$plugin_floder = MOCON. 'plugin/';
		$theme_file = MOCON. 'theme/$mo_theme/$mo_theme.php';
		$plugin = dir( $plugin_floder );
		while( $get = $plugin->read() )
		{
			if( is_dir( "$plugin_floder/$get" ) && $get != "." && $get!=".."
				&& file_exists( "$plugin_floder$get/$get.php" ) )
			{
				$mo_plugin[] = "$plugin_floder$get/$get.php";
			}
		}
		if( !file_exists( $theme_file ) )
		{
			$theme_file = '';
		}
	}
	
	function mo_read_cache( $cache )
	{
		$cache_file = MOCACHE. $cache. '.php';
		if ( file_exists( $cache_file ) )
		{
			require_once( $cache_file );
			return unserialize( $mo_cache[$cache] );
		}
		else
		{
			return False;
		}
	}
	
	function mo_write_cache( $cache, $data )
	{
		if ( !is_writable( MOCACHE ) )
		{
			return False;
		}
		$cache_file = MOCACHE. $cache. '.php';
		$to_cache = "<?php\n" . "\$mo_cache['$cache'] = '". serialize( $data ). "';\n";
		$file = fopen( $cache_file, 'w' );
		fwrite( $file, $to_cache );
		fclose( $file );
		return True;
	}
	
	function mo_del_cache( $cache )
	{
		$cache_file = MOCACHE. $cache. '.php';
		if ( file_exists( $cache_file ) && is_writable( MOCACHE ) )
		{
			return unlink( $cache_file );
		}
		return False;
	}
	
	function is_serialized( $data, $strict = true ) // From WordPress
	{ 
			// if it isn't a string, it isn't serialized 
			if ( ! is_string( $data ) ) 
					return false; 
			$data = trim( $data ); 
			 if ( 'N;' == $data ) 
					return true; 
			$length = strlen( $data ); 
			if ( $length < 4 ) 
					return false; 
			if ( ':' !== $data[1] ) 
					return false; 
			if ( $strict ) {//output 
					$lastc = $data[ $length - 1 ]; 
					if ( ';' !== $lastc && '}' !== $lastc ) 
							return false; 
			} else {//input 
					$semicolon = strpos( $data, ';' ); 
					$brace     = strpos( $data, '}' ); 
					// Either ; or } must exist. 
					if ( false === $semicolon && false === $brace ) 
							return false; 
					// But neither must be in the first X characters. 
					if ( false !== $semicolon && $semicolon < 3 ) 
							return false; 
					if ( false !== $brace && $brace < 4 ) 
							return false; 
			} 
			$token = $data[0]; 
			switch ( $token ) { 
					case 's' : 
							if ( $strict ) { 
									if ( '"' !== $data[ $length - 2 ] ) 
											return false; 
							} elseif ( false === strpos( $data, '"' ) ) { 
									return false; 
							} 
					case 'a' : 
					case 'O' : 
							echo "a"; 
							return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data ); 
					case 'b' : 
					case 'i' : 
					case 'd' : 
							$end = $strict ? '$' : ''; 
							return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data ); 
			} 
			return false; 
	}
	
	function mo_time( $p = 3 )
	{
		global $mo_time;
		$t = microtime();
		list( $m0, $s0 ) = explode( ' ', $mo_time );
		list( $m1, $s1 ) = explode( ' ', $t );
		return round( ( $s1 + $m1 - $s0 - $m0 ) * 1000, $p );
	}
	
	function mo_write_note( $note )
	{
		if ( defined( 'DEBUG' ) && DEBUG == True )
			echo "\n<!-- Note: ". $note. ' Time:'. mo_time(). " -->\n";
	}
	
	function mo_get_user_ip()
	{
		return ip2long( $_SERVER["REMOTE_ADDR"] );
	}
	
	function mo_get_url()
	{
		$url = MO_URL. '/'. $_SERVER['PHP_SELF'];
		return $url;
	}
	
	function mo_in_check( $autoExit = True )
	{
		if ( !defined( 'RUN' ) )
		{
			mo_write_note( 'Invaild entrance.' );
			if ($autoExit)
			{
				exit(0);
			}
			else
			{
				return False;
			}
		}
		return True;
	}
