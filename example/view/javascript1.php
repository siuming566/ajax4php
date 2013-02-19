<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<p>
Call PHP code from Javascript directly
</p>
<p>
<input type="button" value="Try" onclick="myfunc();">
</p>
<script type="text/javascript">
function myfunc()
{
	var msg = a4p.call({controller: 'javascript1Controller', method: 'getMessage', param: a4p.JSONEncode({ a: 'Hello', b: 'World'})});
	alert(msg);
}
</script>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
