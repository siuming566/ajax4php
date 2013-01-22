<?php
//
// a4p_sec.inc - Encryption
//

class a4p_sec
{
    private static $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

	public static function randomString($length) {
	    $pass = array();
	    $alphaLength = strlen(self::$alphabet) - 1;
	    for ($i = 0; $i < $length; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = self::$alphabet[$n];
	    }
	    return implode($pass);
	}

	public static function shiftString($map, $key) {
	    $pass = array();
	    $length = strlen($key);
	    for ($i = 0; $i < $length; $i++) {
	    	$c = $key[$i];
	        $n = strpos(self::$alphabet, $c);
	        //$pass[] = $map[$n];
	        $pass[] = $n . ":";
	    }
	    return implode($pass);
	}

}
