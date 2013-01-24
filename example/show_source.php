<?php

$filename = basename($_SERVER['QUERY_STRING']);

if (file_exists(__DIR__ . "/" . $filename . ".php"))
	show_source(__DIR__ . "/" . $filename . ".php");
else if (file_exists(__DIR__ . "/class/" . $filename . ".class.php"))
	show_source(__DIR__ . "/class/" . $filename . ".class.php");
