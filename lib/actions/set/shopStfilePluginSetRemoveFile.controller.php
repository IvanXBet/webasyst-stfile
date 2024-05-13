<?php


class shopStfilePluginSetRemoveFileController extends waJsonController
{
	public function execute()
	{
		$set_id = waRequest::post('set_id', null);
		$file_id = waRequest::post('file_id', null);
		
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
         $this->response = $stfile->removeFileInSet($file_id, $set_id);
		
	}
}


