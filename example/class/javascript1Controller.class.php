<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class javascript1Controller
{
	/** @ajaxcall */
	public function getMessage($param)
	{
		$obj = json_decode($param);
		return "Server Response: " . $obj->a . " " . $obj->b;
	}
}
