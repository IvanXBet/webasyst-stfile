<?php


class shopStfilePluginFilesSetRemoveDialogAction extends waViewAction
{     

	public function execute()
	{
		$filters = waRequest::post('filters', null);
		$collection = new shopStfilePluginFilesCollection();
		
		$collection->applyFilters($filters);
		$this->view->assign('file_count', $collection->getCount());
		$this->view->assign('file_filters', http_build_query(array('filters' => $filters)));
		

		$collection = new shopStfilePluginSetsCollection();
		$collection->addWhere("T.product_id IS NULL");
		$result = $collection->getItems('T.*');
		$this->view->assign('sets', $result);
	}
}