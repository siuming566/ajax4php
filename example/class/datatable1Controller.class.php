<?php 
// Class file name must be classname.class.php

class datatable1Controller extends Controller
{
	// Controller pageLoad method
	public function pageLoad()
	{
		// load the model
		global $model;
		$model = a4p::Model("datatable1Model");

		if (!isset($model->data)) {
			// Just fill in some random data
			$model->data = array();
			for ($i = 1; $i <= 132; ++$i) {
				$row["colA"] = $i;
				$row["colB"] = $i;
				$row["colC"] = $i;
				$row["colD"] = $i;
				$model->data[] = $row;
			}
		}

		a4p::View("datatable1.php");
	}	
}
