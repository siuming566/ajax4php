<?php

class config
{
	// db connection settings
	public static $connect_string = "mysql:host=;dbname=";
	public static $user = "";
	public static $pass = "";

	// server port settings for SSL
	public static $http_port = 80;
	public static $ssl_port = 443;
	
	// turn on/off debug message 
	public static $debug = true;

	// temp directory for working files, default use session_save_path() if null
	public static $tmp_path = null;

	// expire time for working files
	public static $tmp_expire_time = "-1 days";
}

ini_set('date.timezone', 'Asia/Hong_Kong');
