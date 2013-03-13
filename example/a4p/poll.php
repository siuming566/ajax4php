<?php
//
// poll.php - Push Javascript
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

require_once "nocache.inc.php";
require_once "config.inc.php";
require_once "push.inc.php";

if (config::$tmp_path == null)
	config::$tmp_path = session_save_path();

echo push::poll($_POST["poll_id"], $_POST["pos"], $_POST["feed"]);
