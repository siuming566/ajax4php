<?php 
//
// layout.inc - Layout
//

class layout_table
{
	public $id;
	public $width;
	public $height;
	public $type;
	public $pad_width = 0;
	public $pad_height = 0;
	public $rows = array();
	public $columns = array();

	public function __construct($id, $width, $height, $type) {
		$this->id = $id;
		$this->width = $width;
		$this->height = $height;
		$this->type = $type;
	}
}

class layout_row
{
	public $id;
	public $height;

	public function __construct($id, $height) {
		$this->id = $id;
		$this->height = $height;
	}
}

class layout_column
{
	public $id;
	public $width;

	public function __construct($id, $width) {
		$this->id = $id;
		$this->width = $width;
	}
}

class layout_vertical_meta {

	public $table;
	public $rows;
	public $cssstyle = "";
	public $cssclass = "";

	public function __construct($table, $rows) {
		$this->table = $table;
		$this->rows = explode(",", $rows);
	}

	public function new_row() {
		$count = count($this->table->rows);
		$rowid = $this->table->id . "_row" . ($count + 1);
		$rowheight = $this->rows[$count % count($this->rows)];
		$row = new layout_row($rowid, $rowheight);
		$this->table->rows[] = $row;
		return $rowid;
	}

	public function padding($width, $height) {
		$this->table->pad_width = $width;
		$this->table->pad_height = $height;
		return $this;
	}

	public function cssstyle($cssstyle) {
		if (strlen($cssstyle) > 0)
			$this->cssstyle = "style=\"$cssstyle\"";
		return $this;
	}

	public function cssclass($cssclass) {
		if (strlen($cssclass) > 0)
			$this->cssclass = $cssclass;
		return $this;
	}

	public function begin($attr = "") {
		$id = $this->table->id;
		$rowid = $this->new_row();
		$cssstyle = $this->cssstyle;
		$cssclass = $this->cssclass;
		print <<< END
<table class="layouttable" cellspacing="0" cellpadding="0" id="$id">
<tr class="layoutcell">
<td class="layoutrow $cssclass" valign="top" $cssstyle $attr><div class="layoutdiv" id="$rowid">
END;
		$this->cssstyle = "";
		$this->cssclass = "";
		return $this;
	}

	public function next($attr = "") {
		$rowid = $this->new_row();
		$cssstyle = $this->cssstyle;
		$cssclass = $this->cssclass;
		print <<< END
	</div>
</td>
</tr>
<tr class="layoutcell">
<td class="layoutrow $cssclass" valign="top" $cssstyle $attr><div class="layoutdiv" id="$rowid">
END;
		$this->cssstyle = "";
		$this->cssclass = "";
		return $this;
	}

	public function end() {
		print <<< END
</div></td>
</tr>
</table>
END;
		return $this;
	}
}

class layout_horizontal_meta {

	public $table;
	public $columns;
	public $cssstyle = "";
	public $cssclass = "";

	public function __construct($table, $columns) {
		$this->table = $table;
		$this->columns = explode(",", $columns);
	}

	public function new_column() {
		$count = count($this->table->columns);
		$colid = $this->table->id . "_col" . ($count + 1);
		$colwidth = $this->columns[$count % count($this->columns)];
		$column = new layout_column($colid, $colwidth);
		$this->table->columns[] = $column;
		return $colid;
	}

	public function padding($width, $height) {
		$this->table->pad_width = $width;
		$this->table->pad_height = $height;
		return $this;
	}

	public function cssstyle($cssstyle) {
		if (strlen($cssstyle) > 0)
			$this->cssstyle = "style=\"$cssstyle\"";
		return $this;
	}

	public function cssclass($cssclass) {
		if (strlen($cssclass) > 0)
			$this->cssclass = $cssclass;
		return $this;
	}

	public function begin($attr = "") {
		$id = $this->table->id;
		$colid = $this->new_column();
		$cssstyle = $this->cssstyle;
		$cssclass = $this->cssclass;
		print <<< END
<table class="layouttable" cellspacing="0" cellpadding="0" id="$id">
<tr class="layoutcell">
<td class="layoutrow $cssclass" valign="top" $cssstyle $attr><div class="layoutdiv" id="$colid">
END;
		$this->cssstyle = "";
		$this->cssclass = "";
		return $this;
	}

	public function next($attr = "") {
		$colid = $this->new_column();
		$cssstyle = $this->cssstyle;
		$cssclass = $this->cssclass;
		print <<< END
	</div>
</td>
<td class="layoutrow $cssclass" valign="top" $cssstyle $attr><div class="layoutdiv" id="$colid">
END;
		$this->cssstyle = "";
		$this->cssclass = "";
		return $this;
	}

	public function end() {
		print <<< END
</div></td>
</tr>
</table>
END;
		return $this;
	}
}

class layout
{
	public static $bodymargin = 8;

	public static $layout_info = array();

	public static function bodymargin($bodymargin)
	{
		self::$bodymargin = $bodymargin;
	}

	public static function vertical($width, $height, $rows)
	{
		$id = "table_" . a4p_sec::randomString(8);
		$table = new layout_table($id, $width, $height, "vertical");
		self::$layout_info[] = $table;

		$meta = new layout_vertical_meta($table, $rows);
		return $meta;
	}

	public static function horizontal($width, $height, $columns)
	{
		$id = "table_" . a4p_sec::randomString(8);
		$table = new layout_table($id, $width, $height, "horizontal");
		self::$layout_info[] = $table;

		$meta = new layout_horizontal_meta($table, $columns);
		return $meta;
	}
}
