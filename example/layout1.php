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
<?php $panel1 = layout::vertical("100%", "100%", "100px,*,20%"); ?>
<p>Header - Fixed 100px</p>
<?php $panel1->next(); ?>
	<?php $panel2 = layout::horizontal("100%", "100%", "150px,*"); ?>
	<p>menu</p>
	<?php $panel2->next(); ?>
		<?php $panel3 = layout::vertical("100%", "100%", "10%,*"); ?>
		<p>Title</p>
		<?php $panel3->next(); ?>
		<?php include "popup2.php" ?>
		<?php $panel3->end(); ?>
	<?php $panel2->end(); ?>
<?php $panel1->next(); ?>
<p>Footer - 20%</p>
<p><a href="index.html">Back to index</a></p>
<?php $panel1->end(); ?>
</body>
</html>
