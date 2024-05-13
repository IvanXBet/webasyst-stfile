<?php
class shopStfilePluginFilesEditController extends waJsonController
{

	public function execute()
	{
		$newFile = waRequest::post('file', null);
		if(!array_key_exists('name', $newFile) && !array_key_exists('ext', $newFile) && !array_key_exists('id', $newFile)) {
			$newFile = null;
		}
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
        $this->response = $stfile->updateFile($newFile);
		
	}
}