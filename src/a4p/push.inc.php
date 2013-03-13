<?php 
//
// push.inc - Push Javascript
//

class push
{
	private static $polling_filename = "";

	public static function create($poll_id)
	{
		$yesterday = strtotime(config::$tmp_expire_time);
		foreach (glob(config::$tmp_path . DIRECTORY_SEPARATOR . "push_*") as $oldfile) {
			if (filemtime($oldfile) < $yesterday)
				unlink($oldfile);
		}

		self::$polling_filename = config::$tmp_path . DIRECTORY_SEPARATOR . "push_" . $poll_id;
		$polling_file = fopen(self::$polling_filename . ".lck", "w");
		fclose($polling_file);
		$polling_file = fopen(self::$polling_filename, "w");
		fclose($polling_file);
	}

	public static function execJS($js, $timeout = 0)
	{
		if (self::$polling_filename == "")
			return "";

		$polling_file = fopen(self::$polling_filename, "a");
		fwrite($polling_file, $js);
		fclose($polling_file);

		$feed = "";
		if ($timeout > 0) {

			set_time_limit($timeout / 1000 + 5);

			$feed_filename = self::$polling_filename . ".feed";

			$count = 0;
			$max_count = $timeout / 100;
			while (!file_exists($feed_filename) && ++$count < $max_count) {
				usleep(100000);
				clearstatcache();
			}

			if (file_exists($feed_filename)) {
				$feed = file_get_contents($feed_filename);
				unlink($feed_filename);
			} else
				$feed = false;
		}

		return $feed;
	}

	public static function poll($poll_id, $pos, $feed)
	{
		$polling_filename = config::$tmp_path . DIRECTORY_SEPARATOR . "push_" . $poll_id;
		
		if ($feed != "" && file_exists($polling_filename . ".lck")) {
			$polling_file = fopen($polling_filename . ".feed", "w");
			fwrite($polling_file, $feed);
			fclose($polling_file);
		}
		
		$count = 0;
		while (file_exists($polling_filename) && !(filesize($polling_filename) > $pos) && ++$count < 100) {
			usleep(100000);
			clearstatcache();
		}

		if (!file_exists($polling_filename))
			return "@END@";
		
		$js = "";

		if (filesize($polling_filename) > $pos) {
			$polling_file = fopen($polling_filename, "r");
			fseek($polling_file, $pos);
			$js = fread($polling_file, filesize($polling_filename) - $pos);
			fclose($polling_file);
		}

		if (!file_exists($polling_filename . ".lck"))
			unlink($polling_filename);

		return $js;
	}

	public static function remove($poll_id)
	{
		if (self::$polling_filename == "")
			return;
		if (file_exists(self::$polling_filename . ".lck")) {
			unlink(self::$polling_filename . ".lck");
			if (filesize(self::$polling_filename) == 0)
				unlink(self::$polling_filename);
		}
	}
}
