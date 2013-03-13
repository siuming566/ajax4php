<?php
//
// download.php - Download file
//

$id = $_GET["id"];

require_once "config.inc.php";
require_once "session.inc.php";
require_once "nocache.inc.php";

session_cache_limiter("nocache");
session_start();

if (config::$tmp_path == null)
	config::$tmp_path = session_save_path();

a4p_session::$sid = session_id();
a4p_session::init();

session_write_close();

$info = a4p_session::get("a4p.download_" . $id);

if ($info != null) {
	foreach ($info["headers"] as $value)
		header($value);
	readfile($info["filename"]);
	unlink($info["filename"]);
	a4p_session::remove("a4p.download_" . $id);
}
