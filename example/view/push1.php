<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
<script type="text/javascript">
a4p.onBusy(function () {
	var div = document.getElementById('status');
	div.innerHTML = 'Waiting ...';
});

a4p.onIdle(function () {
	var div = document.getElementById('status'); 
	div.innerHTML = '';
});
</script>
</head>
<body>
<form>
<p>
<input type="button" value="Start Push" onclick="a4p.action({method: 'start', push: true});">
<br/>
<textarea id="outputtext" rows="12" style="width: 300px;"></textarea>
<br/>
<div id="progressbar" style="width: 0px; height: 20px; background-color: blue;"></div>
<div id="status"></div>
</p>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
