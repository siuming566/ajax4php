<?php
//
// rerender.php - Rerender components
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

$page = $_SERVER["PHP_SELF"] = $_POST["page"];
$ids = $_POST["id"];
$rerender = true;

$contents = array();
$arr = explode(",", $ids);

ob_start();
require $_SERVER["DOCUMENT_ROOT"] . $page;
ob_end_flush();
$html = ob_get_contents();
ob_end_clean();

$doctype = "<!DOCTYPE HTML";

if (substr($html, 0, strlen($doctype)) != $doctype) {
	$html4doctype = <<< END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
END;
	$html = $html4doctype . $html;
}

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->loadHTML($html);
foreach ($arr as $id) {
	$tag = $dom->getElementById($id);
	$contents[$id] = $dom->saveXML($tag);
}

$json = json_encode($contents);
echo $json;
