<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class fileupload1Controller
{
	/** @ajaxcall */
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
