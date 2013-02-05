<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class control2Controller
{
	/** @ajaxcall */
	public function initUserControl1($param)
	{
		$control = a4p::Controller("usercontrol1Controller");
		$control->setValue("1");
	}

	/** @ajaxcall */
	public function initUserControl2($param)
	{
		$control = a4p::Controller("usercontrol2Controller");
		$control->setX("2");
		$control->setY("3");
	}
	
}
