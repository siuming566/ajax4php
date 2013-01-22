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
<p>
<input type="button" value="Start Push" onclick="a4p.action({controller: 'push1Controller', method: 'start', push: true});">
<br/>
<textarea id="outputtext" rows="12" style="width: 300px;"></textarea>
<br/>
<div id="progressbar" style="width: 0px; height: 20px; background-color: blue;"></div>
</p>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
