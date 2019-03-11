<?php
	function logger($msg) {
	    $filename = "log";
	    if (!file_exists($filename)) {        
	        mkdir($filename, 0777, true);
	    }
	    $filedata = $filename.'/log_' . date('d-M-Y') . '.log';    
	    file_put_contents($filedata, date('d-m-Y h:i:s') . " " . $msg . "\n", FILE_APPEND);
	}

	function isParamEquals($method, $param, $expected) {
		return isset($method[$param]) && $method[$param] == $expected;
	}

	function isParamExists($method, $param) {
		return isset($method[$param]) && $method[$param] != "";	
	}

	function labelCode($file, $method) {
		echo "<!-- $file:$method -->";
	}
?>