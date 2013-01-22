<?php 
//
// form.inc - Form operations
//

class form
{
	public static function bind($obj)
	{
		foreach ($_REQUEST as $key => $value)
		{
			if (property_exists($obj, $key))
				$obj->$key = $value;
		}
		
		return $obj;
	}
}
