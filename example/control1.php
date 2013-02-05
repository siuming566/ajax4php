<?php
// include the framework
require_once "a4p/framework.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<form>
<div>
<?php 
	$control1 = a4p::Controller("usercontrol1Controller");
	if (!a4p::isPostBack())
		$control1->setValue("1");
	include "usercontrol1.php" 
?>
</div>
<p>
<div>
<?php 
	$control2 = a4p::Controller("usercontrol2Controller");
	if (!a4p::isPostBack()) {
		$control2->setX("2");
		$control2->setY("3");
	}
	include "usercontrol2.php" 
?>
</div>
</form>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
