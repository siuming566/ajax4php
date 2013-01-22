<?php
//
// ajaxcall.php - Handle PHP AJAX requests
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

$classpath = $_GET["classname"];
$method = $_GET["method"];
$param = $_GET["param"];
$token = $_GET["token"];
$_GET["rerender"] = "true";

$ajaxcall = true;

require_once "framework.inc.php";
require_once "nocache.inc.php";
include_once "db.inc.php";

if ($token != a4p_sec::shiftString($_SESSION["a4p._map"], $method . $classpath)) {
	echo "Bad token";
	exit();
}

if ($classpath != "ui") {
	require_once "$classpath.class.php";
	// reload session data
	$sid = session_id();
	session_write_close();
	session_start();
}

$obj = a4p::loadClass(basename($classpath));

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
