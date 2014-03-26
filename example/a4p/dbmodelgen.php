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

$defaults = array(
	'int' => "0",
	'decimal' => "0",
	'text' => "''",
	'varchar' => "''",
	'datetime' => "db::datetime(time())",
	'numeric' => "0",
	'tinyint' => "0",
	'float' => "0",
	'date' => "db::datetime(time())",
	'char' => "''",
	'bigint' => "0",
	'ntext' => "''",
	'nvarchar' => "''",
	'bit' => "0"
);

if (!isset($_POST["table"])) {

$tables = db::select("TABLE_NAME")
			->from("INFORMATION_SCHEMA.TABLES")
			->where("TABLE_TYPE = 'BASE TABLE'")
			->orderby("TABLE_NAME")
			->fetchAll();
?>
<html>
<head>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
</head>
<body>
	<form method="post">
		<?php foreach ($tables as $table) { ?>
			<input type="checkbox" name="table[]" value="<?= $table ?>" /><?= $table ?><br/>
		<?php } ?>
		<input type="submit" />
		<input type="button" value="select all" onclick="$('input:checkbox').prop('checked', true);" />
	</form>
</body>
</html>
<?php

} else {

$tables = $_POST["table"];

foreach ($tables as $table) {

$cols = db::select("tc.CONSTRAINT_TYPE", "c.COLUMN_NAME", "c.DATA_TYPE", "c.IS_NULLABLE", "c.CHARACTER_MAXIMUM_LENGTH")
		->from(
			db::join("INFORMATION_SCHEMA.COLUMNS c")
			->leftjoin("INFORMATION_SCHEMA.KEY_COLUMN_USAGE cu")->on("c.TABLE_NAME = cu.TABLE_NAME and c.ORDINAL_POSITION = cu.ORDINAL_POSITION")
			->leftjoin("INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc")->on("c.TABLE_NAME = tc.TABLE_NAME and cu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME")
		)
		->where("c.TABLE_NAME = :table")
		->orderby("c.ORDINAL_POSITION")
		->fetchAll(array(":table" => $table));

ob_start();
echo "<?php";
?>


/** @table <?= $table ?> */
class <?= canonize($table, true) ?> extends Entity
{
<?php
	foreach ($cols as $col) {
		if ($col["CONSTRAINT_TYPE"] != null && $col["CONSTRAINT_TYPE"] != "PRIMARY KEY")
			continue;
?>
	/** <?= $col["CONSTRAINT_TYPE"] == "PRIMARY KEY" ? "@id" : "@column" ?> <?= $col["COLUMN_NAME"] ?><?= $col["CHARACTER_MAXIMUM_LENGTH"] != null && $col["CHARACTER_MAXIMUM_LENGTH"] > 0 && $col["CHARACTER_MAXIMUM_LENGTH"] < 10000 ? ":" . $col["CHARACTER_MAXIMUM_LENGTH"] : "" ?> */
	public $<?= canonize($col["COLUMN_NAME"]) ?>;

<?php
	}

$fks = db::select("fk.TABLE_NAME", "cu.COLUMN_NAME")
		->from(
			db::join("INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS c")
			->innerjoin("INFORMATION_SCHEMA.TABLE_CONSTRAINTS fk")->on("c.CONSTRAINT_NAME = fk.CONSTRAINT_NAME")
			->innerjoin("INFORMATION_SCHEMA.TABLE_CONSTRAINTS pk")->on("c.UNIQUE_CONSTRAINT_NAME = pk.CONSTRAINT_NAME")
			->innerjoin("INFORMATION_SCHEMA.KEY_COLUMN_USAGE cu")->on("c.CONSTRAINT_NAME = cu.CONSTRAINT_NAME")
		)
		->where("pk.TABLE_NAME = :table")
		->orderby("fk.TABLE_NAME", "cu.COLUMN_NAME")
		->fetchAll(array(":table" => $table));

	foreach ($fks as $fk) {
?>
	/** @fk <?= $fk["TABLE_NAME"] ?>.<?= $fk["COLUMN_NAME"] ?> */
	public $<?= canonize($fk["TABLE_NAME"]) ?>;

<?php
	}
?>
	public function __construct() {
		parent::__construct();
<?php
	foreach ($cols as $col) {
		if ($col["CONSTRAINT_TYPE"] != null && $col["CONSTRAINT_TYPE"] != "PRIMARY KEY")
			continue;
?>
		$this-><?= canonize($col["COLUMN_NAME"]) ?> = <?= $col["IS_NULLABLE"] == "NO" ? $defaults[$col["DATA_TYPE"]] : "null" ?>;
<?php
	}

	foreach ($fks as $fk) {
?>
		$this-><?= canonize($fk["TABLE_NAME"]) ?> = array();
<?php
	}
?>
	}
}
<?php

$content = ob_get_clean();

$dirname = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "class" . DIRECTORY_SEPARATOR . "entity";
if (!file_exists($dirname))
	mkdir($dirname, 0755, true);

$filename = $dirname . DIRECTORY_SEPARATOR . canonize($table, true) . ".class.php";
file_put_contents($filename, $content);

echo $filename . "<br/>";

}

echo "Done";

}
