<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
</head>
<body> 
<?php

$filename = basename($_SERVER['QUERY_STRING']);

if (file_exists(__DIR__ . "/" . $filename . ".php"))
	show_source(__DIR__ . "/" . $filename . ".php");
else if (file_exists(__DIR__ . "/view/" . $filename . ".php"))
	show_source(__DIR__ . "/view/" . $filename . ".php");
else if (file_exists(__DIR__ . "/class/" . $filename . ".class.php"))
	show_source(__DIR__ . "/class/" . $filename . ".class.php");
else if (file_exists(__DIR__ . "/resource/" . $filename . ".ini"))
	show_source(__DIR__ . "/resource/" . $filename . ".ini");
?>
</body>
</html>
