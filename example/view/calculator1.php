<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<form>
<div id="panel1">
<p>
<input name="x" type="text" size="3" value="<?= $model->x ?>">
*
<input name="y" type="text" size="3" value="<?= $model->y ?>">
=
<input name="z" type="text" size="3" value="<?= $model->z ?>">
<input type="button" onclick="a4p.action({method: 'calculate', rerender: 'panel1'});" value="Calculate">
</p>
</div>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
