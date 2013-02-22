<?php

class language
{
	public static $mapping = null;

	public static function load($ini_file)
	{
		self::$mapping = parse_ini_file(SITE_ROOT . "/resource/" . $ini_file . ".ini", true);
	}

	public static function set($name, $value)
	{
		$arr = explode(".", $name, 2);
		$section = $arr[0];
		if (count($arr) == 2) {
			$key = $arr[1];
			self::$mapping[$section][$key] = $value;
		} else
			self::$mapping[$section] = $value;
	}

	public static function replace($matches)
	{
		$arr = explode(".", $matches[1], 2);
		$section = $arr[0];
		if (count($arr) == 2) {
			$key = $arr[1];
			if (isset(self::$mapping[$section][$key]))
				return self::$mapping[$section][$key];
		} else {
			if (isset(self::$mapping[$section]) && is_string(self::$mapping[$section]))
				return self::$mapping[$section];
		}

		return $matches[0];
	}

	public static function &process(&$buffer)
	{
		if (self::$mapping != null) {
			$regex = '/\$([\w|\.]+)/';
			$buffer = preg_replace_callback($regex, "language::replace", $buffer);
		}
		return $buffer;
	}
}