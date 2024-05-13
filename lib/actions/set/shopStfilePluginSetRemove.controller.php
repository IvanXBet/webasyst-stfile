<?php


class shopStfilePluginSetRemoveController extends waJsonController
{
	public function execute()
	{
		
		$id = waRequest::post('id', 0, 'int');
		if($id < 1 && !is_numeric($id)) {$this->response = array("result"=> 0,"message"=> "Системная ошибка", "id" => $id); }
		

		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->removeSet($id);
		
	}
}


