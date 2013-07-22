<?php 
//
// routing.inc - Routing
//

class plugin
{
	private static $plugin_list = array();

	public static function register($list)
	{
		self::$plugin_list = array_merge(self::$plugin_list, $list);
	}

	public static function element($name)
	{
		foreach(self::$plugin_list as $plugin) {
			$plugin_file = $_SERVER['DOCUMENT_ROOT'] . '/plugin/' . $plugin . '/' . $name . '.php';
			if (file_exists($plugin_file))
				include($plugin_file);
		}
	}
}
