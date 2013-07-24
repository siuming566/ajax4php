<?php 
//
// routing.inc - Routing
//

class plugin
{
	private static $plugin_list = array();

	private static $enabled = false;

	public static function register($list)
	{
		self::$plugin_list = array_merge(self::$plugin_list, $list);
	}

	public static function element($name, $env)
	{
		if (!self::$enabled)
			return;
		
		foreach(self::$plugin_list as $plugin) {
			$plugin_file = $_SERVER['DOCUMENT_ROOT'] . '/plugin/' . $plugin . '/' . $name . '.php';
			if (file_exists($plugin_file))
				require $plugin_file;
		}
	}

	public static function enable()
	{
		self::$enabled = true;
	}

	public static function disable()
	{
		self::$enabled = false;
	}
}
