<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class language1Controller extends Controller
{
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("language1Model");

		// set language
		language::load("message_" . $model->lang);

		// Show view
		a4p::View("language1.php");
	}	

	/** @ajaxcall */
	public function showEnglish($param)
	{
		$model = a4p::Model("language1Model");
		$model->lang = "en";		
	}

	/** @ajaxcall */
	public function showChinese($param)
	{
		$model = a4p::Model("language1Model");
		$model->lang = "zh";		
	}
}
