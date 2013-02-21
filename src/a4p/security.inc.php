<?php
//
// a4p_sec.inc - Encryption
//

class a4p_sec
{
	public static $map;

	public static $auth = false;

	private static $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

	public static function randomString($length) {
		$pass = "";
		$alphaLength = strlen(self::$alphabet) - 1;
		for ($i = 0; $i < $length; $i++) {
			$n = mt_rand(0, $alphaLength);
			$pass .= self::$alphabet[$n];
		}
		return $pass;
	}

	public static function shiftString($map, $key) {
		$pass = "";
		$length = strlen($key);
		for ($i = 0; $i < $length; $i++) {
			$c = $key[$i];
			$n = strpos(self::$alphabet, $c);
			$pass .= $n . ":";
		}
		return $pass;
	}

}
