<?php 
//
// routing.inc - Routing
//
require_once "common.inc.php";

class routing
{
	private static $routes = array();

	public static function add($_routes)
	{
		self::$routes = array_merge(self::$routes, $_routes);
	}

	public static function setup($_routes)
	{
		self::$routes = array_merge(self::$routes, $_routes);

		$prefix = dirname($_SERVER["PHP_SELF"]);

		global $rerender;
		if ($rerender == true)
			$prefix = dirname($prefix);

		if ($prefix == "." || $prefix == "\\" || $prefix == "/")
			$prefix = "";

		global $uri;
		$uri_parts = explode("?", $_SERVER["REQUEST_URI"], 2);
		$uri = substr($uri_parts[0], strlen($prefix) + 1);
		$match = false;
		foreach (self::$routes as $route => $classpath) {
			if (preg_match('/^' . $route . '(\?.*)*$/', $uri)) {
				if (!endsWith($classpath, '.php')) {
					$param = explode("@", $classpath);
					$classname = $param[0];
					if (isset($param[1])) {
						global $ajaxcall;
						$ajaxcall = true;
						require "framework.inc.php";
						global $controller;
						$controller = a4p::Controller($classname);
						$method = $param[1];
						try {
							echo $controller->$method();
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					} else {
						global $routed;
						$routed = true;
						require "framework.inc.php";
						global $controller;
						$controller = a4p::Controller($classname);
						$controller->pageLoad();
					}
				} else {
					require $classpath;
				}
				$match = true;
				break;
			}
		}

		if (!$match) {
			global $rerender;
			if (isset($rerender) && $rerender == true)
				require $_SERVER["DOCUMENT_ROOT"] . $_SERVER["REQUEST_URI"];
			else {
				header("HTTP/1.0 404 Not Found");
				echo "<h1>Not Found</h1>";
			}
		}
	}
}
