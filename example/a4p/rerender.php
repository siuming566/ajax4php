<?php
//
// rerender.php - Rerender components
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

$page = $_SERVER["REQUEST_URI"] = $_POST["page"];
$ids = $_POST["id"];
$rerender = true;

$contents = array();
$arr = explode(",", $ids);

ob_start();
if (strncmp($page, "control:", 8) == 0) {
	$_POST["control"] = substr($page, 8);
	require "control.php";
}
else
	require dirname(dirname(__FILE__)) . "/route.php";;
ob_end_flush();
$html = ob_get_contents();
ob_end_clean();

$doctype = "<!DOCTYPE HTML";

if (strncasecmp($html, $doctype, strlen($doctype)) != 0) {
	$html4doctype = <<< END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
END;
	$html = $html4doctype . $html;
}

libxml_use_internal_errors(true);

$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
$dom = new DOMDocument("1.0", "utf-8");
$dom->preserveWhiteSpace = false;
$dom->loadHTML($html);
foreach ($arr as $id) {
	$tag = $dom->getElementById($id);
	$contents[$id] = str_replace('&#13;', '', $dom->saveXML($tag));
}

$json = json_encode($contents);
echo "@" . $json;
