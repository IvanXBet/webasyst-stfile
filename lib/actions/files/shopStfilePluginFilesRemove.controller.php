<?php
class shopStfilePluginFilesRemoveController extends waJsonController
{

	public function execute()
	{
		$RemoveFile = waRequest::post('file', null);
		if(!array_key_exists('name', $RemoveFile) && !array_key_exists('ext', $RemoveFile) && !array_key_exists('id', $RemoveFile)) {
			$RemoveFile = null;
		}
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
        $this->response = $stfile->removeFile($RemoveFile);
	}
}