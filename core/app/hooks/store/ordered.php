<?php
class hook_store_ordered {
	var $conditions = false;
	var $value = false;
	function set_conditions($query, $argument) {
		if (false === $this->conditions) {
			$this->conditions == array();
			$fields = explode(" ", $argument);
			foreach ($fields as $field) $this->conditions[$field] = $query->fields[$field];
		}
	}
	function empty_insert(&$query, $column, $argument) {
		$query->set($column, $this->before_insert($query, $column, "", $column, $argument));
	}
	function before_insert(&$query, $key, $value, $column, $argument) {
		$this->set_conditions($query, $argument);
		if (!empty($value) && is_numeric($value)) $this->value = $value;
		$h = query($query->model)->select("MAX(`$column`) as highest")->conditions($this->conditions)->one();
		return $h['highest']+1;
	}
	function after_store(&$query, $key, $value, $column, $argument) {
		if (false !== $this->value) $value = this->value;
		$select = array("id", $column);
		if (!empty($argument)) $select = array_merge($select, array_keys($this->conditions));
		$id = $query->getId();
		$row = query($query->model)->select(implode(",", $select))->condition("id", $id)->one();
		$same_level = true;
		foreach ($this->conditions as $k => $v) if ($row[$k] != $v) $same_level = false;
		$ids = array($row['id']);
		if ($same_level) $increment = ($row[$column] < $value) ? -1 : 1;
		else $increment = 1;
		while (!empty($row)) {
			query($query->model)->condition("id", $row['id'])->set($column, $value)->update();
			$row = query($query->model)->select(implode(",", $select))->conditions($this->conditions)->condition("id", $ids, "!=")->condition($column, $value)->one();
			$ids[] = $row['id'];
			$value += $increment;
		}
	}
}
?>
