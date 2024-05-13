<?php


class shopStfilePluginFilesRemoveFilesController extends waJsonController
{

	public function execute()
	{
		
		$filters = waRequest::post('filters', null);

		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->RemoveFilesByFilters($filters);
		
	}
}