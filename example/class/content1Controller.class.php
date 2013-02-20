<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class content1Controller extends Controller
{
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("content1Model");

		// Setup environment for template
		$env["lang"] = "English";

		// Show template
		template::View("template1.php", array(
			"header" => "header1.php", 
			"menu" => "menu1.php", 
			"content" => "content1.php", 
			"footer" => "footer1.php"), $env);
	}	

	/** @ajaxcall */
	public function add($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("content1Model"));
		
		$model->textfield1 = $model->textfield1 + 1;
		return "";
	}
}
