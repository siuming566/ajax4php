<?php 
// Class file name must be classname.class.php

class page2Controller extends Controller
{
	public function pageLoad()
	{
		// load the model
		$model = a4p::Model("page1Model");

		// Show view
		a4p::View("page2.php");
	}	
}
