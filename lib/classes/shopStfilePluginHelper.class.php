<?php
class shopStfilePluginHelper
{
	static public function dowloadUrl($file, $absolute = false) 
	{
		if(!is_array($file)) 
		{
			$file_model = new shopStfilePluginFileModel();
			$file = $file_model->getById($file);
		}

		if($file === null) {return null;}

		$base_url = wa()->getDataUrl('plugins/stfile/', true, 'shop', $absolute);
		$file_url = $file['id'].'.'.$file['ext'];

		return $base_url.$file_url;
	}
}