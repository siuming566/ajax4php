<?php
// include the framework
require_once "a4p/framework.inc.php";

// load the controller and model
$controller = a4p::Controller("datatable1Controller");
$model = a4p::Model("datatable1Model");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php a4p::loadScript(); // Need to load required script in header ?>
<style>
table
{
	border-collapse: collapse;
}
table, td, th
{
	border: 1px solid black;
}
</style>
</head>
<body>
<form>
<?php $table1 = ui::dataTable("table1", $model->data, 20) ?>
<table>
<tr>
	<?php $table1->headerColumn("Column A", "colA") ?>
	<?php $table1->headerColumn("Column B", "colB") ?>
	<?php $table1->headerColumn("Column C", "colC") ?>
	<?php $table1->headerColumn("Column D", "colD") ?>
</tr>
<?php foreach ($table1->data as $row) { ?>
<tr>
	<td><?= $row["colA"] ?></td>
	<td><?= $row["colB"] ?></td>
	<td><?= $row["colC"] ?></td>
	<td><?= $row["colD"] ?></td>
</tr>
<?php } ?>
</table>
<p>
<input type="button" value="|&lt;" onclick="<?php $table1->firstPageJS() ?>" />&#160;
<input type="button" value="&lt;" onclick="<?php $table1->previousPageJS() ?>" />&#160;
<?php $table1->selectPage() ?>&#160;
<input type="button" value="&gt;" onclick="<?php $table1->nextPageJS() ?>" />&#160;
<input type="button" value="&gt;|" onclick="<?php $table1->lastPageJS() ?>" />
</p>
<?php $table1->done() ?>
<p><a href="index.html">Back to index</a></p>
</form>
</body>
</html>
