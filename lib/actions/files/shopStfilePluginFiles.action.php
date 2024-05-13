<?php


class shopStfilePluginFilesAction extends waViewAction
{

	public function execute()
	{
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->view->assign('filters', $stfile->getFileFilters());
	}
}