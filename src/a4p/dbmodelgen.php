<?php

require_once "config.inc.php";
require_once "db.inc.php";

function canonize($name, $upper = false) {
	$canonize = "";
	$arr = str_split($name);
	foreach ($arr as $c) {
		if ($c == "_")
			$upper = true;
		else {
			$canonize .= $upper ? strtoupper($c) : strtolower($c);
			$upper = false;
		}
	}
	return $canonize;
}

$table = strtoupper($_SERVER["QUERY_STRING"]);

$cols = db::select("tc.CONSTRAINT_TYPE", "c.COLUMN_NAME")
		->from(
			db::join("INFORMATION_SCHEMA.COLUMNS c")
			->leftjoin("INFORMATION_SCHEMA.KEY_COLUMN_USAGE cu")->on("c.TABLE_NAME = cu.TABLE_NAME and c.ORDINAL_POSITION = cu.ORDINAL_POSITION")
			->leftjoin("INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc")->on("c.TABLE_NAME = tc.TABLE_NAME and cu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME")
		)
		->where("c.TABLE_NAME = :table")
		->orderby("c.ORDINAL_POSITION")
		->fetchAll(array(":table" => $table));

?><pre>&lt;?php

/** @table <?= $table ?> */
class <?= canonize($table, true) ?> extends Entity
{
<?php
	foreach ($cols as $col) {
		if ($col["CONSTRAINT_TYPE"] != null && $col["CONSTRAINT_TYPE"] != "PRIMARY KEY")
			continue;
?>
	/** <?= $col["CONSTRAINT_TYPE"] == "PRIMARY KEY" ? "@id" : "@column" ?> <?= $col["COLUMN_NAME"] ?> */
	public $<?= canonize($col["COLUMN_NAME"]) ?>;

<?php
	}
?>
}
