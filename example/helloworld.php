<?php
// include the framework
require_once "a4p/framework.inc.php";

// load the controller and model
$controller = a4p::Controller("helloworldController");
$model = a4p::Model("helloworldModel");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<p><?= $model->message ?></p>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
