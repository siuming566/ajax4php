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
<?php $layout1 = layout::vertical("100%", "100%", "100px,*,20%", 2)->begin(); // pad 2 pixels for border ?>
Header - Fixed 100px
<?php $layout1->next("border-top: 1px solid lightgrey"); ?>
	<?php $layout2 = layout::horizontal("100%", "100%", "150px,*", 1)->begin(); // pad 1 pixels for border  ?>
	Menu - Fixed 150px
	<?php $layout2->next("border-left: 1px solid lightgrey"); ?>
	<?php a4p::loadControl("usercontrol4.php") ?>
	<?php $layout2->end(); ?>
<?php $layout1->next("border-top: 1px solid lightgrey"); ?>
Footer - 20%
<p><a href="index.html">Back to index</a></p>
<?php $layout1->end(); ?>
</body>
</html>
