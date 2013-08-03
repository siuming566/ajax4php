<?php

class a4p_session_var
{
	public $file;
	public $value;
}

class a4p_session
{
	public static $global;
	
	public static $sid = "";

	private static $stack = array();

	private static $notfound = null;

	public static function exists($name)
	{
		if (self::get($name) !== null)
			return true;
		return false;
	}

	public static function init()
	{
		if (mt_rand(0, 99) != 0)
			return;

		$yesterday = strtotime(config::$tmp_expire_time);
		foreach (glob(config::$tmp_path . DIRECTORY_SEPARATOR . "session_*") as $oldfile) {
			if (filemtime($oldfile) < $yesterday)
				unlink($oldfile);
		}
	}

	public static function set($name, &$value)
	{
		$session_var = new a4p_session_var();
		$session_var->value = $value;

		$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "session_" . self::$sid . "." . md5($name);
		$session_var->file = fopen($filename, "w");
		flock($session_var->file, LOCK_EX);

		self::$stack[$name] = $session_var;
	}

	public static function remove($name)
	{
		if (self::exists($name)) {
			$session_var = self::$stack[$name];
			fclose($session_var->file);
			unset(self::$stack[$name]);
		}

		$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "session_" . self::$sid . "." . md5($name);
		if (file_exists($filename))
			unlink($filename);
	}

	public static function &get($name)
	{
		if (isset(self::$stack[$name]))
			return self::$stack[$name]->value;

		$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "session_" . self::$sid . "." . md5($name);
		if (file_exists($filename)) {
			$session_var = new a4p_session_var();
			$session_var->file = fopen($filename, "r+");
			$serial = fread($session_var->file, filesize($filename));
			$session_var->value = unserialize($serial);

			self::$stack[$name] = $session_var;

			return $session_var->value;
		}

		return self::$notfound;
	}

	public static function flush()
	{
		foreach (self::$stack as $session_var) {
			$serial = serialize($session_var->value);
			fseek($session_var->file, 0);
			fwrite($session_var->file, $serial);
			fclose($session_var->file);
		}
	}
}