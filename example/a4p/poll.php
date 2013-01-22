<?php
//
// poll.php - Push Javascript
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

require_once "nocache.inc.php";
require_once "push.inc.php";

echo push::poll($_POST["poll_id"], $_POST["pos"], $_POST["feed"]);
