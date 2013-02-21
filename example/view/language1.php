<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<form>
<div id="panel1">
<p>$sample.message</p>
<p>
<input type="button" value="English" onclick="a4p.action({controller: 'language1Controller', method: 'showEnglish', rerender: 'panel1'});">
</p>
<p>
<input type="button" value="Chinese" onclick="a4p.action({controller: 'language1Controller', method: 'showChinese', rerender: 'panel1'});">
</p>
</div>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
