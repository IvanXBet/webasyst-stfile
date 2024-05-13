<?php


class shopStfilePluginBackendProductAction extends waViewAction
{

	public function execute()
	{
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->view->assign('sets', $stfile->getAllSets());
		
	}
}
