<?php a4p::localScript("usercontrol4"); // add local script tags. give local script a unique namespace ?>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" /> 
<form id="form2">
<?php $layout3 = layout::vertical("100%", "100%", "30px,*,*", 2)->begin("border-bottom: 1px solid lightgrey"); // pad 2 pixels for border ?>
Title
<?php $layout3->next("border-bottom: 1px solid lightgrey"); ?>
<div id="panel2">
<p>
<input type="text" name="textfield1" value="<?= $model->textfield1 ?>">
<input type="button" value="Add" onclick="usercontrol4.action({controller: 'usercontrol4Controller', method: 'add', rerender: 'panel2', formname: 'form2'});">
</p>
</div>
<?php $layout3->next(); ?>
<div id="panel3">
<p>
Server time is <?= $controller->getTime() ?>
<input type="button" value="Refresh" onclick="usercontrol4.rerender('panel3');">
</p>
</div>
<?php $layout3->end(); ?>
</form>
