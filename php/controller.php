<?php
	include_once("utils.php");
	include_once("component.php");

	if (isParamEquals($_POST, "action", "save")) {//Actions.php
		applyChanges($_POST["items"]);

		echo getComponentByName($_POST["pageName"]);
	} else if (isParamEquals($_POST, "action", "add")) {//Actions.php
		addRecord($_POST['sel'], $_POST['date_control'], $_POST['input_notes']);
		
		echo getComponentByName($_POST["pageName"]);
	}

	function getComponentByName($name) {
		$content = "Содержимое отсутствует";
		if (isParamEquals($_POST, "pageName", "plan")) {
			$content = getPlanTable($_POST["pageId"]); 
		} else {
			$content = getMachineTable($_POST["pageId"]);
		}	

		return $content;	
	}

	function applyChanges($items) {
	    $link = mysqli_connect("localhost", "root", "", "desk");
	    mysqli_set_charset($link, "utf8");   
	    foreach ($items as $value) {
	         $query = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '".$value."', '".date('Y-m-d')."', '5', 'ok')";

	         mysqli_query($link, $query);
	     }    
	}

	function addRecord($sel, $dateControl, $inputNotes) {
	        $link = mysqli_connect("localhost", "root", "", "desk");
	        mysqli_set_charset($link, "utf8"); 
	        
	        $query = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '$sel', '$dateControl', "."'4'".", '$inputNotes')";

	        mysqli_query($link, $query);

	        mysqli_close($link);
	}	
?>