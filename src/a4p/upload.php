<?php

require_once "security.inc.php";

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

$yesterday = strtotime('-1 days');
foreach (glob(session_save_path() . DIRECTORY_SEPARATOR . "upload_*") as $oldfile) {
	if (filemtime($oldfile) < $yesterday)
		unlink($oldfile);
}

$filename = session_save_path() . DIRECTORY_SEPARATOR . "upload_" . a4p_sec::randomString(32);
move_uploaded_file($_FILES["file"]["tmp_name"], $filename);
$_FILES["file"]["tmp_name"] = $filename;

echo "@" . json_encode($_FILES["file"]);
