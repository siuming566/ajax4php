<?php

function startsWith($haystack, $needle) {
	return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0)
		return true;
	return (substr($haystack, -$length) === $needle);
}

function match() {
	$str = null;
	foreach (func_get_args() as $s) {
		if ($str == null) {
			if (strpos($s, ',') === false)
				$str = $s;
			else
				$str = explode(',', $s);
		} else {
			if (is_array($str)) {
				if (array_search($s, $str) !== false)
					return true;
			} else {
				if ($str == $s)
					return true;
			}
		}
	}
	return false;
}