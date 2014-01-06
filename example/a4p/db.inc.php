<?php 
//
// db.inc - Database connection
//

class _db
{
	protected static $conn = null;

	public static function getConnection($new_connection = false)
	{
		if (self::$conn == null || $new_connection == true) {
			self::$conn = new PDO(static::$connect_string, static::$user, static::$pass);
			self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return self::$conn;
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

	public static function select()
	{
		return new db_sqlquery(get_called_class(), func_get_args());
	}

	public static function insert()
	{
		return new db_sqlinsert(get_called_class());
	}

	public static function update($table)
	{
		return new db_sqlupdate(get_called_class(), $table);
	}

	public static function delete()
	{
		return new db_sqldelete(get_called_class());
	}

	public static function join($table = "")
	{
		return new db_sqljoin($table);
	}

	public static function execute($sql)
	{
		$conn = call_user_func(get_called_class() . "::getConnection");
		return $conn->exec($sql);
	}
	
	public static function str($s)
	{
		return "'" . str_replace("'", "''", $s) . "'";
	}
	
	public static function datetime($time)
	{
		return $time == null ? null : date('Y-m-d\\TH:i:s', $time);
	}
	
	public static function in(array $arr)
	{
		return ' (' . implode(',', array_fill(0, count($arr), '?')) . ')';
	}
	
	public static function beginTransaction()
	{
		$conn = call_user_func(get_called_class() . "::getConnection");
		if (!$conn->inTransaction())
			$conn->beginTransaction();
		return $conn;
	}

	public static function commit()
	{
		if (self::$conn != null && self::$conn->inTransaction())
			self::$conn->commit();
	}

	public static function rollback()
	{
		if (self::$conn != null && self::$conn->inTransaction())
			self::$conn->rollback();
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
	private $db = "";

	public function __construct($db, $args) {
		$this->db = $db;
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
		$conn = call_user_func($this->db . "::getConnection");
		$stmt = $conn->prepare($sql);
		$stmt->execute($param);
		$result =  $stmt->fetchAll();	
		if ($result !== false && $stmt->columnCount() == 1) {
			$arr = array();
			foreach ($result as $row)
				$arr[] = $row[0];
			return $arr;
		}
		return $result;
	}

	public function fetchOneRow($param = array()) {
		$sql = (string) $this;
		$conn = call_user_func($this->db . "::getConnection");
		$stmt = $conn->prepare($sql);
		$stmt->execute($param);
		$result = $stmt->fetch();
		if ($result !== false && $stmt->columnCount() == 1)
			return $result[0];
		return $result;
	}
}

class db_sqlinsert
{
	private $into = "";
	private $columns = "";
	private $values = "";
	private $db = "";
	
	public function __construct($db) {
		$this->db = $db;
	}

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
		$conn = call_user_func($this->db . "::beginTransaction");
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
	private $db = "";
	
	public function __construct($db, $table) {
		$this->db = $db;
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
		$conn = call_user_func($this->db . "::beginTransaction");
		$stmt = $conn->prepare($sql);
		$result = $stmt->execute($param);
		return $stmt->rowCount();
	}
}

class db_sqldelete
{
	private $from = "";
	private $where = "";
	private $db = "";
	
	public function __construct($db) {
		$this->db = $db;
	}	

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
		$conn = call_user_func($this->db . "::beginTransaction");
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
