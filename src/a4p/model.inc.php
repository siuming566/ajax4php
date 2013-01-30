<?php

class Model
{
	public function __construct($defaults)
	{
		foreach ($defaults as $key => $value)
		{
			if (property_exists($this, $key))
				$this->$key = $value;
		}
	}
}