<?php
	include_once("getdata.php");
	if (isset($_GET["type"]) && $_GET["type"] == "page") {
		$id = 1;
		if (isset($_GET["id"]) && $_GET["id"] != "") {
		    $id = $_GET["id"];
		}
		echo getContent($id); 
	} else if (isset($_POST["type"]) && $_POST["type"] == "save") {
		//print_r($_POST["ids"]);
		applyChanges($_POST["ids"]);
		echo getContent($_POST["page"]);		
	} else if (isset($_POST["type"]) && $_POST["type"] == "add"){
		addRecord($_POST['sel'], $_POST['date_control'], $_POST['input_notes']);
		echo getContent($_POST["page"]);
		//echo json_encode(Array("error" => "", "data" => getContent($_POST["page"])));	
	} else if (isset($_GET["type"]) && $_GET["type"] == "combos") {
		echo getCombos($_GET["id"]);
	}
?>