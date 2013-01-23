<?php 
//
// ui.inc - UI Components
//

class ui_dataTable
{
	public $name = "";
	public $sortBy = "";
	public $order = "SORT_ASC";
	public $currentPage = 0;
	public $totalPage = 0;
	public $data = array();
	public $pager = "";

	public function headerColumn($text, $sortBy = "")
	{
		if ($sortBy != "")
		{
			global $ui;
			echo "<th onclick=\"$ui.sortBy('$this->name', '$sortBy');\">" . $text;
			if ($this->sortBy == $sortBy)
			{
				if ($this->order == "SORT_ASC")
					echo "&#160;^";
				else 				
					echo "&#160;v";
			}
			echo "</th>";
		} 
		else
			echo "<th>" . $text . "</th>"; 
		return;
	}
	
	public function done() {
		echo "</div>";
		return;
	}
	
	public function firstPageJS() {
		global $ui;
		echo "$ui.firstPage('" . $this->name . "', '" . $this->pager . "');";
		return ;
	}

	public function previousPageJS() {
		global $ui;
		echo "$ui.previousPage('" . $this->name . "', '" . $this->pager . "');";
		return ;
	}

	public function nextPageJS() {
		global $ui;
		echo "$ui.nextPage('" . $this->name . "', '" . $this->pager . "');";
		return ;
	}

	public function lastPageJS() {
		global $ui;
		echo "$ui.lastPage('" . $this->name . "', '" . $this->pager . "');";
		return ;
	}
	
	public function selectPage()
	{
		global $ui;
		echo "<select onchange=\"$ui.gotoPage('" . $this->name . "', '" . $this->pager . "', this.value);\">\r\n";
		for ($i = 0; $i < $this->totalPage; ++$i)
		{
			if ($i == $this->currentPage)
				echo "<option selected=\"true\">" . ($i + 1) . "</option>\r\n";
			else
				echo "<option>" . ($i + 1) . "</option>\r\n";
		}
		echo "</select>";
		return ;
	}
}

class ui
{
	public $enableAjaxCall = true;
	
	static function &array_sort(&$array, $on, $order="SORT_DESC")
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch($order)
			{
				case "SORT_ASC":
					asort($sortable_array);
					break;
				case "SORT_DESC":
					arsort($sortable_array);
					break;
			}

			foreach($sortable_array as $k => $v) {
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}
	
	static function &array_filter(&$array, $page, $rowcount, $total)
	{
		$new_array = array();
		$startcount = $page * $rowcount;
		$j = 0;
		for ($i = $startcount; $i < $total && $i < $startcount + $rowcount; ++$i)
		{
			$row = $array[$i];
			$row["rownum"] = $j;
			$new_array[] = $row;
			$j++;
		}
		return $new_array;
	}
	
	public static function getTable($name)
	{
		if (!isset($_SESSION["a4p.ui_" . $name]))
			$_SESSION["a4p.ui_" . $name] = new ui_dataTable();
	
		return $_SESSION["a4p.ui_" . $name];
	}
	
	public static function resetTable($name)
	{
		unset($_SESSION["a4p.ui_" . $name]);
		return getTable($name);
	}
	
	public static function dataTable($name, &$data, $rowcount, $pager = "")
	{
		$dataTable = self::getTable($name);
		
		$dataTable->name = $name;
		echo "<div id=\"$name\">";
		
		if ($pager != "")
			$dataTable->pager = $pager;
		
		if ($dataTable->sortBy != "")
			$allrow = self::array_sort($data, $dataTable->sortBy, $dataTable->order);
		else
			$allrow = &$data;
	
		$total = count($allrow);
		$dataTable->totalPage = floor($total / $rowcount) + 1;
		$dataTable->data = self::array_filter($allrow, $dataTable->currentPage, $rowcount, $total);
		
		return $dataTable;
	}
	
	public function sortBy($param)
	{
		$obj = json_decode($param);
		
		$dataTable = self::getTable($obj->name);
		
		if ($dataTable->sortBy == $obj->sortBy)
		{
			if ($dataTable->order == "SORT_ASC")
				$dataTable->order = "SORT_DESC";
			else
				$dataTable->sortBy = "";
		}
		else
		{
			$dataTable->sortBy = $obj->sortBy;
			$dataTable->order = "SORT_ASC";
		}
		
		return "";
	}

	public function firstPage($param) {
		$dataTable = self::getTable($param);
		$dataTable->currentPage = 0;
		return "";
	}

	public function previousPage($param) {
		$dataTable = self::getTable($param);
		if ($dataTable->currentPage > 0)
			--$dataTable->currentPage;
		return "";
	}

	public function nextPage($param) {
		$dataTable = self::getTable($param);
		if ($dataTable->currentPage < $dataTable->totalPage - 1)
			++$dataTable->currentPage;
		return "";
	}

	public function lastPage($param) {
		$dataTable = self::getTable($param);
		$dataTable->currentPage = $dataTable->totalPage - 1;
		return "";
	}

	public function gotoPage($param) {
		$obj = json_decode($param);
		$dataTable = self::getTable($obj->name);
		$dataTable->currentPage = $obj->page - 1;
		return "";
	}
}