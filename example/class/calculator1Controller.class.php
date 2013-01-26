<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class calculator1Controller
{
	/** @ajaxcall */
	public function calculate($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("calculator1Model"));

		$model->z = $model->x * $model->y;

		return ""; // return empty string to stay on the same page
	}
	
}
