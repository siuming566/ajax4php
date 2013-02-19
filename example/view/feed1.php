<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<form>
<p>
<input type="button" value="Ask Question" onclick="a4p.action({controller: 'feed1Controller', method: 'ask', push: true});">
<br/>
<textarea id="outputtext2" rows="12" style="width: 300px;"></textarea>
</p>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
