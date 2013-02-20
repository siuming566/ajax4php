<?php

class template
{
	private static $sections = array();
	private static $env = array();

	public static function View($template, $sections, $env = array())
	{
		self::$sections = $sections;
		self::$env = $env;

		global $controller;
		global $model;
		$model = a4p::$currentModel;
		require SITE_ROOT . "/view/" . $template;
	}

	public static function section($section)
	{
		if (isset(self::$sections[$section]))
		{
			global $controller;
			global $model;
			$env = self::$env;
			require SITE_ROOT . "/view/" . self::$sections[$section];
		}
	}
}
