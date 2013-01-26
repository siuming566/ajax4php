<?php 
// Class file name must be classname.class.php

/** @ajaxenable */
class push1Controller
{
	/** @ajaxcall */
	public function start($param)
	{
		// push javascript to browser
		push::execJS( <<< END
document.getElementById('outputtext').value = 'Start ...\\n';
document.getElementById('progressbar').style.width = '0px';
END
);

		for ($i = 0; $i < 10; $i++) {
			// push javascript to browser
			push::execJS( <<< END
document.getElementById('outputtext').value += 'Working ... ' + $i + '\\n';
document.getElementById('progressbar').style.width = ($i * 30) + 'px';
END
);
			// idle for some time
			sleep(2); 
		}

		// push javascript to browser
		push::execJS( <<< END
document.getElementById('outputtext').value += 'Finish\\n';
document.getElementById('progressbar').style.width = '300px';
END
);
	}
	
}
