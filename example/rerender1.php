<?php
// include the framework
require_once "a4p/framework.inc.php";

// load the controller
$controller = a4p::Controller("rerender1Controller");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<div id="panel1">
<p>
<?= $controller->getTime() ?>
</p>
<p>
<input type="button" value="Refresh" onclick="a4p.rerender('panel1');">
</p>
</div>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
