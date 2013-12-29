<?php 
//
// form.inc - Form operations
//

class form
{
	public static function bind($obj)
	{
		return self::bindValues($_POST, $obj);
	}
	
	public static function getValues()
	{
		$values = array();
		foreach ($_POST as $key => $value)
		{
			$arr = explode(',', $key);
			$var = &$values;
			while (count($arr) > 0) {
				$idx = array_shift($arr);
				if (!isset($var[$idx]))
					$var[$idx] = array();
				$var = &$var[$idx];
			}
			$var = $value;
		}
		return $values;
	}

	public static function bindValues($values, $obj)
	{
		foreach ($values as $key => $value)
		{
			if (property_exists($obj, $key))
				$obj->$key = $value;
		}
		
		return $obj;
	}
}
