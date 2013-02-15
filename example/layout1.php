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
<?php $panel1 = layout::vertical("100%", "100%", "100px,*,20%")->begin("border-bottom: 1px solid lightgrey;"); ?>
Header - Fixed 100px
<?php $panel1->next(); ?>
	<?php $panel2 = layout::horizontal("100%", "100%", "150px,*")->begin("border-right: 1px solid lightgrey;"); ?>
	Menu - Fixed 150px
	<?php $panel2->next(); ?>
	<?php a4p::loadControl(SITE_ROOT . "/usercontrol3.php") ?>
	<?php $panel2->end(); ?>
<?php $panel1->next("border-top: 1px solid lightgrey;"); ?>
Footer - 20%
<p><a href="index.html">Back to index</a></p>
<?php $panel1->end(); ?>
</body>
</html>
