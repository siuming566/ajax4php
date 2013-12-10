<?php

class Entity
{
	public $_new = true;

	public static function findById($id)
	{
		return orm::findById(get_called_class(), $id);
	}

	public static function findAll($filter, $param = array())
	{
		return orm::findAll(get_called_class(), $filter, $param);
	}

	public static function findFirst($filter, $param = array())
	{
		return orm::findFirst(get_called_class(), $filter, $param);
	}

	public function Insert()
	{
		return orm::insert(get_called_class(), $this);
	}

	public function Delete()
	{
		return orm::delete(get_called_class(), $this);
	}

	public function Update()
	{
		return orm::update(get_called_class(), $this);
	}

	public function SaveOrUpdate()
	{
		if ($this->_new)
			Insert();
		else
			Update();
	}
}