<?php 
//
// auth.inc - Auth checker
//

if (!a4p::isLoggedIn())
{
	header("Location: index.php");
	exit();
}
