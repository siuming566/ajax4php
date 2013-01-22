<?php 
// Class file name must be classname.class.php

class javascript1Controller
{
	// setup flag to enable ajax call from browser
	public $enableAjaxCall = true;

	public function getMessage($param)
	{
		$obj = json_decode($param);
		return "Server Response: " . $obj->a . " " . $obj->b;
	}
	
}
