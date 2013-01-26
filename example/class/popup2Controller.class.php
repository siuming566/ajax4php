<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class popup2Controller
{
	/** @ajaxcall */
	public function add($param)
	{
		// bind form value to model
		$model = form::bind(a4p::Model("popup2Model"));
		
		$model->textfield1 = $model->textfield1 + 1;
		return "";
	}
	
	public function getTime()
	{
		return "server time: " . date('Y-m-d H:i:s e');
	}
	
}
