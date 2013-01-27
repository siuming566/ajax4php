<?php 
//
// framework.inc - A simple PHP AJAX Toolkit
//
require_once "container.inc.php";
require_once "form.inc.php";
require_once "push.inc.php";
require_once "security.inc.php";
require_once "ui.inc.php";

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

if (isset($_GET["rerender"]) && $_GET["rerender"] == true) {
	if (isset($_GET["PHPSESSID"]))
		session_id($_GET["PHPSESSID"]);
}

session_cache_limiter("nocache");
session_start();

if (isset($_GET["rerender"]) && $_GET["rerender"] == true) {
	$_COOKIE = $_SESSION["a4p._cookie"];
}

if (!isset($_SESSION["a4p._map"])) {
	$_SESSION["a4p._map"] = a4p_sec::randomString(26 * 2 + 10);
}

class a4p
{
	public static function Controller($classpath)
	{
		$classname = basename($classpath);
		if (!class_exists($classpath))
			require_once "$classpath.class.php";

		$instance = new $classname();
		if (method_exists($instance, 'init'))
			$instance->init();
		
		return $instance;
	}

	private static $viewarray = array();

	public static function Model($classpath)
	{
		$classname = basename($classpath);
		if (!class_exists($classpath)) {
			require_once "$classpath.class.php";
			// reload session data
			$sid = session_id();
			session_write_close();
			session_start();
		}

		$class = new ReflectionClass($classname);
		$comment = $class->getDocComment();
		
		$scope = "request";
		if (strpos($comment, "@viewscope") !== false)
			$scope = "view";
		if (strpos($comment, "@sessionscope") !== false)
			$scope = "session";

		if ($scope == "request")
			$instance = new $classname();

		if ($scope == "view") {
			if (!a4p::isPostBack() && !a4p::isAjaxCall() && !in_array($classname, a4p::$viewarray)) {
				$instance = new $classname();
				$_SESSION["a4p." . $classname] = $instance;
				a4p::$viewarray[] = $classname;
			}
		}

		if ($scope == "view" || $scope == "session") {
			if (!isset($_SESSION["a4p." . $classname]))	{
				$instance = new $classname();
				$_SESSION["a4p." . $classname] = $instance;
			} else
				$instance = $_SESSION["a4p." . $classname];
		}
		
		return $instance;
	}
	
	private static $js_name = "a4p";

	public static function loadScript()
	{
		$phpself = $_SERVER["PHP_SELF"];
		$phpsessid = session_id();
		$phpquery = $_SERVER["QUERY_STRING"];
		$prefix = "/" . str_replace("\\", "/", dirname(substr( __FILE__, strlen(realpath($_SERVER["DOCUMENT_ROOT"])) + 1)));
		print <<< END
<link href="$prefix/framework.css" type="text/css" rel="Stylesheet" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" src="http://jquery-json.googlecode.com/files/jquery.json-2.4.min.js"></script>
<script type="text/javascript" src="$prefix/framework.js"></script>
<script type="text/javascript">
a4p.init('$prefix', '$phpself', '$phpsessid', '$phpquery');
ui.init(a4p);
</script>
END;
	}
	
	public static function localScript($param)
	{
		$phpself = $_SERVER["PHP_SELF"];
		$phpsessid = session_id();
		$phpquery = $_SERVER["QUERY_STRING"];
		$prefix = "/" . str_replace("\\", "/", dirname(substr( __FILE__, strlen(realpath($_SERVER["DOCUMENT_ROOT"])) + 1)));
		$param1 = $param;
		$param2 = $param . "ui";
		print <<< END
<script type="text/javascript">
$param1 = a4p.setup('$prefix', '$phpself', '$phpsessid', '$phpquery');
$param2 = ui.setup($param1);
</script>
END;
		global $ui;
		$ui = $param2;
		self::$js_name = $param;
	}
	
	public static function setAuth($param)
	{
		if ($param == true)
			$_SESSION["a4p._auth"] = true;
		else
			unset($_SESSION["a4p._auth"]);
	}
	
	public static function isLoggedIn()
	{
		return isset($_SESSION["a4p._auth"]) && ($_SESSION["a4p._auth"] == true);
	}

	public static function isPostBack()
	{
		if (isset($_GET["rerender"]) && $_GET["rerender"] == "true")
			return true;
		return false;
	}

	public static function isAjaxCall()
	{
		global $ajaxcall;
		if (isset($ajaxcall) && $ajaxcall === true)
			return true;
		return false;
	}

	private static function processBuffer($buffer, $js_call)
	{
		$pos = -1;
		while ($pos = strpos($buffer, $js_call, $pos + 1))
		{
			$controller_start = strpos($buffer, "controller:", $pos) + strlen("controller:");
			if ($controller_start === false || $controller_start - $pos > 100)
				continue;
			$controller_end1 = strpos($buffer, ",", $controller_start);
			$controller_end2 = strpos($buffer, "}", $controller_start);
			if ($controller_end1 === false)
				$controller_end1 = $controller_end2;
			$controller_end = $controller_end1 < $controller_end2 ? $controller_end1 : $controller_end2;
			if ($controller_end === false || $controller_end - $controller_start > 100)
				continue;
			$controller_raw = substr($buffer, $controller_start, $controller_end - $controller_start);
			$controller = trim(str_replace("'", "", $controller_raw));
			
			$method_start = strpos($buffer, "method:", $pos) + strlen("method:");
			if ($method_start === false || $method_start - $pos > 100)
				continue;
			$method_end1 = strpos($buffer, ",", $method_start);
			$method_end2 = strpos($buffer, "}", $method_start);
			if ($method_end1 === false)
				$method_end1 = $method_end2;
			$method_end = $method_end1 < $method_end2 ? $method_end1 : $method_end2;
			if ($method_end === false || $method_end - $method_start > 100)
				continue;
			$method_raw = substr($buffer, $method_start, $method_end - $method_start);
			$method = trim(str_replace("'", "", $method_raw));

			$token = a4p_sec::shiftString($_SESSION["a4p._map"], $method . $controller);

			$pos += strlen($js_call);
			$buffer = substr($buffer, 0, $pos) . "token: '$token', " . substr($buffer, $pos);
		}
		return $buffer;
	}

	public static function postProcess($buffer)
	{
		global $ui;
		$buffer = self::processBuffer($buffer, self::$js_name . ".action({");
		$buffer = self::processBuffer($buffer, self::$js_name . ".call({");
		$buffer = self::processBuffer($buffer, $ui . ".fileupload({");
		return $buffer;
	}
}

a4p::Model("Container");

$ui = "ui";

if (!a4p::isAjaxCall())
	ob_start("a4p::postProcess");
