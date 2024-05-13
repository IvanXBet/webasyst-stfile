<?php

class shopStbadgesPluginProductsBadgesGetController extends waJsonController
{
	public function execute()
    {
        $products = waRequest::post('products', '', 'string');
		$products = explode(',', $products);
		if(!count($products)) {return array();}
		foreach($products as $key => $id)
		{
			$id = intval($id);
			if($id) {$products[$key] = $id;}
			else {unset($products[$key]);}
		}
		if(!count($products)) {return array();}
		$stbadges = waSystem::getInstance('shop')->getPlugin('stbadges');
		$this->response = $stbadges->getProductsBackendBadges($products);
    }
}
