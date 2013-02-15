<?php

class a4p_session_var
{
	public $file;
	public $value;
}

class a4p_session
{
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
		$session_files = glob(session_save_path() . DIRECTORY_SEPARATOR . "sess_*");
		foreach($session_files as $session_file) {
			$pos = strpos($session_file, ".");
			if ($pos != false) {
				$session_name = substr($session_file, 0, $pos);
				if (!in_array($session_name, $session_files))
					unlink($session_file);
			}
		}
	}

	public static function set($name, &$value)
	{
		$session_var = new a4p_session_var();
		$session_var->value = $value;

		$filename = session_save_path() . DIRECTORY_SEPARATOR . "sess_" . self::$sid . "." . md5($name);
		$session_var->file = fopen($filename, "w");
		flock($session_var->file, LOCK_EX);

		self::$stack[$name] = $session_var;
	}

	public static function &get($name)
	{
		if (isset(self::$stack[$name]))
			return self::$stack[$name]->value;

		$filename = session_save_path() . DIRECTORY_SEPARATOR . "sess_" . self::$sid . "." . md5($name);
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