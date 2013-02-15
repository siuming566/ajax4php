<?php 
//
// container.inc - Container class
//

define('SITE_ROOT', dirname(dirname(__FILE__)));

if (isset($rerender) && $rerender == true)
	set_include_path(dirname($_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"]) . PATH_SEPARATOR . SITE_ROOT . '/class');
else
	set_include_path("." . PATH_SEPARATOR . SITE_ROOT . '/class');

/** @sessionscope */
class Container
{
	function __construct() {
		// initialization here
	}
}
