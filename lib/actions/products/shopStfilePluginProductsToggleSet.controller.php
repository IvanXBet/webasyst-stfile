<?php

class shopStfilePluginProductsToggleSetController extends waJsonController
{
	public function execute()
    {
		$products = waRequest::post('ids_product', '', 'string');
		$ids = array();
		$products = explode(',', $products);
		
		if(count($products))
		{
			foreach($products as $key => $product)
			{
				$product_id = intval($product);
				if($product_id) {array_push($ids, $product_id);}
			}
		}

		$set_id = waRequest::post('set_id', 0, 'int');
		if(!$set_id) {$this->response = array(); return;}

		$status = intval(waRequest::post('status', '', 'string'));
		if($status != 1 && $status != 0) {$this->response = array(); return;}

		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->changeSetInProducts($set_id, $ids, $status);
    }
}
