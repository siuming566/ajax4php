<?php 
//
// auth.inc - Auth checker
//

if (!isset($_SESSION["a4p._auth"]))
{
	header("Location: index.php");
	exit();
}
