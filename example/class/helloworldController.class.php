<?php 
// Class file name must be classname.class.php

class helloworldController
{
	// Controller initialize method
	public function init()
	{
		// load the model
		$model = a4p::Model("helloworldModel");
		$model->message = "Hello World";
	}	
}
