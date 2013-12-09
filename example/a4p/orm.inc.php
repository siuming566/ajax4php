<?php

class orm
{
	private static function getParam($comment, $word)
	{
		$params = array();
		foreach (explode("\n", $comment) as $line) {
			if (preg_match('/\*\s+@(.[^\s]+)\s+(.[^\s]+)/', trim($line), $match)) {
				if ($match[1] == $word)
					return $match[2];
			}
		}
		return null;
	}

	private static function getEntity($obj)
	{
		$reflect = new ReflectionClass($obj);
		$comment = $reflect->getDocComment();

		$table = self::getParam($comment, "table");
		
		$primarykey = "";

		$columns = array();
		$props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($props as $prop) {
			$comment = $prop->getDocComment();
			$column = self::getParam($comment, "id");
			if ($column != null)
				$primarykey = $column;
			else
				$column = self::getParam($comment, "column");
			if ($column != null)
				$columns[$column] = $prop->getName();
		}

		return array("table" => $table, "primarykey" => $primarykey, "columns" => $columns);
	}

	private static function bind($row, $mapping, $obj)
	{
		foreach ($mapping as $column => $attr) {
			if (property_exists($obj, $attr))
				$obj->$attr = $row[$column];
		}
		return $obj;
	}

	public static function findById($obj, $id)
	{
		$entity = self::getEntity($obj);
		$row = db::select("*")
				->from($entity["table"])
				->where($entity["primarykey"] . " = :id")
				->fetchOneRow(array(":id" => $id));
		return self::bind($row, $entity["columns"], new $obj());
	}

	public static function find($obj, $filter, $param = array())
	{
		$entity = self::getEntity($obj);
		$rows = db::select("*")
				->from($entity["table"])
				->where($filter)
				->fetchAll($param);
		$result = array();
		foreach ($rows as $row)
			$result[] = self::bind($row, $entity["columns"], new $obj());
		return $result;
	}

	public static function insert($obj, $value)
	{
		$entity = self::getEntity($obj);
		$query = db::insert()->into($entity["table"]);
		$binding = array();
		foreach ($entity["columns"] as $column => $attr) {
			if ($column == $entity["primarykey"])
				continue;
			$query->values($column, ":" . $attr);
			$binding[":" . $attr] = $value->$attr;
		}
		return $value->$entity["primarykey"] = $query->execute($binding);
	}

	public static function delete($obj, $value)
	{
		$entity = self::getEntity($obj);
		return db::delete()
		->from($entity["table"])
		->where($entity["primarykey"] . " = :id")
		->execute(array(":id" => $value->$entity["primarykey"]));
	}

	public static function update($obj, $value)
	{
		$entity = self::getEntity($obj);
		$query = db::update($entity["table"]);
		$binding = array();
		foreach ($entity["columns"] as $column => $attr) {
			if ($column == $entity["primarykey"])
				continue;
			$query->set($column, ":" . $attr);
			$binding[":" . $attr] = $value->$attr;
		}
		$binding[":id"] = $value->$entity["primarykey"];
		return $query->where($entity["primarykey"] . " = :id")->execute($binding);
	}
}