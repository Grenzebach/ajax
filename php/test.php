<?php
	include_once("getdata.php");

	echo json_encode(Array("error" => "", "data" =>utf8_encode(getContent(1))), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);	
?>