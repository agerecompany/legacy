<?php
namespace Agere;

class Debug {
	public static function dump() {
		ob_start();
		foreach(func_get_args() as $var) {
			var_dump($var);
		}
		$dump = ob_get_clean();
		echo "<div class='dump'><pre>" . $dump . "</pre></div>";
	}
}