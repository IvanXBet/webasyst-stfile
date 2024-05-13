<?php
class shopStfilePluginFilesListController extends shopStfilePluginBackendDatatableController
{

	public function execute()
	{
		$filters = waRequest::get('filters', null);
		$hash = waRequest::get('hash', '');

		if($hash) { $hash = 'set/'.$hash; }
		if(!is_array($filters)) {$filters = array();}
		if(!empty($this->search)) {$filters['search'] = trim($this->search);}
		
		$collection = new shopStfilePluginFilesCollection($hash);
		$collection->applyFilters($filters);
		
		
		$result = $collection->getDatatableItems('T.*', $this->start, $this->length);
		$items = array();
		foreach($result['data'] as $file_id => $file) 
		{
			$tmp = array(
				'<input type="checkbox" class="stf-file-item" data-id="'.intval($file['id']).'">',
				htmlspecialchars($file['name'].'.'.$file['ext'], ENT_QUOTES),
				'<a href="?plugin=stfile&module=files&action=download&file_id='.intval($file['id']).'">
					<i class="icon16 download stf-file-download" title="Скачать" data-id="'.intval($file['id']).'"></i>
				</a>',
				'<i class="icon16 edit stf-file-edit" title="Редактировать" data-id="'.intval($file['id']).'"></i>',
				'<i class="icon16 delete stf-file-delete" title="Удалить" data-id="'.intval($file['id']).'"></i>',
			);
			array_push($items,  $tmp);
		}
		$result['data'] = $items;
		$result['draw'] = $this->draw;
		$this->response = $result;
	}
}