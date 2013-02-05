<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class usercontrol1Controller
{
	public function setValue($param)
	{
		$model = a4p::Model("usercontrol1Model");
		
		$model->textfield1 = $param;
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
