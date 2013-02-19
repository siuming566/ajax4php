<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
<script type="text/javascript">
function loadUserControl1() {
	ui.loadControl('usercontrol1Controller', 'place1');
}
function loadUserControl2() {
	a4p.call({controller: 'control2Controller', method: 'initUserControl2'});
	ui.loadControl('usercontrol2Controller', 'place2');
}
</script>
</head>
<body>
<form>
<input type="button" value="Load User Control 1" onclick="loadUserControl1();" />
<div id="place1">
</div>
<p>
<input type="button" value="Load User Control 2" onclick="loadUserControl2();" />
<div id="place2">
</div>
</form>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
