<?php


class shopStfilePluginProductAddSetDialogAction extends waViewAction
{
	
	public function execute()
	{

		$product_id = waRequest::post('product_id', 0, 'int');
		$this->view->assign('product_id', $product_id); 

		$collection = new shopStfilePluginSetsCollection();
		$collection->addWhere("T.product_id IS NULL");
		$result = $collection->getItems('T.*');
		$this->view->assign('sets', $result);
	}
}
