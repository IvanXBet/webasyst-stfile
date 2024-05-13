<?php


class shopStfilePluginSetEditAction extends waViewAction
{

	public function execute()
	{
		$id = waRequest::post('id', 0, 'int');
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->view->assign('set', $stfile->getSetData($id));
		$this->view->assign('filters', $stfile->getFileFilters());
		
	}
}