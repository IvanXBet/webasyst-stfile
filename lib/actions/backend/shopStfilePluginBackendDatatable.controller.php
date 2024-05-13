<?php

class shopStfilePluginBackendDatatableController extends waJsonController
{
    protected $draw = null;
	protected $start = null;
	protected $length = null;
	protected $order = null;
	protected $direction = null;
	protected $column = null;
	protected $search = null;
	
	public function preExecute()
	{
		parent::preExecute();
		$this->draw = waRequest::get('draw', 0, 'int');
		$this->start = waRequest::get('start', 0, 'int');
		$this->length = waRequest::get('length', 25, 'int');
		if($this->length < 1 || $this->length > 50) {$this->length = 50;}
		
		$this->order = 0;
		$this->column = 0;
		$this->direction = 'ASC';
		$ordering = waRequest::get('order', null);
		if(is_array($ordering))
		{
			$this->order = 1;
			if(isset($ordering[0]['column'])) {$this->column = intval($ordering[0]['column']);}
			if(isset($ordering[0]['dir']))
			{
				$this->direction = $ordering[0]['dir'];
				if($this->direction === 'desc') {$this->direction = 'DESC';} else {$this->direction = 'ASC';}
			}
		}
		
		$searching = waRequest::get('search', null);
		if(is_array($searching))
		{
			if(!empty($searching['value'])) {$this->search = trim($searching['value']);}
		}
	}
	public function display()
    {
        $this->getResponse()->sendHeaders();
        if(!$this->errors)
		{
            $data = $this->response;
            echo json_encode($data);
        }
		else {echo json_encode(array('status' => 'fail', 'errors' => $this->errors));}
    }
}