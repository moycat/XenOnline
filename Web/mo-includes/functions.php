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
		return $arg;
	}
	
	function getPT()
	{
		global $mo_plugin, $mo_theme, $mo_theme_floder, $mo_theme_file;
		$mo_theme = mo_get_option( 'theme' );
		$plugin = mo_get_option( 'plugin' );;
		$plugin_floder = MOCON. 'plugin/';
		$mo_theme_floder = MOCON. "theme/$mo_theme/";
		$mo_theme_file = $mo_theme_floder. "$mo_theme.php";
		if ( $plugin )
		{
			foreach ( $plugin as $now )
			{
				if( is_dir( file_exists( "$plugin_floder$now/$now.php" ) ) )
				{
					$mo_plugin[] = "$plugin_floder$now/$now.php";
				}
			}
		}
		if( !file_exists( $mo_theme_file ) )
		{
			$mo_theme_file = '';
		}
	}
	
	function mo_read_cache( $cache )
	{
		if ( defined( 'MEM' ) && MEM == True )
		{
			global $mem;
			return $mem->get( $cache );
		}
		else
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
	}
	
	function mo_write_cache( $cache, $data )
	{
		if ( defined ( 'MEM' ) && MEM == True )
		{
			global $mem;
			if ( !$mem->set( $cache, $data ) )
				$mem->replace( $cache, $data );
			return True;
		}
		else
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
	}
	
	function mo_del_cache( $cache )
	{
		if ( defined( 'MEM' ) && MEM == True )
		{
			global $mem;
			return $mem->delete( $cache );
		}
		else
		{
			$cache_file = MOCACHE. $cache. '.php';
			if ( file_exists( $cache_file ) && is_writable( MOCACHE ) )
			{
				return unlink( $cache_file );
			}
			return False;
		}
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
		if ( defined( 'DEBUG' ) && DEBUG && defined( 'OUTPUT' ) && OUTPUT )
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
