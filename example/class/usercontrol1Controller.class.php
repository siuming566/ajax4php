<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class usercontrol1Controller
{
	/** @ajaxcall */
	public function add($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("usercontrol1Model"));
		
		$model->textfield1 = $model->textfield1 + 1;
		return "";
	}
	
}
