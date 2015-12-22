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
	
	function get_problem($get)
	{
		if (!isset($get['pid']) || !is_numeric($get['pid']))
		{
			return False;
		}
		global $db;
		$sql = 'SELECT `id`, `title`, `description`, `tag`, `extra`, `time_limit`, `memory_limit`, `state` FROM `mo_judge_problem` WHERE `id` = ?';
		$db->prepare($sql);
		$db->bind('i', $get['pid']);
		$result = $db->execute();
		return $result ? $result[0] : False;
	}
	
	function add_problem()
	{
		if (!isset($_POST['title'], $_POST['test-editormd-markdown-doc'], $_POST['time_limit'], $_POST['memory_limit'], $_POST['tag']))
		{
			return False;
		}
		if (!$_POST['title'] || !$_POST['test-editormd-markdown-doc'] || !is_numeric($_POST['time_limit']) || !is_numeric($_POST['memory_limit']))
		{
			$_SESSION['publish_tmp'] = $_POST;
			$_SESSION['publish_tmp']['description'] = $_POST['test-editormd-markdown-doc'];
			$_SESSION['publish_tmp']['error'] = '未定义的操作。';
			return False;
		}
		$datacount = 0;
		while (isset($_FILES["stdout$datacount"], $_FILES["input$datacount"]) && !$_FILES["stdout$datacount"]['error'] && !$_FILES["input$datacount"]['error'])
		{
			$datacount++;
		}
		if (!$datacount)
		{
			return array(False, '没有测试数据！');
		}
		$extra = '';
		$hash = md5((string)rand(100000, 999999). time());
		$floder = dirname(__FILE__). '/../mo-content/data/'. $hash;
		mkdir($floder, 0777, true);
		for ($i=0; $i<$datacount; $i++)
		{
			move_uploaded_file($_FILES["stdout$i"]['tmp_name'], $floder. "/std$i.out");
			move_uploaded_file($_FILES["input$i"]['tmp_name'], $floder. "/test$i.in");
		}
		$sql = 'INSERT INTO `mo_judge_problem` (`title`, `description`, `tag`, `extra`, `hash`, `ver`, `post_time`, `time_limit`, `memory_limit`, `state`, `ac`, `submit`, `solved`, `try`, `test_turn`)'.
					'VALUES (?, ?, ?, ?, ?, \'0\', CURRENT_TIMESTAMP, ?, ?, \'1\', \'0\', \'0\', \'0\', \'0\', ?)';
		global $db;
		$db->prepare($sql);
		$db->bind('sssssiii', $_POST['title'], $_POST['test-editormd-markdown-doc'], $_POST['tag'], serialize($extra), $hash, $_POST['time_limit'], $_POST['memory_limit'], $datacount);
		$db->execute();
		return True;
	}
	
	function edit_problem()
	{
		
	}
