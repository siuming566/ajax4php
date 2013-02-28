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
}
