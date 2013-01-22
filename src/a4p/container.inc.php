<?php 
//
// container.inc - Container class
//

define('SITE_ROOT', dirname(dirname(__FILE__)));

ini_set('include_path', '.' . PATH_SEPARATOR . SITE_ROOT . '/class');

class Container
{
	function __construct() {
		// initialization here
	}
}
