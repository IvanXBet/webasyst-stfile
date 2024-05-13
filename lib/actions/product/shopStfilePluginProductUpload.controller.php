<?php


class shopStfilePluginProductUploadController extends waJsonController
{

	public function execute()
	{
		$files = waRequest::file('files');
		$product_id = waRequest::post("product_id", null, 'int');
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->uploadFileProduct($files, $product_id);
	}
}