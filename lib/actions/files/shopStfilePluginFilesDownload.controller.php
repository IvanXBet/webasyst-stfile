<?php
class shopStfilePluginFilesDownloadController extends waJsonController
{
    public function execute() 
    {
        $file_id = waRequest::get('file_id', null, 'int'); 

        if($file_id)
        {
            $file = (new shopStfilePluginFileModel())->getById($file_id);
        }
        
        $name = explode('.', $file['name'])[0]; 
        $path = (wa()->getDataPath('plugins/stfile/', true)).$file_id.'.'.$file['ext'];
        waFiles::readFile($path, $name.'.'.$file['ext'],); 
    }

}


 
 