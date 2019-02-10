<?php
	include_once("getdata.php");
	if (isset($_GET["type"]) && $_GET["type"] == "page") {
		$id = 1;
		if (isset($_GET["id"]) && $_GET["id"] != "") {
		    $id = $_GET["id"];
		}
		echo getContent($id); 
	} else if (isset($_POST["type"]) && $_POST["type"] == "save") {
		applyChanges($_POST["ids"]);

		if (isset($_POST["pageType"]) && $_POST["pageType"] == "plan") {
			echo getPlan($_POST["page"]);
		} else {
			echo getContent($_POST["page"]);		
		}
	} else if (isset($_POST["type"]) && $_POST["type"] == "add") {
		addRecord($_POST['sel'], $_POST['date_control'], $_POST['input_notes']);
		
		if (isset($_POST["pageType"]) && $_POST["pageType"] == "plan") {
			echo getPlan($_POST["page"]);
		} else {
			echo getContent($_POST["page"]);		
		}
	} else if (isset($_GET["type"]) && $_GET["type"] == "combos") {
		echo getCombos($_GET["id"]);		
	} else if (isset($_GET["type"]) && $_GET["type"] == "plan") {
		echo getPlan($_GET["page"]);
	}
?>