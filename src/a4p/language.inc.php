<?php

class language
{
	public static $mapping = null;

	public static function load($ini_file)
	{
		self::$mapping = parse_ini_file(SITE_ROOT . "/resource/" . $ini_file . ".ini", true);
	}

	public static function replace($matches)
	{
		$arr = explode(".", $matches[1], 2);
		if (isset($arr[1]) && isset(self::$mapping[$arr[0]][$arr[1]]))
			return self::$mapping[$arr[0]][$arr[1]];
		return "$" . $matches[1];
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