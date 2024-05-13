<?php 
	class shopStfilePluginSetFilesModel extends waModel
	{
		protected $table = 'shop_stfile_set_files'; 
		protected $id = array('file_id', 'set_id');

		public function addFile($file_id, $set_id)
        {
            $data['set_id'] = $this->escape($set_id);
			$data['file_id'] = $this->escape($file_id);
            return $this->insert($data);
        }

		public function removeFile($file_id, $set_id)
        {
            $data['set_id'] = $this->escape($set_id);
			$data['file_id'] = $this->escape($file_id);
            return $this->deleteByField($data);
        }
		
	}