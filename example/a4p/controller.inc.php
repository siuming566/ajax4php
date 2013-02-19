<?php

class Controller
{
	public $name;

	public function __construct()
	{
		$this->name = str_replace("\\", "/", dirname(substr(__FILE__, strlen(realpath($_SERVER["DOCUMENT_ROOT"]) . "/class/"))));
	}

	public function pageLoad()
	{
		global $uri;
		a4p::View("$uri.php");
	}	
}

class _defaultController extends Controller
{
}