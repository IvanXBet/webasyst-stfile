<?php
class shopStfilePluginSetsCollection 
{
	protected $hash;
	protected $model;
	protected $fields = [];
    protected $where = [];
    protected $count;
    protected $order_by = 'T.id';
	protected $group_by;
    protected $joins;
	protected $join_index = [];
	protected $options = [];
	
	public function __construct($hash = '', $options = array())
    {
		$this->hash = $hash;
		$this->model = new shopStfilePluginSetModel();
		$this->options = $options;
		$this->prepare();
	}
	
	public function getModel()
	{
		return $this->model;
	}

	public function setHash($hash)
	{
		$this->hash = $hash;
	}
	
	public function addJoin($table)
    {
        if(!is_array($table)) {return null;}

		if(isset($table['on'])) {$on = $table['on'];}
		if(isset($table['where'])) {$where = $table['where'];} else {$where = null;}
		if(isset($table['type'])) {$join_type = $table['type'];}
		$table = $table['table'];
        $alias = $this->getAlias($table);

        if(!isset($this->join_index[$alias])) {$this->join_index[$alias] = 1;}
		else {$this->join_index[$alias]++;}
        $alias .= $this->join_index[$alias];

        $join = array(
            'table' => $table,
            'alias' => $alias,
        );
        if($on) {$join['on'] = str_replace(':table', $alias, $on);}
        if(isset($join_type)) {$join['type'] = $join_type;}
        $this->joins[] = $join;
        if($where) {$this->where[] = str_replace(':table', $alias, $where);}
        return $alias;
    }
	
	public function addWhere($condition)
    {
        $this->where[] = $condition;
    }
	
	protected function getAlias($table)
    {
        $alias = '';

        if(is_array($table))
		{
            if(isset($table['alias'])) {$alias = $table['alias'];}
            $table = $table['table'];
        }
        if(!$alias)
		{
            $t = explode('_', $table);
            foreach($t as $tp)
			{
                $alias .= substr($tp, 0, 1);
            }
        }
        if(!$alias) {$alias = $table;}

        return $alias;
    }
	
	public function getOrderBy()
    {
        if($this->order_by) {return "\nORDER BY ".$this->order_by;}
		else {return "";}
    }
	
	public function setOrderBy($order_by)
	{
		$this->order_by = $order_by;
	}
	
	public function getSQL()
    {
        $sql = "FROM ".$this->model->getTableName()." AS T";

        if($this->joins)
		{
            foreach($this->joins as $join)
			{
                $alias = isset($join['alias']) ? $join['alias'] : '';
                if(isset($join['on'])) {$on = $join['on'];}
				else {$on = "T.id = ".($alias ? $alias : $join['table']).".id";}
                $sql .= "\n\t".(isset($join['type']) ? $join['type'].' ' : '')."JOIN ".$join['table']." ".$alias."\n\t\tON ".$on;
				
            }
        }

        $where = $this->where;

        if($where) {$sql .= "\nWHERE ".implode("\n\tAND ", $where);}
        return $sql;
    }
	
	public function getCount()
    {
        if($this->count !== null) {return $this->count;}
        $sql = $this->getSQL();
        $sql = "SELECT COUNT(".($this->joins ? 'DISTINCT ' : '')."T.id) ".$sql;
        $count = intval($this->model->query($sql)->fetchField());
        return $this->count = $count;
    }
	
	public function getItems($fields = 'T.*', $offset = 0, $limit = null, $fetch_by = 'id')
    {
        $from_and_where = $this->getSQL();
        $extra_fields = '';
		$sql = "SELECT ".$fields." \n";
        $sql .= $from_and_where;
        $sql .= $this->getOrderBy();
        if($limit !== null) {$sql .= "\nLIMIT ".($offset ? intval($offset).',' : '').intval($limit);}
		

        $data = $this->model->query($sql)->fetchAll($fetch_by);
        if(!$data) {return array();}
        $this->workupItems($data);
        return $data;
    }
	
	public function workupItems(&$items = array())
    {
		if(!count($items)) {return;}
	}
	
	public function getDatatableItems($fields, $start, $limit)
	{
		$items = $this->getItems($fields, $start, $limit);
		
		$result = array();
		$result['data'] = $items;
		$result['recordsTotal'] = $this->getCount();
		$result['recordsFiltered'] = $this->getCount();
		
		return $result;
	}
	
	protected function prepare()
	{
		if(mb_substr($this->hash, 0, 4) == 'set/' && mb_strlen($this->hash) > 4)
		{
			$set_ids = explode(',', mb_substr($this->hash, 4));
			foreach($set_ids as $key => $value)
			{
				if(!intval($value)) {unset($set_ids[$key]);}
				else {$set_ids[$key] = intval($value);}
			}
			if(count($set_ids))
			{
				$set_files_model = new shopStfilePluginSetFilesModel();
				$alias = $this->addJoin(array(
					'table' => " (SELECT * FROM ".$set_files_model->getTableName()." WHERE set_id IN (".implode(',', $set_ids)."))",
					'on' => "T.id = :table.file_id",
					'type' => 'left',
				));
				$this->addWhere($alias.".file_id IS NOT NULL");
			}
		}
	}
	public function applyFilters($filters)
	{
		if(!is_array($filters)) {$filters = array();}

		if(array_key_exists('all', $filters))
		{
			$all = ifempty($filters['all'], 0);
			if($all && array_key_exists('items', $filters)) {unset($filters['items']);}
			if(!$all)
			{
				if(!array_key_exists('items', $filters)) {$filters['items'] = array(0);}
				elseif(!is_array($filters['items'])) {$filters['items'] = array(0);}
				elseif(!count($filters['items'])) {$filters['items'] = array(0);}
			}
		}

		if(array_key_exists('items', $filters))
		{
			if(is_array($filters['items']))
			{
				if(count($filters['items']))
				{
					$tmp = array();
					foreach($filters['items'] as $item_id)
					{
						$item_id = intval($item_id);
						array_push($tmp, $item_id);
					}
					if(count($tmp)) {$this->addWhere("T.id IN (".implode(',', $tmp).")");}
				}
			}
		}

		if(!count($filters)) {return;}
		
		if(array_key_exists('search', $filters))
		{
			if(mb_strlen($filters['search'])) {$this->addWhere("T.name LIKE '%".$this->model->escape($filters['search'])."%'");}
		}
		
		if(array_key_exists('ext', $filters))
		{
			if(is_array($filters['ext']))
			{
				if(count($filters['ext']))
				{
					$tmp = array();
					foreach($filters['ext'] as $ext)
					{
						$ext = trim($ext);
						if(mb_strlen($ext) > 0) {array_push($tmp, "'".$this->model->escape($ext)."'");}
					}
					if(count($tmp)) {$this->addWhere("T.ext IN (".implode(',', $tmp).")");}
				}
			}
		}

		
	}
}