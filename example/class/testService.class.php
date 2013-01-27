<?php 

/** @webservice */
class testService
{
	/**
	 * @webmethod
	 * @param msg string
	 * @return string
	 */
	function getText($msg)
	{
		return "Hello World " . $msg;
	}
}
