<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class filedownload1Controller extends Controller
{
	/** @ajaxcall */
	public function download()
	{
		// create a temp filename for holding the download content
		$file = a4p::createDownloadFile();

		// write somthing to the download file
		file_put_contents($file, "testing ...");

		// redirect the client to download the file, put required header here
		return a4p::redirectDownload($file, array(
			"Content-disposition: attachment; filename=test.txt",
			"Content-type: text/plain"));
	}	
}
