<?php

if (config::$debug == false) {
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	libxml_use_internal_errors(true);
}
