<?php 
// Class file name must be classname.class.php

class page1Controller
{
	// setup flag to enable ajax call from browser
	public $enableAjaxCall = true;

	public function go($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("page1Model"));
		
		if ($model->textfield1 != "")
			return "page2.php"; // you can navigate to page2.php
		else
			return "javascript:alert('Please type something');"; // or you can return a javascript call
	}
	
}
