<?php

class shopStfilePluginSettingsDeleteCustomTemplateController extends waJsonController
{
    public function execute()
    {
		$custom_template_path = wa()->getDataPath('plugins\stfile').'\CustomTemplates.html';
		try
		{
			// if (!file_exists($custom_template_path)){return;} 
			waFiles::delete($custom_template_path);
			$path = wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/DefaultTemplates.html';
            $files_template = file_get_contents($path);
			
		}
		catch (Exception $e)
		{
			$this->response = array('result' => 0, 'message' => $e->getMessage());
			
		}

		$this->response = array('result' => 1, 'message' => 'Шаблон откатился до начального состояния', 'default_template' => $files_template );
    }
}
