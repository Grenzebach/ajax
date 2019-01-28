<?php
	include_once("getdata.php");
	if (isset($_GET["type"]) && $_GET["type"] == "page") {
		$id = 1;
		if (isset($_GET["id"]) && $_GET["id"] != "") {
		    $id = $_GET["id"];
		}
		echo getContent($id); 
	} else if (isset($_POST["type"]) && $_POST["type"] == "save") {
		print_r($_POST["ids"]);
		applyChanges();		
	}
		else if (isset($_POST["type"]) && $_POST["type"] == "add"){

			echo addRecord();

			//echo "<p>gertrtert</p>";
	}
?>