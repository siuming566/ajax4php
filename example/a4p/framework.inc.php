<?php 
//
// framework.inc - A simple PHP AJAX Toolkit
//
require_once "common.inc.php";
require_once "config.inc.php";
require_once "session.inc.php";
require_once "container.inc.php";
require_once "form.inc.php";
require_once "push.inc.php";
require_once "security.inc.php";
require_once "ui.inc.php";
require_once "controller.inc.php";
require_once "model.inc.php";
require_once "layout.inc.php";
require_once "template.inc.php";
require_once "language.inc.php";
require_once "db.inc.php";
require_once "debug.inc.php";

session_cache_limiter("nocache");
session_start();

if (config::$tmp_path == null)
	config::$tmp_path = session_save_path();

a4p_session::$sid = session_id();
a4p_session::$global = $_SESSION;
a4p_session::init();

if (!isset($_SESSION["a4p._map"]))
	$_SESSION["a4p._map"] = a4p_sec::randomString(26 * 2 + 10);

a4p_sec::$map = $_SESSION["a4p._map"];
a4p_sec::$auth = isset($_SESSION["a4p._auth"]) && ($_SESSION["a4p._auth"] == true);

session_write_close();
$_SESSION = array();

class a4p
{
	private static $requestscopestack = array();
	private static $viewscopestack = array();

	public static function Controller($classpath)
	{
		$classname = basename($classpath);
		if (!class_exists($classpath))
			require_once "$classpath.class.php";

		if (!isset(a4p::$requestscopestack["a4p." . $classname])) {
			$instance = new $classname();
			if (property_exists($instance, 'name'))
				$instance->name = $classpath;
			a4p::$requestscopestack["a4p." . $classname] = $instance;
		} else
			$instance = a4p::$requestscopestack["a4p." . $classname];
		
		return $instance;
	}

	public static $currentModel;

	public static function View($viewpath, $env = array())
	{
		global $controller;
		global $model;
		$model = a4p::$currentModel;
		require SITE_ROOT . "/view/" . $viewpath;
	}

	public static function Model($classpath, $defaults = null)
	{
		$classname = basename($classpath);
		if (!class_exists($classpath))
			require_once "$classpath.class.php";

		$class = new ReflectionClass($classname);
		$comment = $class->getDocComment();
		
		$scope = "request";
		if (strpos($comment, "@viewscope") !== false)
			$scope = "view";
		if (strpos($comment, "@sessionscope") !== false)
			$scope = "session";

		if ($scope == "request") {
			if (!isset(a4p::$requestscopestack["a4p." . $classname])) {
				if ($defaults != null)
					$instance = new $classname($defaults);
				else
					$instance = new $classname();
				a4p::$requestscopestack["a4p." . $classname] = $instance;
			} else
				$instance = a4p::$requestscopestack["a4p." . $classname];
		}

		if ($scope == "view") {
			if (!a4p::isPostBack() && !a4p::isAjaxCall() && !in_array($classname, a4p::$viewscopestack)) {
				if ($defaults != null)
					$instance = new $classname($defaults);
				else
					$instance = new $classname();
				a4p_session::set("a4p." . $classname, $instance);
				a4p::$viewscopestack[] = $classname;
			}
		}

		if ($scope == "view" || $scope == "session") {
			if (!a4p_session::exists("a4p." . $classname))	{
				if ($defaults != null)
					$instance = new $classname($defaults);
				else
					$instance = new $classname();
				a4p_session::set("a4p." . $classname, $instance);
			} else
				$instance = a4p_session::get("a4p." . $classname);
		}

		a4p::$currentModel = $instance;
		
		return $instance;
	}
	
	public static function Reset($classpath)
	{
		$classname = basename($classpath);
		
		if (isset(a4p::$requestscopestack["a4p." . $classname]))
			unset(a4p::$requestscopestack["a4p." . $classname]);

		if (isset(a4p::$viewscopestack["a4p." . $classname]))
			unset(a4p::$viewscopestack["a4p." . $classname]);

		a4p_session::remove("a4p." . $classname);
	}

	private static $js_name = array();

