<?php
// include the framework
require_once "a4p/framework.inc.php";

// load the model
$model = a4p::Model("page1Model");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<p>You typed: <?= $model->textfield1 ?></p>
<p><a href="page1.php">Back to page 1</a></p>
<p><a href="index.html">Back to index</a></p>
</body>
</html>
