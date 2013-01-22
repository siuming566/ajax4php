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

if ($token != a4p_sec::shiftString($_SESSION["a4p._map"], $method . $controller)) {
	echo "Bad token";
	exit();
}

require_once "$controller.class.php";
// reload session data
$sid = session_id();
session_write_close();
session_start();

$obj = a4p::Controller(basename($controller));

$valid = false;
if (property_exists($obj, "enableAjaxCall"))
	if ($obj->enableAjaxCall == true)
		$valid = true;

if (!$valid) {
	echo "Class not ajax enable";
	exit();
}

$polling = false;
if (isset($_GET["poll_id"])) {
	push::create($_GET["poll_id"]);
	$polling = true;
}

try {
    echo "@" . $obj->$method($param);
} catch (Exception $e) {
    echo $e->getMessage();
}

if ($polling) {
	push::remove($_GET["poll_id"]);
}
