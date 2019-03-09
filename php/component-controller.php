<?php
	include_once("utils.php");
	include_once("component.php");

	if (isParamEquals($_GET, "name", "statusList")) {
		echo getStatusList();
	}
?>