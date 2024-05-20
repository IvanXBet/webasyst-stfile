<?php

class shopStfilePlugin  extends shopPlugin
{
	const MODE_HOOK = 1;
	const MODE_STATIC = 0;
	/////////////////////////////////////////////////////////////////////////////////////
	// Хуки
	/////////////////////////////////////////////////////////////////////////////////////
	public function backendMenu()
	{
		$view = wa()->getView();
		$plugin = waRequest::get('plugin', '', 'string');
		$module = waRequest::get('module', '', 'string');
		$action = waRequest::get('action', '', 'string');
		$stfile_core_li_class = 'no-tab';
		if($plugin == 'stfile' && $module == 'backend' && $action == 'control') {$stfile_core_li_class = 'selected';}
		
		$view->assign('stfile_core_li_class', $stfile_core_li_class);
		
		return array(
			"core_li" => $view->fetch(wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/BackendMenuCoreLi.html'),
			'aux_li' => $view->fetch(wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/BackendMenuAuxLi.html'),
		);
	}

	public function backendProduct($data)
	{
		$view = wa()->getView();

		$plugin = waRequest::get('plugin', '', 'string');
		$module = waRequest::get('module', '', 'string');
		$action = waRequest::get('action', '', 'string');
		
		$stfile_core_li_class = 'no-tab';
		if($plugin == 'stfile' && $module == 'backend' && $action == 'control') {$stfile_core_li_class = 'selected';}
		$view->assign('stfile_core_li_class', $stfile_core_li_class);

		$view->assign('id_poduct', $data['id']);
		return array('edit_section_li' => $view->fetch(wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/BackendMenuEditSectionLi.html'));
	}
	public function backendProducts()
	{
		
		$collection = new shopStfilePluginSetsCollection();
		$collection->addWhere("T.product_id IS NULL");
		$collection->setOrderBy("T.sort ASC");
		$result = $collection->getItems('T.*');

		$view = wa()->getView();
		$view->assign('stf_sets', $result);
		
		return array(
			'toolbar_organize_li' => $view->fetch(wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/BackendProductsToolbarOrganizeLi.html')
		);
	}
	


	public function frontendProduct($product)
	{
		return array(
			'menu'      => '',
			'cart'      => '',
			'block'     => self::frontendProductFiles($product['id'], true),
			'block_aux' => '',
		);
	}

	static public function frontendProductFiles($product_id, $is_hook = false) 
	{
		$stfile = waSystem::getInstance('shop')->getPlugin('stfile');
		$settings = $stfile->getSettings();

		$hook_mode = intval(ifempty($settings['hook_mode'], 0));
		if($hook_mode == self::MODE_HOOK && !$is_hook) {return '';}
		if($hook_mode == self::MODE_STATIC && $is_hook) {return '';}
		//плучаем id сета продукта
		$setsCollection = new shopStfilePluginSetsCollection();
		$setsCollection->addWhere("T.product_id = ".$product_id);
		$set_id = $setsCollection->getItems('T.id');
		$set_id = array_keys($set_id)[0];
		//получаем id сетов которые прикреплены к продуктку
		$product_set_model = new shopStfilePluginProductSetModel();
		$sets_add = $product_set_model->getByField('product_id', $product_id, true);
		//собираем массив всех id сетов
		foreach($sets_add as $key => $set)
		{
			$sets_add[$key] = $set['set_id'];
		}
		array_push($sets_add, $set_id);
		
		if(!$sets_add) {return;}
		$files = [];
		foreach($sets_add as $key => $set)
		{
			if($sets_add[$key]) {$sets_add[$key] = 'set/'.$set;}
			
			$collection = new shopStfilePluginFilesCollection($sets_add[$key]);
			$file =  $collection->getItems('T.name, id, ext');
			$files += $file;
		}
		
		
		
		$view = wa()->getView();
		$view->assign('stfile_files', $files);

		$template_path = wa()->getDataPath('plugins\stfile').'\CustomTemplates.html';
        if(file_exists($template_path)) 
        {
			
			return $view->fetch(wa()->getDataPath('plugins\stfile').'\CustomTemplates.html');
        }
		else {
			return $view->fetch(wa()->getAppPath(null, 'shop').'/plugins/stfile/templates/DefaultTemplates.html');
		}
	}
	
	/////////////////////////////////////////////////////////////////////////////////////
	// Работа со списками
	/////////////////////////////////////////////////////////////////////////////////////

	public function addSet($data) 
	{
		$set_model = new shopStfilePluginSetModel();

		if(!array_key_exists('product_id', $data)) 
		{
			$data['product_id'] = NULL;
		}
		elseif($set_model->getByField(array('product_id' => $data['product_id'])))
		{
			return array('result' => 1, 'message' => 'Списко этого продукта уже есть', 'set' => $set_model->getByField(array('product_id' => $data['product_id'])));
		}
		else 
		{
			$data['name'] = 'product'.$data['product_id'];
		}
		
		$name = trim(ifempty($data['name'], ''));
		if(!mb_strlen($name)) {return array('result' => 0, 'message' => 'Укажите имя');}
		
		$data['name'] = $name;

		
		$id = $set_model->add($data);
		return array('result' => 1, 'message' => 'Готово', 'set' => $set_model->getById($id));
	}
	public function updateSet($data) 
	{
		$id = ifempty($data['id'], 0);
		if(array_key_exists('id', $data)) {unset($data['id']);}
		if(array_key_exists('sort', $data)) {unset($data['sort']);}
		
		$set_model = new shopStfilePluginSetModel();
		$ex = $set_model->getById($id);
		if(!$ex) {return array('result' => 0, 'message' => 'Список не найден');}
		$set_model->updateById($id, $data);
		return array('result' => 1, 'message' => 'Готово', 'set' => $set_model->getById($id));
	}
	public function removeSet($id) 
	{
		$set_model = new shopStfilePluginSetModel();
		if(!is_numeric($id))
		{
			return array('result' => 0, 'message' => 'Системная ошибка #IDNONUM');
		}
		$set_model->remove($id);
		return array('result' => 1,'message' => 'Успешное удаление', 'id'=>$id);
	}

	public function sortSets($sets) 
	{
		if(!count($sets)) {return array('result' => 0, 'message' => 'Не заданн список для сротировки');}
		$set_model = new shopStfilePluginSetModel();
		return $set_model->sortSets($sets);
	}

	public function getAllSets() 
	{
		$set_model = new shopStfilePluginSetModel();
		return $set_model->getAllSorted();
	}

	public function getSetData($id) {
		$set_model = new shopStfilePluginSetModel();
		$set = $set_model->getById($id);
		if(!$set) {return null;}
		return array('result'=> 1,'data'=> $set);
	}
	/////////////////////////////////////////////////////////////////////////////////////
	// Работа с файлами
	/////////////////////////////////////////////////////////////////////////////////////
	public function uploadFilesFromPost($files, $set_id = '')
	{
		
		$file_model = new shopStfilePluginFileModel();
		
		$path = wa()->getDataPath('plugins/stfile', true);
		$tmp_path = wa()->getDataPath('plugins/stfile/tmp/', true);
		$result = array();
		foreach($files as $file) 
		{
			if(!$file->uploaded()) {$result[] = array('result' => 0, 'message' => 'Не удалось загрузить файл. Проверьте лимиты на размер файла (MAX_UPLOAD_FILESIZE)'); continue;}
			try 
			{
				$uuid = $file_model->getUUID();
				try
				{
					if((file_exists($path) && !is_writable(($path)) || !file_exists($path) && !waFiles::create($path))) 
					{
						$result[] = array('result' => 0, 'message' => 'Ошибка записи фалйла. Проверте права на запись');
					}
					else
					{
						$file->copyTo($tmp_path.$uuid.'.'.$file->extension);
						$hash = md5(file_get_contents($tmp_path.$uuid.'.'.$file->extension));
						waFiles::delete($tmp_path.$uuid.'.'.$file->extension);

						if($file_model->getByField('hash', $hash)) 
						{
							$file = $file_model->getByField('hash', $hash);

							if($set_id != '')
							{
								$result = $this->addFileInSet($file['id'], $set_id);
								continue;
							}
							
							$result[] = array('result' => 0, 'message' => 'Такой файл уже загружен', 'file' => $file); 
							continue;
						}
						
						$data = array(
							'name' => pathinfo(basename($file->name), PATHINFO_FILENAME),
							'size' => $file->size,
							'original_filename' => basename($file->name),
							'ext' => $file->extension,
							'hash' => $hash
						);

						$id = $file_model->insert($data);
						$file->moveTo($path, $id.'.'.$file->extension);
						
						//сохраняем зависимоть от списка
						if($set_id != '')
						{
							$file = $file_model->getByField('hash', $hash);
							$result = $this->addFileInSet($file['id'], $set_id);
							continue;
						}
						
						$result[] = array('result' => 1, 'message' => 'Файл загружен', 'file' =>  $file_model->getById($id));
						
					}
				}
				catch (Exception $e)
				{
					$result[] = array('result' => 0, 'message' => $e->getMessage());
				}
			}
			catch (Exception $e)
			{
				$result[] = array('result' => 0, 'message' => $e->getMessage());
			}
		}

		return $result;
		
	}
//сохраняем зависимоть файла от списка
	public function addFileInSet($file_id, $set_id)
	{
		$set_files_model = new shopStfilePluginSetFilesModel();
		//проверка еслить ли такой фалй в списке
		
		$data = array(
			'file_id' => $set_files_model->escape($file_id),
			'set_id' => $set_files_model->escape($set_id),
		);

		if($set_files_model->getByField($data)) 
		{
			$result[] = array('result' => 0, 'message' => 'В этом списке уже есть этот фалй', 'file_id' => $file_id); 
		}
		else
		{
			$set_files_model->addFile($file_id ,$set_id);
			$result[] = array('result' => 1, 'message' => 'Файл добавлен в этот список', 'file_id' => $file_id);
		}
			

		return $result;	
	}

	//удаляем зависимоть файла от списка

	public function removeFileInSet($file_id, $set_id)
	{
		$set_files_model = new shopStfilePluginSetFilesModel();
		//проверка еслить ли такой фалй в списке
		
		$data = array(
			'file_id' => $set_files_model->escape($file_id),
			'set_id' => $set_files_model->escape($set_id),
		);

		if($set_files_model->getByField($data)) 
		{
			$set_id = (int)$set_id;
			$set_files_model->removeFile($file_id ,$set_id);
			$result[] = array('result' => 1, 'message' => 'Файл удалён из списка', 'file_id' => $file_id);
		}
		else
		{

			$result[] = array('result' => 0, 'message' => 'Файл не найден'); 
		}
		return $result;	
	}

	//изменение имяни фала 
	public function updateFile($file) 
	{
		$file_model = new shopStfilePluginFileModel();

		$id = $file_model->escape($file['id']);
		$name = $file_model->escape($file['name']);
		
		if(is_numeric($id)) 
		{
			$file_model->updateById($id ,array('name' => $name));
			$result[] = array('result' => 1, 'message' => 'Имя изменяно', 'file' =>  $file_model->getById($id));
		}
		else
		{
			$result[] = array('result' => 0, 'message' => 'Ошибка записи');
		}
		return $result;
	}

	//удаление фалйа
	public function removeFile($file) 
	{
		$file_model = new shopStfilePluginFileModel();
		$set_files_model = new shopStfilePluginSetFilesModel();
		$path = wa()->getDataPath('plugins/stfile/', true);
		$id = $file_model->escape($file['id']);

		if(is_numeric($id)) 
		{
			if($file_model->getById($id)) 
			{
				$file_model->deleteById($id);
				$set_files_model->deleteByField('file_id', $id);
				unlink($path.$file['id'].'.'.$file['ext']);
				$result[] = array('result' => 1, 'message' => 'Файл удалён');
			}
			else
			{
				$result[] = array('result' => 0, 'message' => 'Такого файла не существует');
			} 
			
		}
		else
		{
			$result[] = array('result' => 0, 'message' => 'Ошибка удаления');
		}

		return $result;

	}
	


	public function getAuthorPDF($file_name) 
	{
		$pdf = new PDFInfo;

		$path = wa()->getDataPath('plugins/stfile/', true);
		$pdf->load($path.$file_name);

		return trim($pdf->author);
	}

	//Получение фильров для файлов
	public function getFileFilters()
	{
		$file_model = new shopStfilePluginFileModel();

		return array(
			'exts' => $file_model->getUniqueExtensions(),
		);
	}




	/////////////////////////////////////////////////////////////////////////////////////
	// Массовые действия с файлами
	/////////////////////////////////////////////////////////////////////////////////////
	//Добавление файлов в список
	public function addFilesToSetByFilters($filters, $sets) 
	{
		$collection = new shopStfilePluginFilesCollection();
		$collection->applyFilters($filters);

		$file_ids = $collection->getItems();

		if(is_array($file_ids) && count($file_ids) > 0)
		{
			$file_ids = array_keys($file_ids);
		}
		else
		{
			return array("result" => 0, 'message' => 'Не заданы айди для файлов', 'data' => '');
		}
		
		if(!is_array($sets) && count($sets) < 1)
		{
			return array("result" => 0, 'message' => 'Не заданы айди для списков', 'data' => '');
		}

		
		foreach($sets as $set_id)
		{
			foreach($file_ids as $file_id)
			{
				$file_id = intval($file_id);
				$set_id = intval($set_id);

				if($file_id && $set_id)
				{
					$this->addFileInSet($file_id, $set_id);
				}
			}
		}
		return array("result" => 1, 'message' => 'Файлы добавленны в список', 'data' => '');
	}

	//Удаление файлов из списока
	public function RemoveFilesFromSetByFilters($filters, $sets)
	{
		$collection = new shopStfilePluginFilesCollection();
		$collection->applyFilters($filters);

		$file_ids = $collection->getItems();

		if(is_array($file_ids) && count($file_ids) > 0)
		{
			$file_ids = array_keys($file_ids);
		}
		else
		{
			return array("result" => 0, 'message' => 'Не заданы айди для файлов', 'data' => '');
		}
		
		if(!is_array($sets) && count($sets) < 1)
		{
			return array("result" => 0, 'message' => 'Не заданы айди для списков', 'data' => '');
		}

		
		foreach($sets as $set_id)
		{
			foreach($file_ids as $file_id)
			{
				$file_id = intval($file_id);
				$set_id = intval($set_id);

				if($file_id && $set_id)
				{
					$this->removeFileInSet($file_id, $set_id);
				}
			}
		}
		return array("result" => 1, 'message' => 'Файлы удалены из выбранных списков', 'data' => '');
	}
	//удалене файлов
	public function RemoveFilesByFilters($filters)
	{
		$collection = new shopStfilePluginFilesCollection();
		$collection->applyFilters($filters);

		$files = $collection->getItems();

		if(!is_array($files) && !count($files) > 0)
		{
			return array("result" => 0, 'message' => 'Файлы не найдены', 'data' => '');
		}
		
		foreach($files as $file)
		{
			$this->removeFile($file);
		}
		
		return array("result" => 1, 'message' => 'Файлы удалены', 'data' => '');
	}
	/////////////////////////////////////////////////////////////////////////////////////
	// Работа с файлами продукта
	/////////////////////////////////////////////////////////////////////////////////////
	//Добавление файла в список продукта
	public function uploadFileProduct($files, $product_id)
	{
		$set_model = new shopStfilePluginSetModel();

		$set = $set_model->getByField(array('product_id' => $product_id));

		if(!$set) 
		{
			$data = array(
				'name' => 'product'.$product_id,
				'product_id' => $product_id,
			);

			$this->addSet($data);
			$set = $set_model->getByField(array('product_id' => $product_id));
		}
		
		return $this->uploadFilesFromPost($files, $set['id']);
	}
	//Добавление к продукуту фалйов из списка
	public function addSetToProduct($sets_id, $product_id)
	{
		
		$model_set_files = new shopStfilePluginSetFilesModel();
		$model_set = new shopStfilePluginSetModel();

		if(is_numeric($product_id)) 
		{
			$set_product = $model_set->getByField('product_id', $product_id);

			try
			{
				foreach($sets_id as $set_id)
				{
					
					$files_in_set = $model_set_files->getByField('set_id', $set_id, true);
					
					foreach($files_in_set as $file_id)
					{
						$this->addFileInSet($file_id['file_id'], $set_product['id']);
					}
				}
			
				$result[] = array('result' => 1, 'message' => 'Файлы добавленны');
			}
			catch (Exception $e)
			{
				$result[] = array('result' => 0, 'message' => $e->getMessage());
			}
		}
		else
		{
			$result[] = array('result' => 0, 'message' => 'Ошибка записи');
		}

		return $result;
	}
	//Добавление сета к списоку продукта
	public function changeSetInProduct($set_id, $product_id, $checked)
	{
		if(is_numeric($set_id) && is_numeric($product_id)) 
		{
			$model = new shopStfilePluginProductSetModel(); 
			$data = array(
				'product_id' => $model->escape($product_id),
				'set_id' => $model->escape($set_id),
			);
			if($checked == 1)
			{
				if($model->getByField($data))
				{
					return $result[] = array('result' => 0, 'message' => 'Этот списко уже прикреплен');
				}
				$model->insert($data);
				$result[] = array('result' => 1, 'message' => 'Списко прикреплен к продукту');
			}
			
			if($checked == 0) 
			{
				$model->deleteByField($data);
				$result[] = array('result' => 1, 'message' => 'Списко откреплён от продукта');
			}
		}
		else
		{
			$result[] = array('result' => 0, 'message' => 'Ошибка записи');
		}
		return $result;
	}
	//Добавление сета к продуктам

	public function changeSetInProducts($set_id, $products_ids, $status)
	{
		if(is_numeric($set_id)) {
			try
			{
				foreach($products_ids as $product_id)
				{
					$result[] = $this->changeSetInProduct($set_id, $product_id, $status);
				}
			}
			catch (Exception $e)
			{
				$result[] = array('result' => 0, 'message' => $e->getMessage());
			}
			
		}
		else
		{
			$result[] = array('result' => 0, 'message' => 'Ошибка записи');
		}
		return $result;
	}
	
}
