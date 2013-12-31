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
	
	public static function optionValue(array $arr, $selected = null)
	{
		$str = "";
		foreach ($arr as $option) {
			$isSelected = ($option == $selected) ? ' selected="true"' : "";
			$str .= "<option value=\"$option\"$isSelected>$option</option>\r\n";
		}
		return $str;
	}

	public static function optionValuePair(array $arr, $selected = null)
	{
		$str = "";
		foreach ($arr as $key => $value) {
			$isSelected = ($key == $selected) ? ' selected="true"' : "";
			$str .= "<option value=\"$key\"$isSelected>$value</option>\r\n";
		}
		return $str;
	}
}
