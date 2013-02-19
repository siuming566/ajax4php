<?php
require "a4p/routing.inc.php";

routing::setup(array(
	"helloworld" => "helloworldController",
	"page1" => "page1Controller",
	"page2" => "page2Controller",
	"rerender1" => "rerender1Controller",
	"calculator1" => "calculator1Controller",
	"javascript1" => "javascript1Controller",
	"popup1" => "_defaultController",
	"popup2" => "popup2Controller",
	"push1" => "push1Controller",
	"feed1" => "feed1Controller",
	"datatable1" => "datatable1Controller",
	"fileupload1" => "fileupload1Controller",
	"control(1|2)" => "_defaultController",
	"layout(1|2)" => "_defaultController",
));
