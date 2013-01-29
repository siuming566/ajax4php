<?php
//
// rerender.php - Rerender components
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

$page = $_POST["page"];
$sessid = $_POST["sessid"];
$ids = $_POST["id"];

require_once "nocache.inc.php";

session_start();
$_SESSION["a4p._cookie"] = $_COOKIE;
session_write_close();

$contents = array();
$arr = explode(",", $ids);

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
	$protocol = "https";
else
	$protocol = "http";

if (strpos($_SERVER["HTTP_HOST"], ":" . $_SERVER["SERVER_PORT"]) == false)
	$host = $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"];
else
	$host = $_SERVER["HTTP_HOST"];

$url = $protocol . "://" . $host . $page . "?PHPSESSID=" . $sessid . "&rerender=true&" . $_SERVER['QUERY_STRING'];

libxml_use_internal_errors(true);

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->loadHTMLFile($url);

$tags = $dom->getElementsByTagName("div");
foreach ($tags as $tag) {
	$id = $tag->getAttribute("id");
	if ($id != "" && in_array($id, $arr)) {
		$content = new DOMDocument();
		$content->appendChild($content->importNode($tag, true));
		$contents[$id] = $content->saveHTML();
		if (count(array_keys($contents)) == count($arr))
			break;
	}
}

$json = json_encode($contents);
echo $json;
