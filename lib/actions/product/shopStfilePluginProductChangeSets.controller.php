<?php


class shopStfilePluginProductChangeSetsController extends waJsonController
{

	public function execute()
	{
		$set_id = waRequest::post('set_id', 0);
		$product_id = waRequest::post('product_id', 0);
		$checked = waRequest::post('checked', 0, 'int');

		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->changeSetInProduct($set_id, $product_id, $checked);
	}
}