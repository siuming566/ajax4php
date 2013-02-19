<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<p>
<input type="button" value="Popup" onclick="ui.popup('popup2?id=12', 400, 300).onLoad(function () { alert('You can add handler to onLoad event'); }).onClose(function () { alert('You can add handler to onClose event'); });">
</p>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
