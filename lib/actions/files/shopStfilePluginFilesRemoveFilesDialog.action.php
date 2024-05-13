<?php


class shopStfilePluginFilesRemoveFilesDialogAction extends waViewAction
{     

	public function execute()
	{
		$filters = waRequest::post('filters', null);
		$collection = new shopStfilePluginFilesCollection();

		$file_model = new shopStfilePluginFilesCollection();
        $file_model->applyFilters($filters);
        $files = $file_model->getItems();
		
		$collection->applyFilters($filters);
		$this->view->assign('file_count', $collection->getCount());
		$this->view->assign('files', $files);
		$this->view->assign('file_filters', http_build_query(array('filters' => $filters)));
	}
}