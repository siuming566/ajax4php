<?php 
// Class file name must be classname.class.php

class fileupload1Controller
{
	// setup flag to enable ajax call from browser
	public $enableAjaxCall = true;

	public function upload($param)
	{
		// decode the param to get the uploaded file
		$upload_file = json_decode($param);

		// get file properties
		$name = $upload_file->name;
		$size = $upload_file->size;
		$tmp_name = $upload_file->tmp_name;

		return "javascript:alert('Filename: $name\\nSize: $size');";
	}	
}
