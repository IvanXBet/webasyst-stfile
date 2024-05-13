<?php


class shopStfilePluginFilesUploadController extends waJsonController
{

	public function execute()
	{
		$files = waRequest::file('files');
		$id = waRequest::post("id", null, 'int');
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->uploadFilesFromPost($files, $id);
	}
}