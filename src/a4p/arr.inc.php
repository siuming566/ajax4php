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
	private $groupby = array();
	private $sort = "";

	public function __construct($select) {
		$this->select = $select;
	}
	
	public static function select() {
		return new arr(func_get_args());
	}

	public function from(&$from) {
		$this->from = &$from;
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

	public function groupby() {
		$this->groupby = func_get_args();
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

	public function _cmp2($a, $b) {
		foreach($this->groupby as $prop) {
			if ($a->$prop > $b->$prop)
				return 1;
			if ($a->$prop < $b->$prop)
				return -1;
		}
		return 0;
	}

	private function _filter() {
		$match = array();
		if (strlen($this->where) > 5) {
			$regex = '/{(\w+)}/';
			$where = preg_replace($regex, '$a->$1', substr($this->where, 5));
			$where = str_replace('{}', '$a', $where);
			$condition = create_function('$a', "return $where;");
			foreach ($this->from as $row) {
				$obj = is_array($row) ? (object) $row : $row;
				if ($condition($obj))
					$match[] = $obj;
			}
		} else {
			foreach ($this->from as $obj)
				$match[] = $obj;
		}
		return $match;
	}

	public function takeAll() {
		return $this->take(-1);
	}

	public function take($n) {
		$match = $this->_filter();

		if (count($this->orderby) > 0)
			usort($match, array($this, "_cmp"));

		if (strtolower($this->sort) == "asc")
			sort($match);

		if (strtolower($this->sort) == "desc")
			rsort($match);

		if ($n == -1)
			$n = count($match);

		if (count($this->select) == 1) {
			$prop = $this->select[0];
			$result = array();
			$i = 1;
			foreach ($match as $row) {
				$result[] = $row->$prop;
				if ($i++ >= $n)
					break;
			}
			return $result;
		} else if (count($this->select) > 0) {
			$result = array();
			$i = 1;
			foreach ($match as $row) {
				$obj = new stdclass();
				foreach ($this->select as $prop) 
					$obj->$prop = $row->$prop;
				$result[] = $obj;
				if ($i++ >= $n)
					break;
			}
			return $result;
		} else
			return array_slice($match, 0, $n);
	}

	public function each($func) {
		$arr = $this->takeAll();
		$result = array();
		foreach ($arr as $obj)
			$result[] = $func($obj);
		return $result;
	}

	public function distinct($prop = null) {
		$match = $this->_filter();

		if ($prop != null) {
			$result = array();
			foreach ($match as $row)
				$result[] = $row->$prop;
		}
		else
			$result = $match;

		return array_values(array_unique($result));
	}

	private function reset_match() {
		$match = new stdclass();
		foreach ($this->groupby as $prop)
			$match->$prop = null;
		$match->count = 0;
		return $match;
	}

	public function count() {
		$match = $this->_filter();

		if (count($this->groupby) > 0) {

			usort($match, array($this, "_cmp2"));

			$last_match = $this->reset_match();

			$result = array();
			foreach ($match as $row) {
				foreach ($this->groupby as $prop) {
					if ($row->$prop != $last_match->$prop) {
						if ($last_match->count > 0)
							$result[] = $last_match;
						$last_match = $this->reset_match();
						break;
					}
				}
				if ($last_match->count == 0) {
					foreach ($this->groupby as $prop)
						$last_match->$prop = $row->$prop;
				}
				$last_match->count++;
			}
			if ($last_match->count > 0)
				$result[] = $last_match;

			return $result;
		}
		else
			return count($match);
	}

	public function sum() {
		$attrs = func_get_args();

		$match = $this->_filter();

		if (count($this->groupby) > 0) {

			usort($match, array($this, "_cmp2"));

			$last_match = $this->reset_match();

			$result = array();
			foreach ($match as $row) {
				foreach ($this->groupby as $prop) {
					if ($row->$prop != $last_match->$prop) {
						if ($last_match->count > 0)
							$result[] = $last_match;
						$last_match = $this->reset_match();
						break;
					}
				}
				if ($last_match->count == 0) {
					foreach ($this->groupby as $prop)
						$last_match->$prop = $row->$prop;
				}
				$last_match->count++;
				foreach ($attrs as $attr) {
					$prop = "sum_" . $attr;
					if (!isset($last_match->$prop))
						$last_match->$prop = 0;
					$last_match->$prop += $row->$attr;
				}
			}
			if ($last_match->count > 0)
				$result[] = $last_match;

			return $result;
		}
		else
			return array_sum($match);
	}

	public function remove() {
		if (strlen($this->where) > 5) {
			$regex = '/{(\w+)}/';
			$where = preg_replace($regex, '$a->$1', substr($this->where, 5));
			$where = str_replace('{}', '$a', $where);
			$condition = create_function('$a', "return $where;");
			foreach ($this->from as $key => $row) {
				$obj = is_array($row) ? (object) $row : $row;
				if ($condition($obj))
					unset($this->from[$key]);
			}
		}
	}
}
