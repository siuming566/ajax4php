<?php 
//
// routing.inc - Routing
//

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
				if (!self::endsWith($classpath, '.php')) {
					global $routed;
					$routed = true;
					require "framework.inc.php";
					global $controller;
					$controller = a4p::Controller($classpath);
					if (method_exists($controller, "pageLoad"))
						$controller->pageLoad();
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

	private static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0)
			return true;

		return (substr($haystack, -$length) === $needle);
	}
}
