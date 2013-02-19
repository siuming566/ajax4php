<?php
//
// control.php - Load control
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

require_once "framework.inc.php";
require_once "nocache.inc.php";

$control = $_POST["control"];
$_SERVER["REQUEST_URI"] = "control:" . $control;
a4p::loadControl($control);
