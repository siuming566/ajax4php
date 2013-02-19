<?php 
// Class file name must be classname.class.php

class rerender1Controller extends Controller
{
	public function getTime()
	{
		return "server time: " . date('Y-m-d H:i:s e');
	}
}
