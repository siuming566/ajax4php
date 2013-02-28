<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<?php ui::fileupload("fileupload1Controller", "upload"); ?>
<br/>
<?php ui::fileupload("fileupload1Controller", "upload2", "", "true"); ?>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
