<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class calculator1Controller extends Controller
{
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("calculator1Model");

		// Show view
		a4p::View("calculator1.php");
	}	

	/** @ajaxcall */
	public function calculate($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("calculator1Model"));

		$model->z = $model->x * $model->y;

		return ""; // return empty string to stay on the same page
	}
	
}
