<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class usercontrol3Controller extends Controller
{
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("usercontrol3Model");

		// Show view
		a4p::View("usercontrol3.php");
	}

	/** @ajaxcall */
	public function add($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("usercontrol3Model"));
		
		$model->textfield1 = $model->textfield1 + 1;
		return "";
	}
	
	public function getTime()
	{
		return "server time: " . date('Y-m-d H:i:s e');
	}
}
