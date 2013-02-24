<form>
<div id="panel1">
Content Here
<p>
<input type="text" name="textfield1" value="<?= $model->textfield1 ?>">
<input type="button" value="Add" onclick="a4p.action({method: 'add', rerender: 'panel1'});">
</p>
</div>
</form>
