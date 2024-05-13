<?php


class shopStfilePluginFilesSetRemoveController extends waJsonController
{

	public function execute()
	{
		
		$filters = waRequest::post('filters', null);
		$sets = waRequest::post('sets', null);

		if(is_array($sets) && count($sets) > 0)
		{
			$sets = array_keys($sets);
		}
		else
		{
			$sets = array();
		}
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->RemoveFilesFromSetByFilters($filters, $sets);
		
	}
}