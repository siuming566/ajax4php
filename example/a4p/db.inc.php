<?php 
//
// db.inc - Database connection
//

class db
{
	private static $connect_string = "mysql:host=;dbname=";
	private static $user = "";
	private static $pass = "";

	public static function getConnection()
	{
		$conn = new PDO(self::$connect_string, self::$user, self::$pass);
		$conn->exec("SET CHARACTER SET utf8");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
	}
	
	public static function map($row, $obj)
	{
		foreach ($row as $key => $value)
		{
			if (property_exists($obj, $key))
				$obj->$key = $value;
		}
		
		return $obj;
	}

	public static function query()
	{
		return new db_sqlquery();
	}

	public static function insert()
	{
		return new db_sqlinsert();
	}

	public static function update()
	{
		return new db_sqlupdate();
	}

	public static function delete()
	{
		return new db_sqldelete();
	}

	public static function str($s)
	{
		return "'" . str_replace("'", "''", $s) . "'";
	}
}

class db_sqlquery
{
	private $select = "";
	private $from = "";
	private $where = "";
	private $groupby = "";
	private $orderby = "";
	private $top = "";
	
	public function select() {
		foreach (func_get_args() as $s)
			$this->select .= ", " . $s;
		return $this;
	}

	public function from() {
		foreach (func_get_args() as $o)
			$this->from .= ", " . (string) $o;
		return $this;
	}

	public function where() {
		foreach (func_get_args() as $s)
			if (strlen($s) > 0)
				$this->where .= " and " . $s;
		return $this;
	}
	
	public function groupby() {
		foreach (func_get_args() as $s)
			$this->groupby .= ", " . $s;
		return $this;
	}

	public function orderby() {
		foreach (func_get_args() as $s)
			$this->orderby .= ", " . $s;
		return $this;
	}
	
	public function __toString() {
		$sql = "";
		
		if (strlen($this->select) > 2)
			$sql .= "select " . substr($this->select, 2);
		
		if (strlen($this->from) > 2)
			$sql .= " from " . substr($this->from, 2);

		if (strlen($this->where) > 5)
			$sql .= " where " . substr($this->where, 5);

		if (strlen($this->groupby) > 2)
			$sql .= " group by " . substr($this->groupby, 2);

		if (strlen($this->orderby) > 2)
			$sql .= " order by " . substr($this->orderby, 2);
		
		return $sql;
	}

	public function sql() {
		return (string) $this;
	}
	
	public function _as($alias) {
		return "(" . $this->sql() . ") as " . $alias;
	}
}

class db_sqlinsert
{
	private $into = "";
	private $columns = "";
	private $values = "";
	
	public function into($into) {
		$this->into = $into;
		return $this;
	}
	
	public function values($column, $value) {
		$this->columns .= ", " . $column;
		$this->values .= ", " . $value;
		return $this;
	}
	
	public function __toString() {
		return "insert into " . $this->into . " (" . substr($this->columns ,2) . ") values (" . substr($this->values ,2) . ")";
	}

	public function sql() {
		return (string) $this;
	}
}

class db_sqlupdate
{
	private $table = "";
	private $set = "";
	private $where = "";
	
	public function update($table) {
		$this->table = $table;
		return $this;
	}

	public function set() {
		foreach (func_get_args() as $s)
			$this->set .= ", " . $s;
		return $this;
	}

	public function where() {
		foreach (func_get_args() as $s)
			$this->where .= " and " . $s;
		return $this;
	}
	
	public function __toString() {
		$sql = "update " . $this->table . " set " . substr($this->set, 2);

		if (strlen($this->where) > 5)
			$sql .= " where " . substr($this->where, 5);

		return $sql;
	}

	public function sql() {
		return (string) $this;
	}
}

class db_sqldelete
{
	private $from = "";
	private $where = "";
	
	public function from($from) {
		$this->from = $from;
		return $this;
	}

	public function where() {
		foreach (func_get_args() as $s)
			$this->where .= " and " . $s;
		return $this;
	}
	
	public function __toString() {
		$sql = "delete from " . $this->from;

		if (strlen($this->where) > 5)
			$sql .= " where " . substr($this->where, 5);

		return $sql;
	}

	public function sql() {
		return (string) $this;
	}	
}