<?php 
// Class file name must be classname.class.php

class feed1Controller
{
	// setup flag to enable ajax call from browser
	public $enableAjaxCall = true;

	public function ask($param)
	{
		// push javascript to browser
		push::execJS( <<< END
document.getElementById('outputtext2').value = 'Start ...\\n';
END
);

		// push javascript to browser and wait for feedback
		$result = push::execJS( <<< END
confirm('Answer Question?');
END
, 30000);

		if ($result == "true") {
			// push javascript to browser and wait for feedback
			$input = push::execJS( <<< END
prompt('Type something?');
END
, 30000);

			// push javascript to browser
			push::execJS( <<< END
document.getElementById('outputtext2').value += 'You typed $input ...\\n';
END
);
		} else if ($result == "false") {
			// push javascript to browser and wait for feedback
			push::execJS( <<< END
document.getElementById('outputtext2').value += 'You pressed cancel ...\\n';
END
);
		}

		// push javascript to browser
		push::execJS( <<< END
document.getElementById('outputtext2').value += 'Finish\\n';
END
);
	}
	
}
