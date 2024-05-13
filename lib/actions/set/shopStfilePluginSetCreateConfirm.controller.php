<?php


class shopStfilePluginSetCreateConfirmController extends waJsonController
{
	public function execute()
	{
		
		$set = waRequest::post("set", null);
		if(!is_array($set)) {$this->response = array("result"=> 0,"message"=> "Системная ошибка"); return;}
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->addSet($set);
		
	}
}


