<?php
//
// rerender.php - Rerender components
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

$page = $_POST["page"];
$ids = $_POST["id"];
$rerender = true;

$contents = array();
$arr = explode(",", $ids);

ob_start();
require $_SERVER["DOCUMENT_ROOT"] . $page;
ob_end_flush();
$html = ob_get_contents();
ob_end_clean();

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->loadHTML($html);
$tags = $dom->getElementsByTagName("div");
foreach ($tags as $tag) {
	$id = $tag->getAttribute("id");
	if ($id != "" && in_array($id, $arr)) {
		$contents[$id] = $dom->saveHTML($tag);
		if (count(array_keys($contents)) == count($arr))
			break;
	}
}

$json = json_encode($contents);
echo $json;
