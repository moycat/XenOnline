<?php
	function check_login()
	{
		global $db, $mem;
		if ( defined( 'MEM' ) && MEM == True )
		{
			$mem = new Memcached( 'moyoj' );
			$mem->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
			if ( !count( $mem->getServerList() ) )
			{
				$mem->addServer(MEM_HOST, MEM_PORT);
			}
		}
		$db = new DB();
		$db->init( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		$db->connect();
		$admin_info = mo_read_cache('mo-admin-'. $_SESSION['aid']);
		if (!$admin_info)
		{
			$sql = 'SELECT `id`, `username`, `password`, `nickname`, `role` FROM `mo_admin` WHERE `id` = ? AND `role` > 0';
			$db->prepare($sql);
			$db->bind('i', $_SESSION['aid']);
			$result = $db->execute();
			if (!$result || $result[0]['password'] != $_SESSION['admin_password'])
			{
				unset($_SESSION['aid']);
				header("Location: login.php");
				exit(0);
			}
			mo_write_cache('mo-admin-'. $_SESSION['aid'] , $result[0]);
		}
		$mo_settings = array();
		mo_load_settings();
		if (!isset($active))
		{
			$active = '';
		}
	}