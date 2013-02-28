<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class fileupload1Controller extends Controller
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

		return "javascript:alert('Upload1: Filename: $name\\nSize: $size');";
	}	

	/** @ajaxcall */
	public function upload2($param)
	{
		// decode the param to get the uploaded file
		$upload_file = json_decode($param);

		// get file properties
		$name = $upload_file->name;
		$size = $upload_file->size;
		$tmp_name = $upload_file->tmp_name;

		push::execJS("alert('Upload2: Filename: $name\\nSize: $size');");
	}	
}
