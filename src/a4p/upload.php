<?php

require_once "security.inc.php";
require_once "config.inc.php";

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

if (config::$tmp_path == null)
	config::$tmp_path = session_save_path();

$yesterday = strtotime(config::$tmp_expire_time);
foreach (glob(config::$tmp_path . DIRECTORY_SEPARATOR . "upload_*") as $oldfile) {
	if (filemtime($oldfile) < $yesterday)
		unlink($oldfile);
}

$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "upload_" . a4p_sec::randomString(32);
move_uploaded_file($_FILES["file"]["tmp_name"], $filename);
$_FILES["file"]["tmp_name"] = $filename;

echo "@" . json_encode($_FILES["file"]);
