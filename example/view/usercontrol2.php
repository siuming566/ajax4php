<?php a4p::localScript("usercontrol2"); // add local script tags. give local script a unique namespace ?>
<form id="usercontrol2form">
<div id="panel2">
This is user control 2
<p>
<input name="x" type="text" size="3" value="<?= $model->x ?>">
*
<input name="y" type="text" size="3" value="<?= $model->y ?>">
=
<input name="z" type="text" size="3" value="<?= $model->z ?>">
<input type="button" onclick="usercontrol2.action({controller: 'usercontrol2Controller', method: 'calculate', rerender: 'panel2', formname: 'usercontrol2form'});" value="Calculate">
</p>
</div>
</form>
