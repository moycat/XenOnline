<?php
	/*
	 * mo-includes/class-problem.php @ MoyOJ
	 * 
	 * This file provides the classes of problems and solutions.
	 * 
	 */
	
	class Problem
	{
		private $pid;
		private $info = array();
		
		function __construct( $pid = 0 )
		{
			$this->pid = $pid;
			if ( $pid )
			{
				$this->load();
			}
		}
		public function setPID( $pid )
		{
			$this->pid = $pid;
			$this->load();
		}
		public function getPID()
		{
			return $this->pid;
		}
		
		public function getInfo( $category )
		{
			if ( isset( $this->info[$category] ) )
			{
				$content = apply_filter( "problem_$category", $this->info[$category] );
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
			$sql = 'SELECT * FROM `mo_judge_problem` WHERE `id` = ?';
			$db->prepare( $sql );
			$db->bind( 'i', $this->pid );
			$result = $db->execute();
			if ( !$result || !$result[0]['state'] )
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
	
	class Solution
	{
		private $sid;
		private $info = array();
		
		function __construct( $sid = 0 )
		{
			$this->sid = $sid;
			if ( $sid )
			{
				$this->load();
			}
		}
		public function setSID( $sid )
		{
			$this->sid = $sid;
			$this->load();
		}
		public function getSID()
		{
			return $this->sid;
		}
		
		public function getInfo( $info )
		{
			if ( isset( $this->info[$info] ) )
			{
				$content = apply_filter( "solution_$info", $this->info[$info] );
				return $content;
			}
			else
			{
				return False;
			}
		}
		
		public function load()
		{
			global $db, $user;
			$sql = 'SELECT * FROM `mo_judge_solution` WHERE `id` = ?';
			$db->prepare( $sql );
			$db->bind( 'i', $this->sid );
			$result = $db->execute();
			if ( !$result || $result[0]['uid'] != $user->getUID() )
			{
				$this->pid = 0;
				return;
			}
			foreach ( $result[0] as $key => $value )
			{
				$this->info[$key] = $value;
			}
			$sql = 'SELECT `code` FROM `mo_judge_code` WHERE `sid` = ?';
			$db->prepare( $sql );
			$db->bind( 'i', $this->sid );
			$result = $db->execute();
			$this->info['code'] = base64_decode( $result[0]['code'] );
		}
	}
