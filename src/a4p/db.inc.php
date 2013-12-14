<?php 
//
// db.inc - Database connection
//

class db extends _db
{
	private static $connect_string = null;
	private static $user = null;
	private static $pass = null;

	public static function getConnection($new_connection = false)
	{
		if (self::$connect_string == null)
			self::$connect_string = config::$connect_string;

		if (self::$user == null)
			self::$user = config::$user;

		if (self::$pass == null)
			self::$pass = config::$pass;

		if (self::$conn == null || $new_connection == true) {
			self::$conn = new PDO(self::$connect_string, self::$user, self::$pass);
			self::$conn->exec("SET NAMES utf8");
			self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return self::$conn;
	}
}

class _db
{
	protected static $conn = null;

	public static function map($row, $obj)
	{
		foreach ($row as $key => $value)
		{
			if (property_exists($obj, $key))
				$obj->$key = $value;
		}
		
		return $obj;
	}

	public static function select()
	{
		return new db_sqlquery(func_get_args());
	}

	public static function insert()
	{
		return new db_sqlinsert();
	}

	public static function update($table)
	{
		return new db_sqlupdate($table);
	}

	public static function delete()
	{
		return new db_sqldelete();
	}

	public static function join($table = "")
	{
		return new db_sqljoin($table);
	}

	public static function str($s)
	{
		return "'" . str_replace("'", "''", $s) . "'";
	}
	
	public static function datetime($time)
	{
		return date('Y-m-d\\TH:i:s', $time);
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

	public function __construct($args) {
		foreach ($args as $s)
			$this->select .= ", " . $s;
	}
	
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
			if (strlen($s) > 0) {
				if (strncasecmp($s, " or ", 4) == 0)
					$this->where .= $s;
				else
					$this->where .= " and " . $s;
			}
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

	public function fetchAll($param = array()) {
		$sql = (string) $this;
		$conn = db::getConnection();
		$stmt = $conn->prepare($sql);
		$stmt->execute($param);
		return $stmt->fetchAll();
	}

	public function fetchOneRow($param = array()) {
		$sql = (string) $this;
		$conn = db::getConnection();
		$stmt = $conn->prepare($sql);
		$stmt->execute($param);
		return $stmt->fetch();
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

	public function execute($param = array()) {
		$sql = (string) $this;
		$conn = db::getConnection();
		$stmt = $conn->prepare($sql);
		$result = $stmt->execute($param);
		return $stmt->rowCount() == 1 ? $conn->lastInsertId() : null;
	}
}

class db_sqlupdate
{
	private $table = "";
	private $set = "";
	private $where = "";
	
	public function __construct($table) {
		$this->table = $table;
	}
	
	public function update($table) {
		$this->table = $table;
		return $this;
	}

	public function set($column, $value) {
		$this->set .= ", " . $column . " = " . $value;
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

	public function execute($param = array()) {
		$sql = (string) $this;
		$conn = db::getConnection();
		$stmt = $conn->prepare($sql);
		$result = $stmt->execute($param);
		return $stmt->rowCount();
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

	public function execute($param = array()) {
		$sql = (string) $this;
		$conn = db::getConnection();
		$stmt = $conn->prepare($sql);
		$result = $stmt->execute($param);
		return $stmt->rowCount();
	}
}

class db_sqljoin
{
	private $table1 = "";
	private $table2 = "";
	private $join = "";
	private $on = "";
	
	public function __construct($table1 = "") {
		$this->table1 = $table1;
	}
	
	public function table($table1) {
		$this->table1 = $table1;
		return $this;
	}
	
	public function join($table2) {
		$this->table1 = (string) $this;
		$this->join = " join ";
		$this->table2 = $table2;
		return $this;
	}

	public function innerjoin($table2) {
		$this->table1 = (string) $this;
		$this->join = " inner join ";
		$this->table2 = $table2;
		return $this;
	}

	public function leftjoin($table2) {
		$this->table1 = (string) $this;
		$this->join = " left join ";
		$this->table2 = $table2;
		return $this;
	}

	public function rightjoin($table2) {
		$this->table1 = (string) $this;
		$this->join = " right join ";
		$this->table2 = $table2;
		return $this;
	}

	public function outerjoin($table2) {
		$this->table1 = (string) $this;
		$this->join = " outer join ";
		$this->table2 = $table2;
		return $this;
	}

	public function leftouterjoin($table2) {
		$this->table1 = (string) $this;
		$this->join = " left outer join ";
		$this->table2 = $table2;
		return $this;
	}

	public function rightouterjoin($table2) {
		$this->table1 = (string) $this;
		$this->join = " right outer join ";
		$this->table2 = $table2;
		return $this;
	}

	public function on($on) {
		$this->on = $on;
		return $this;
	}
	
	public function __toString() {
		if (strlen($this->join) > 0)
			return trim($this->table1 . $this->join . $this->table2 . " on (" . $this->on . ")");
		else
			return trim($this->table1);
	}
}
