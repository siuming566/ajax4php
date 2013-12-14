<?php 
//
// arr.inc - array selection
//

class arr
{
	private $select = array();
	private $from = array();
	private $where = "";
	private $orderby = array();
	private $sort = "";

	public function __construct($select) {
		$this->select = $select;
	}
	
	public static function select() {
		return new arr(func_get_args());
	}

	public function from($from) {
		$this->from = $from;
		return $this;
	}

	public function where() {
		foreach (func_get_args() as $s)
			if (strlen($s) > 0)
				$this->where .= " and " . $s;
		return $this;
	}
	
	public function orderby() {
		$this->orderby = func_get_args();
		return $this;
	}

	public function sort($sort) {
		$this->sort = $sort;
		return $this;
	}

	public function nosort() {
		$this->sort = "";
		return $this;
	}

	public function _cmp($a, $b) {
		foreach($this->orderby as $attr) {
			$arr = explode(" ", $attr);
			$prop = $arr[0];
			if (isset($arr[1]) && strtolower($arr[1]) == "desc")
				$reverse = -1;
			else
				$reverse = 1;
			if ($a->$prop > $b->$prop)
				return $reverse;
			if ($a->$prop < $b->$prop)
				return -$reverse;
		}
		return 0;
	}

	public function takeAll() {
		return $this->take(-1);
	}

	public function take($n) {
		$filter = array();
		if (strlen($this->where) > 5) {
			$regex = '/{(\w+)}/';
			$where = preg_replace($regex, '$a->$1', substr($this->where, 5));
			$where = str_replace('{}', '$a', $where);
			$condition = create_function('$a', "return $where;");
			foreach ($this->from as $row) {
				$obj = is_array($row) ? (object) $row : $row;
				if ($condition($obj))
					$filter[] = $obj;
			}
		} else {
			foreach ($this->from as $obj)
				$filter[] = $obj;
		}

		if (count($this->orderby) > 0)
			usort($filter, array($this, "_cmp"));

		if (strtolower($this->sort) == "asc")
			sort($filter);

		if (strtolower($this->sort) == "desc")
			rsort($filter);

		if ($n == -1)
			$n = count($filter);

		if (count($this->select) == 1) {
			$prop = $this->select[0];
			$result = array();
			$i = 1;
			foreach ($filter as $row) {
				$result[] = $row->$prop;
				if ($i++ >= $n)
					break;
			}
			return $result;
		} else if (count($this->select) > 0) {
			$result = array();
			$i = 1;
			foreach ($filter as $row) {
				$obj = new stdclass();
				foreach ($this->select as $prop) 
					$obj->$prop = $row->$prop;
				$result[] = $obj;
				if ($i++ >= $n)
					break;
			}
			return $result;
		} else
			return array_slice($filter, 0, $n);
	}
}
