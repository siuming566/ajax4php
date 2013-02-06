<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class usercontrol2Controller
{
	public function setX($param)
	{
		$model = a4p::Model("usercontrol2Model");
		$model->x = $param;
		$model->z = "";
	}

	public function setY($param)
	{
		$model = a4p::Model("usercontrol2Model");
		$model->y = $param;
		$model->z = "";
	}

	/** @ajaxcall */
	public function calculate($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("usercontrol2Model"));

		$model->z = $model->x * $model->y;

		return ""; // return empty string to stay on the same page
	}
	
}
