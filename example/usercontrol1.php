<?php
// include the framework
require_once "a4p/framework.inc.php";

// load the controller and model
$model = a4p::Model("usercontrol1Model");
?>
<?php a4p::localScript("usercontrol1"); // add local script tags. give local script a unique namespace ?>
<form id="usercontrol1form">
<div id="panel1">
This is user control 1
<p>
<input type="text" name="textfield1" value="<?= $model->textfield1 ?>">
<input type="button" value="Add" onclick="usercontrol1.action({controller: 'usercontrol1Controller', method: 'add', rerender: 'panel1', formname: 'usercontrol1form'});">
</p>
</div>
</form>
