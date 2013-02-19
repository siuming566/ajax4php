<?php 
// Class file name must be classname.class.php

class helloworldController extends Controller
{
	// Controller page load method
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("helloworldModel");

		// Do something
		$model->message = "Hello World";

		// Show view
		a4p::View("helloworld.php");
	}	
}
