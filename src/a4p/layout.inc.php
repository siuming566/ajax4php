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
	public $nested;

	public function __construct($id, $height) {
		$this->id = $id;
		$this->height = $height;
	}
}

class layout_column
{
	public $id;
	public $width;
	public $nested;

	public function __construct($id, $width) {
		$this->id = $id;
		$this->width = $width;
	}
}

class layout_vertical_meta {

	public $table;
	public $rows;

	public function __construct($table, $rows) {
		$this->table = $table;
		$this->rows = explode(",", $rows);
	}

	public function new_row() {
		$count = count($this->table->rows);
		$rowid = $this->table->id . "row" . ($count + 1);
		$rowheight = $this->rows[$count % count($this->rows)];
		$row = new layout_row($rowid, $rowheight);
		$this->table->rows[] = $row;
		layout::$layout_stack[] = $row;
		return $rowid;
	}

	public function next() {
		array_pop(layout::$layout_stack);
		$rowid = $this->new_row();
		print <<< END
	</div>
</td>
</tr>
<tr class="layoutcell">
<td class="layoutrow"><div class="layoutdiv" id="$rowid">
END;
	}

	public function end() {
		array_pop(layout::$layout_stack);
		print <<< END
</div></td>
</tr>
</table>
END;
	}
}

class layout_horizontal_meta {

	public $table;
	public $columns;

	public function __construct($table, $columns) {
		$this->table = $table;
		$this->columns = explode(",", $columns);
	}

	public function new_column() {
		$count = count($this->table->columns);
		$colid = $this->table->id . "col" . ($count + 1);
		$colwidth = $this->columns[$count % count($this->columns)];
		$column = new layout_column($colid, $colwidth);
		$this->table->columns[] = $column;
		layout::$layout_stack[] = $column;
		return $colid;
	}

	public function next() {
		array_pop(layout::$layout_stack);
		$colid = $this->new_column();
		print <<< END
	</div>
</td>
<td class="layoutrow" id="$colid"><div class="layoutdiv">
END;
	}

	public function end() {
		array_pop(layout::$layout_stack);
		print <<< END
</div></td>
</tr>
</table>
END;
	}
}

class layout
{
	public static $layout_stack = array();

	public static $layout_info = array();

	public static function vertical($width, $height, $rows)
	{
		$count = count(self::$layout_info);
		$id = "table" . ($count + 1);

		$table = new layout_table($id, $width, $height, "vertical");
		if (count(self::$layout_stack) > 0) {
			$last = end(self::$layout_stack);
			$last->nested = $count;
		}
		self::$layout_info[] = $table;

		$meta = new layout_vertical_meta($table, $rows);
		$rowid = $meta->new_row();
		print <<< END
<table class="layouttable" id="$id">
<tr class="layoutcell">
<td class="layoutrow"><div class="layoutdiv" id="$rowid">
END;
		return $meta;
	}

	public static function horizontal($width, $height, $columns)
	{
		$count = count(self::$layout_info);
		$id = "table" . ($count + 1);

		$table = new layout_table($id, $width, $height, "horizontal");
		if (count(self::$layout_stack) > 0) {
			$last = end(self::$layout_stack);
			$last->nested = $count;
		}
		self::$layout_info[] = $table;

		$meta = new layout_horizontal_meta($table, $columns);
		$colid = $meta->new_column();
		print <<< END
<table class="layouttable" id="$id">
<tr class="layoutcell">
<td class="layoutrow" id="$colid"><div class="layoutdiv">
END;
		return $meta;
	}
}
