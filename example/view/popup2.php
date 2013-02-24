<?php a4p::localScript("popup2"); // add local script tags. give local script a unique namespace ?>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" /> 
Popup here, id is <?= $_GET["id"] ?>
<form id="form2">
<div id="panel2">
<p>
<input type="text" name="textfield1" value="<?= $model->textfield1 ?>">
<input type="button" value="Add" onclick="popup2.action({method: 'add', rerender: 'panel2', formname: 'form2'});">
</p>
</div>
<div id="panel3">
<p>
Server time is <?= $controller->getTime() ?>
<input type="button" value="Refresh" onclick="popup2.rerender('panel3');">
</p>
</div>
</form>
