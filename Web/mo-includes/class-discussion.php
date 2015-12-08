<?php
	/*
	 * mo-includes/class-discuss.php @ MoyOJ
	 * 
	 * This file provides the class of discussion.
	 * 
	 */
	
	class Discuss
	{
		private $did;
		private $info = array();
		
		function __construct( $pid = 0 )
		{
			$this->did = $did;
			if ( $did )
			{
				$this->load();
			}
		}
		public function setDID( $did )
		{
			$this->did = $did;
			$this->load();
		}
		public function getDID()
		{
			return $this->did;
		}
		
		public function getInfo( $info )
		{
			if ( isset( $this->info[$info] ) )
			{
				$content = apply_filter( "discussion_$category", $this->info[$info] );
				return $content;
			}
			else
			{
				return False;
			}
		}
		
		public function load()
		{
			global $db;
			$sql = 'SELECT * FROM `mo_judge_discussion` WHERE `id` = ?';
			$db->prepare( $sql );
			$db->bind( 'i', $this->pid );
			$result = $db->execute();
			if ( !$result )
			{
				$this->pid = 0;
				return;
			}
			foreach ( $result[0] as $key => $value )
			{
				$this->info[$key] = $value;
			}
			$this->info['extra'] = unserialize( $this->info['extra'] );
		}
	}
