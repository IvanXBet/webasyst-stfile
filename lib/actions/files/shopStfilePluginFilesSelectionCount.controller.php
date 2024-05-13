<?php


class shopStfilePluginFilesSelectionCountController extends waJsonController
{     

	public function execute()
	{
		$filters = waRequest::post('filters', null);
		$collection = new shopStfilePluginFilesCollection();
		$collection->applyFilters($filters);
		
		$this->response = array('result' => 1, 'count' => $collection->getCount());
		
	}
}