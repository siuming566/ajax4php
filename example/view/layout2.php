<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<?php layout::bodymargin(0); ?>
<?php $layout1 = layout::vertical("100%", "100%", "100px,*,20%", 3)->begin("border-bottom: 1px solid lightgrey;"); // pad 3 pixels for borders ?>
Header - Fixed 100px
<?php $layout1->next("border-bottom: 1px solid lightgrey;"); ?>
	<?php $layout2 = layout::horizontal("100%", "100%", "150px,*", 1)->begin("border-right: 1px solid lightgrey;"); // pad 1 pixel for border  ?>
	Menu - Fixed 150px
	<?php $layout2->next(); ?>
	<?php a4p::loadControl("usercontrol4Controller") ?>
	<?php $layout2->end(); ?>
<?php $layout1->next("border-bottom: 1px solid red;"); ?>
Footer - 20%
<p><a href="index.html">Back to index</a></p>
<?php $layout1->end(); ?>
</body>
</html>
