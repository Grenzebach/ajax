<?php
	include_once("utils.php");
	include_once("component.php");

	if (isParamEquals($_GET, "name", "statusList")) {
		echo getStatusList();
	} else if (isParamEquals($_GET, "name", "get-problem-panel")){
		echo inputProblemsPanel();
		logger("inputProblemsPanel");

	} else if (isParamEquals($_GET, "name", "tablePage")) {
		if (isParamEquals($_GET, "type", "problems")) {
			echo getProblemsTablePage($_GET["page"], $_GET["id"], $_GET["currentPage"], $_GET["rowsPerPage"]);
		}
	}
?>