	public static function loadScript()
	{
		$prefix = "/" . str_replace("\\", "/", dirname(substr(__FILE__, strlen(realpath($_SERVER["DOCUMENT_ROOT"])) + 1)));

		$isIE = false;
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match("/(?i)MSIE [6-8]/", $user_agent) == 0)
			print <<< END
<link href="$prefix/ui.css" type="text/css" rel="Stylesheet" />
END;

		global $routed;
		if (isset($routed) && $routed == true)
			$phpself = $_SERVER["REQUEST_URI"];
		else
			$phpself = $_SERVER["PHP_SELF"];
		
		$phpquery = $_SERVER["QUERY_STRING"];

		global $controller;
		if (isset($controller->name)) {
			$controllername = $controller->name;
			$token = a4p_sec::shiftString(a4p_sec::$map, $controller->name);
		}
		else {
			$controllername = "";
			$token = "";
		}

		print <<< END
<link href="$prefix/framework.css" type="text/css" rel="Stylesheet" />
<link href="$prefix/layout.css" type="text/css" rel="Stylesheet" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jquery-json.googlecode.com/files/jquery.json-2.4.min.js"></script>
<script type="text/javascript" src="$prefix/framework.js"></script>
<script type="text/javascript" src="$prefix/ui.js"></script>
<script type="text/javascript" src="$prefix/layout.js"></script>
<script type="text/javascript">
a4p.init('$prefix', '$phpself', '$phpquery', '$controllername', '$token');
ui.init(a4p);
var layout_info = new Array();
<!-- layout info here -->
</script>
END;

