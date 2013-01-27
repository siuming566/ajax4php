<?php 
//
// service.php - Handle webservice and wsdl requests
//

$query = $_SERVER["QUERY_STRING"];

if (strlen($query) == 0)
	exit();

if (strlen($query) > 5 && substr($query, -5) == ".wsdl")
{
	$classpath = substr($query, 0, strlen($query) - 5);
	$classname = basename($classpath);
	$is_wsdl = true;
}
else
{
	$classpath = $query;
	$classname = basename($classpath);
	$is_wsdl = false;
} 

require_once "../class/$classpath.class.php";

$class = new ReflectionClass($classname);
$comment = $class->getDocComment();
		
if (strpos($comment, "@webservice") == false)
	exit();

if ($is_wsdl)
{
	require_once "wsdl.inc.php";

	header("Content-Type: application/xml");
	
	if (isset($_SERVER["HTTPS"]))
	$protocol = "https";
	else
		$protocol = "http";

	if (strpos($_SERVER["HTTP_HOST"], ":" . $_SERVER["SERVER_PORT"]) == false)
		$host = $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"];
	else
		$host = $_SERVER["HTTP_HOST"];

	$url = $protocol . "://" . $host . $_SERVER["PHP_SELF"];
	
	$wsdl = new wsdl($classname, $url . "?" . $classname);
	echo $wsdl->generate();
}
else
{
	$server = new SoapServer(null, array("uri" => "urn:" . $classname . "wsdl"));
	$server->setClass($classname);
	$server->handle();
}
