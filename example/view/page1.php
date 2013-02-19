<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<form>
<p>Type something here and press "Go"</p>
<p>
<input type="text" name="textfield1">
<input type="button" value="Go" onclick="a4p.action({controller: 'page1Controller', method: 'go'});">
</p>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
