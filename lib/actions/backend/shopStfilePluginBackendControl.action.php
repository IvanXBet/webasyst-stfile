<?php


class shopStfilePluginBackendControlAction extends waViewAction
{

	public function execute()
	{
		$this->setLayout(new shopBackendLayout());
		$collection = new shopStfilePluginSetsCollection();
		$collection->addWhere("T.product_id IS NULL");
		$collection->setOrderBy("T.sort ASC");
		$result = $collection->getItems('T.*');
		$this->view->assign('sets', $result);
	}
}
