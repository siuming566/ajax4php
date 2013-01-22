<?php 
// Class file name must be classname.class.php

class rerender1Controller
{
	// setup flag to enable ajax call from browser
	public $enableAjaxCall = true;

	public function getTime()
	{
		return "server time: " . date('Y-m-d H:i:s e');
	}
	
}
