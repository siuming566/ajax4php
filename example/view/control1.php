<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<form>
<div>
<?php a4p::loadControl("usercontrol1Controller"); ?>
</div>
<p>
<div>
<?php 
	$control = a4p::Controller("usercontrol2Controller");
	if (!a4p::isPostBack()) {
		$control->setX("2");
		$control->setY("3");
	}
	a4p::loadControl("usercontrol2Controller");
?>
</div>
</form>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
