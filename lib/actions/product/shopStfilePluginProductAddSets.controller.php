<?php


class shopStfilePluginProductAddSetsController extends waJsonController
{

	public function execute()
	{
		$sets = waRequest::post('sets', null);
		$product_id = waRequest::post('product_id', null);

		if(!is_array($sets)) {return;}
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->addSetToProduct(array_keys($sets), $product_id);
	}
}