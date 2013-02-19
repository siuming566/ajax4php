<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class usercontrol1Controller extends Controller
{
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("usercontrol1Model");

		// Show view
		a4p::View("usercontrol1.php");
	}

	/** @ajaxcall */
	public function add($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("usercontrol1Model"));
		
		$model->textfield1 = $model->textfield1 + 1;
		return "";
	}
}
