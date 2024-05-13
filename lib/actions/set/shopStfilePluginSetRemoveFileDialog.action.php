<?php


class shopStfilePluginSetRemoveFileDialogAction extends waViewAction
{

	public function execute()
	{
		$file_id = waRequest::post('file_id', null);
        $set_id = waRequest::post('set_id', 0, 'int');
        $prodict_id = waRequest::post('product_id', 0, 'int');
        
        if(!$file_id) 
        { 
            return array('result' => 0, 'message' => 'Нет айди файла'); 
        } 
        if($file_id)
        {
            $file = (new shopStfilePluginFileModel())->getById($file_id);
        }
    
        if(isset($file) && array_key_exists('name', $file) && strlen($file['name']) > 0) 
        {
            $file['name'] = explode('.', $file['name'])[0];
        }

        // $stfile = waSystem::getInstance('shop')->getPlugin('stfile');
        // $author = $stfile->getAuthorPDF($file['id'].'.'.$file['ext']);

        $this->view->assign('file', $file);
        $this->view->assign('set_id', $set_id);
        $this->view->assign('prodict_id', $prodict_id);
        // $this->view->assign('author', $author);
	}
}