<?php

class shopStfilePluginSettingsSaveController extends waJsonController
{
    public function execute()
    {
		//хук или статик мод
		$settings = waRequest::post('settings', null);
		if(!is_array($settings)) {$this->response = array('result' => 0, 'message' => 'Системная ошибка #NOARR'); return;}
		
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$current_settings = $stfile->getSettings();
		foreach($settings as $key => $value)
		{
			$current_settings[$key] = $value;
		}
		$stfile->saveSettings($current_settings);
		

		//работа с шаблоном
		$files_template = waRequest::post('files_template', null);
		$files_template = trim($files_template);

		$custom_template_path = wa()->getDataPath('plugins\stfile').'\CustomTemplates.html';
		$default_template_path = wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/DefaultTemplates.html';
		$default_template = trim(file_get_contents($default_template_path));
		try
		{
			if (file_exists($custom_template_path)) 
			{
				waFiles::write($custom_template_path, $files_template);
			} 
			elseif($files_template !== $default_template)
			{
				waFiles::write($custom_template_path, $files_template);
			}
		}
		catch (Exception $e)
		{
			$this->response = array('result' => 0, 'message' => $e->getMessage());
			
		}
		$this->response = array('result' => 1, 'message' => 'Настройки сохранены');
    }
}
