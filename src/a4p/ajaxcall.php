<?php
//
// ajaxcall.php - Handle PHP AJAX requests
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

$controller = $_GET["controller"];
$method = $_GET["method"];
$param = $_GET["param"];
$token = $_GET["token"];
$_GET["rerender"] = "true";

$ajaxcall = true;

require_once "framework.inc.php";
require_once "nocache.inc.php";

if ($controller != "ui" && $token != a4p_sec::shiftString($_SESSION["a4p._map"], $method . $controller)) {
	echo "Bad token";
	exit();
}

include_once "$controller.class.php";

$_class = new ReflectionClass(basename($controller));
$comment = $_class->getDocComment();
		
if (strpos($comment, "@ajaxenable") == false) {
	echo "Class not ajax enable";
	exit();
}

$_method = $_class->getMethod($method);
$comment = $_method->getDocComment();

if (strpos($comment, "@ajaxcall") == false) {
	echo "Not a ajax method";
	exit();
}

// reload session data
$sid = session_id();
session_write_close();
session_start();

$class = a4p::Controller(basename($controller));

$polling = false;
if (isset($_GET["poll_id"])) {
	push::create($_GET["poll_id"]);
	$polling = true;
}

try {
    echo "@" . $class->$method($param);
} catch (Exception $e) {
    echo $e->getMessage();
}

if ($polling) {
	push::remove($_GET["poll_id"]);
}
