<?php
// include the framework
require_once "a4p/framework.inc.php";

// load the controller and model
$controller = a4p::Controller("usercontrol3Controller");
$model = a4p::Model("usercontrol3Model");
?>
<?php a4p::localScript("usercontrol3"); // add local script tags. give local script a unique namespace ?>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" /> 
<form id="form2">
<?php $layout3 = layout::vertical("100%", "100%", "30px,*,*")->begin(); ?>
Title
<?php $layout3->next(); ?>
<div id="panel2">
<p>
<input type="text" name="textfield1" value="<?= $model->textfield1 ?>">
<input type="button" value="Add" onclick="usercontrol3.action({controller: 'usercontrol3Controller', method: 'add', rerender: 'panel2', formname: 'form2'});">
</p>
</div>
<?php $layout3->next(); ?>
<div id="panel3">
<p>
Server time is <?= $controller->getTime() ?>
<input type="button" value="Refresh" onclick="usercontrol3.rerender('panel3');">
</p>
</div>
<?php $layout3->end(); ?>
</form>