		self::$js_name[] = "a4p";
	}
	
	public static function localScript($param)
	{
		$prefix = "/" . str_replace("\\", "/", dirname(substr(__FILE__, strlen(realpath($_SERVER["DOCUMENT_ROOT"])) + 1)));

		$phpself = $_SERVER["REQUEST_URI"];
		$phpquery = $_SERVER["QUERY_STRING"];

		global $controller;
		if (isset($controller->name)) {
			$controllername = $controller->name;
			$token = a4p_sec::shiftString(a4p_sec::$map, $controller->name);
		}
		else {
			$controllername = "";
			$token = "";
		}
	
		$param1 = $param;
		$param2 = $param . "ui";

		print <<< END
<script type="text/javascript">
$param1 = a4p.setup('$prefix', '$phpself', '$phpquery', '$controllername', '$token');
$param2 = ui.setup($param1);
<!-- layout info here -->
</script>
END;

		global $ui;
		$ui = $param2;
		self::$js_name[] = $param;
	}
	
	public static function setAuth($param)
	{
		$session_started = isset($_SESSION["a4p._map"]);

		if (!$session_started)
			session_start();

		if ($param == true)
			$_SESSION["a4p._auth"] = a4p_sec::$auth = true;
		else {
			a4p_sec::$auth = false;
			unset($_SESSION["a4p._auth"]);
		}

		if (!$session_started) {
			session_write_close();
			$_SESSION = array();
		}
	}
	
	public static function isLoggedIn()
	{
		return a4p_sec::$auth == true;
	}

	public static function isPostBack()
	{
		global $rerender;
		if (isset($rerender) && $rerender == true)
			return true;
		return false;
	}

	public static function isAjaxCall()
	{
		global $ajaxcall;
		if (isset($ajaxcall) && $ajaxcall == true)
			return true;
		return false;
	}

	public static function Container()
	{
		return a4p::Model("Container");
	}

	private static function &processBuffer(&$buffer, $js_call)
	{
		global $controller;
		$max_len = 200;
		$pos = -1;
		while ($pos = strpos($buffer, $js_call, $pos + 1)) {
			$controller_start = strpos($buffer, "controller:", $pos);
			if ($controller_start === false || $controller_start - $pos > $max_len) {
				$controllername = "";
			} else {
				$controller_start += strlen("controller:");
				$controller_end1 = strpos($buffer, ",", $controller_start);
				$controller_end2 = strpos($buffer, "}", $controller_start);
				if ($controller_end1 === false)
					$controller_end1 = $controller_end2;
				$controller_end = $controller_end1 < $controller_end2 ? $controller_end1 : $controller_end2;
				if ($controller_end === false || $controller_end - $controller_start > $max_len)
					continue;
				$controller_raw = substr($buffer, $controller_start, $controller_end - $controller_start);
				$controller_name = trim(str_replace("'", "", $controller_raw));
			}
			
			$method_start = strpos($buffer, "method:", $pos);
			if ($method_start === false || $method_start - $pos > $max_len)
				continue;
			$method_start += strlen("method:");
			$method_end1 = strpos($buffer, ",", $method_start);
			$method_end2 = strpos($buffer, "}", $method_start);
			if ($method_end1 === false)
				$method_end1 = $method_end2;
			$method_end = $method_end1 < $method_end2 ? $method_end1 : $method_end2;
			if ($method_end === false || $method_end - $method_start > $max_len)
				continue;
			$method_raw = substr($buffer, $method_start, $method_end - $method_start);
			$method = trim(str_replace("'", "", $method_raw));

			$token = a4p_sec::shiftString(a4p_sec::$map, $method . $controller_name);

			$pos += strlen($js_call);
			$buffer = substr($buffer, 0, $pos) . "token: '$token', " . substr($buffer, $pos);
		}
		return $buffer;
	}

	public static function &postProcess(&$buffer)
	{
		global $ui;
		foreach(self::$js_name as $name) {
			$buffer = self::processBuffer($buffer, $name . ".action({");
			$buffer = self::processBuffer($buffer, $name . ".call({");
		}
		$buffer = self::processBuffer($buffer, $ui . ".fileupload({");
		$buffer = self::processLayout($buffer);
		$buffer = self::processLanguage($buffer);
		return self::finalize($buffer);
	}

	private static function &processLayout(&$buffer)
	{
		$placement = "<!-- layout info here -->";
		$pos = -1;
		while ($pos = strpos($buffer, $placement, $pos + 1)){
			if (count(layout::$layout_info) > 0) {
				$json = json_encode(layout::$layout_info);
				$bodymargin = layout::$bodymargin;
				$buffer = substr($buffer, 0, $pos) . "var layout_info = layout_info.concat(eval('($json)'));\nvar layout_margin = { bodymargin: $bodymargin };" . substr($buffer, $pos + strlen($placement));
				layout::$layout_info = array();
			} else
				$buffer = substr($buffer, 0, $pos) . substr($buffer, $pos + strlen($placement));
		}
		return $buffer;
	}

	private static function &processLanguage(&$buffer)
	{
		return language::process($buffer);
	}

	public static function &finalize(&$buffer)
	{
		a4p_session::flush();
		return $buffer;
	}

	public static function requireSSL($ssl = true)
	{
		$isHTTPS = isset($_SERVER["HTTPS"]) ? ($_SERVER["HTTPS"] == "on") : false;
		$host = isset($_SERVER["HTTP_X_FORWARDED_HOST"]) ? $_SERVER["HTTP_X_FORWARDED_HOST"] : $_SERVER["SERVER_NAME"];

		if($ssl == true && $isHTTPS == false) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://" . $host . ":" . config::$ssl_port . $_SERVER["REQUEST_URI"]);
			exit();
		}

		if($ssl == false && $isHTTPS == true) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://" . $host . ":" . config::$http_port . $_SERVER["REQUEST_URI"]);
			exit();
		}
	}

	public static function requireAuth()
	{
		if (!a4p::isLoggedIn())
		{
			header("Location: index.php");
			exit();
		}
	}

	public static function loadControl($classpath)
	{
		global $ui, $controller, $model;

		$_ui = $ui;
		$_controller = $controller;
		$_model = $model;

		$controller = a4p::Controller($classpath);
		if (method_exists($controller, 'pageLoad'))
			$controller->pageLoad();

		$model = $_model;
		$controller = $_controller;
		$ui = $_ui;
	}

	public static function createDownloadFile()
	{
		return tempnam(sys_get_temp_dir(), 'a4p');
	}

	public static function redirectDownload($file, $headers)
	{
		$id = a4p_sec::randomString(16);
		$info = array("filename" => $file, "headers" => $headers);
		a4p_session::set("a4p.download_" . $id, $info);
		return "#" . $id;
	}
}

global $ui;
$ui = "ui";

if (!a4p::isAjaxCall())
	ob_start("a4p::postProcess");
else
	ob_start("a4p::finalize");
