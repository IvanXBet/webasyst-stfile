<?php


class shopStfilePluginProductFilesAction extends waViewAction
{

	public function execute()
	{
		$product_id = waRequest::get('id', 0, 'int');
		$model_product_set = new shopStfilePluginProductSetModel();
		$collection = new shopStfilePluginSetsCollection();
		$collection->addWhere("T.product_id IS NULL");
		$collection->setOrderBy("T.sort ASC");
		$result = $collection->getItems('T.*');

 		$cheked_set = $model_product_set->getByField('product_id', $product_id, true);

		foreach ($result as $key => $set)
		{
			$result[$key]['checked'] = false;
			foreach($cheked_set as $check)
			{
				if($set['id'] === $check['set_id'])
				{
					$result[$key]['checked'] = true;
				}
			};
		};
		
		$this->view->assign('product_id', $product_id); 
		$this->view->assign('cheked_set', $cheked_set); 
		$this->view->assign('sets', $result); 
		
	}
}
