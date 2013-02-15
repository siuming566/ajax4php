<?php
// include the framework
require_once "a4p/framework.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<?php $layout1 = layout::vertical("100%", "100%", "100px,*,20%")->begin(); ?>
Header - Fixed 100px
<?php $layout1->next(); ?>
	<?php $layout2 = layout::horizontal("100%", "100%", "150px,*")->begin(); ?>
	Menu - Fixed 150px
	<?php $layout2->next(); ?>
	<?php a4p::loadControl("usercontrol3.php") ?>
	<?php $layout2->end(); ?>
<?php $layout1->next(); ?>
Footer - 20%
<p><a href="index.html">Back to index</a></p>
<?php $layout1->end(); ?>
</body>
</html>
