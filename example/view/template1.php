<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
</head>
<body>
<?php $layout1 = layout::vertical("100%", "100%", "100px,*,20%")->begin(); ?>
<?php template::section("header"); ?>
<?php $layout1->next(); ?>
	<?php $layout2 = layout::horizontal("100%", "100%", "150px,*")->begin(); ?>
	<?php template::section("menu"); ?>
	<?php $layout2->next(); ?>
	<?php template::section("content"); ?>
	<?php $layout2->end(); ?>
<?php $layout1->next(); ?>
<?php template::section("footer"); ?>
<?php $layout1->end(); ?>
</body>
</html>
