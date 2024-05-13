<?php 
	class shopStfilePluginFileModel extends waModel
	{
		protected $table = 'shop_stfile_file'; 
		public function getUUID()
		{
			$data = $this->query('SELECT UUID() AS uuid')->fetchAll();
			return $data[0]['uuid'];
		}

		public function getUniqueExtensions()
		{
			return $this->query("SELECT DISTINCT ext FROM ".$this->table)->fetchAll('ext');
		}

	}