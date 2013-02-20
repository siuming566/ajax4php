<?php

class Controller
{
	public $name;

	public function pageLoad()
	{
		global $uri;
		a4p::View("$uri.php");
	}	
}

class _defaultController extends Controller
{
}