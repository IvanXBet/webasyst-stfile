<?php

class shopStfilePluginSettingsAction extends waViewAction
{
	public function execute()
	{
        $stfile = waSystem::getInstance('shop')->getPlugin('stfile');
        $this->view->assign('settings', $stfile->getSettings());

        $custom_template_path = wa()->getDataPath('plugins\stfile').'\CustomTemplates.html';
        if (file_exists($custom_template_path)) 
        {
            $files_template = file_get_contents($custom_template_path);
        }
        else 
        {
            $path = wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/DefaultTemplates.html';
            $files_template = file_get_contents($path);
        }

        $this->view->assign('files_template', $files_template);
	}
}