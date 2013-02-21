<?php 
//
// routing.inc - Routing
//
require "framework.inc.php";

class routing
{
	public static function setup($routes)
	{
		$prefix = dirname($_SERVER["PHP_SELF"]);

		global $rerender;
		if ($rerender == true)
			$prefix = dirname($prefix);

		if ($prefix == "." || $prefix == "\\" || $prefix == "/")
			$prefix = "";

		global $uri;
		$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
		$uri = substr($uri_parts[0], strlen($prefix) + 1);
		$match = false;
		foreach ($routes as $route => $classpath) {
			if (preg_match("/^" . $route . "(\?.*)*$/", $uri)) {
				global $controller;
				$controller = a4p::Controller($classpath);
				if (method_exists($controller, 'pageLoad'))
					$controller->pageLoad();
				$match = true;
				break;
			}
		}

		if (!$match) {
			global $rerender;
			if (isset($rerender) && $rerender == true)
				require $_SERVER["DOCUMENT_ROOT"] . $_SERVER['REQUEST_URI'];
			else
				header("Location: /notfound.html");
		}
	}
}
