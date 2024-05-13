<?php


class shopStfilePluginSetUpdateSortController extends waJsonController
{
	public function execute()
	{
		
		$sets = waRequest::post('sets', '', 'string');
		if(!mb_strlen($sets)) {$this->response = array('result' => 0, 'message' => 'Системная ошибка #NOARR'); return;}
        
        $arrSets = explode(',', $sets);
            
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$this->response = $stfile->sortSets($arrSets);
		
	}
}