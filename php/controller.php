<?php
	include_once("utils.php");
	include_once("component.php");

//Обновление дат в таблице ТО по станкам
	if (isParamEquals($_POST, "action", "save")) {									
		applyChanges($_POST["items"]);

		echo getComponentByName($_POST["pageName"]);	
	} else if (isParamEquals($_POST, "action", "add")) {							
//Добавление записи в таблицу ТО
		addRecord($_POST['sel'], $_POST['date_control'], $_POST['input_notes']);
		
		echo getComponentByName($_POST["pageName"]);
	} else if (isParamEquals($_POST, "action", "add-problem")) {					
//Добавление записи в таблицу проблем
		addProblem($_POST['selIdMachine'], $_POST['nameProblem'], $_POST['dateProblem'], $_POST['noteProblem']);
		
		echo getProblemsPanel($_POST["pageId"]);
	} else if (isParamEquals($_POST, "action", "delete")) { 						
//Удаление записи в таблице проблем
		deleteProblem($_POST["items"]);

		echo getProblemsPanel($_POST["pageId"]);
	} else if (isParamEquals($_POST, "action", "get-select-machine-list")) {		
//Вывод списка оборудования в выпадающий список на странице проблем
		echo getSelectMachineList($_POST["userId"]);		
		
	}else if (isParamEquals($_POST, "action", "btn-to-select")){
		//echo selectOfStatusProblem();

	}else if (isParamEquals($_POST, "action", "select-to-btn")){
		echo getBtnProblem($_POST["sel-value"], $_POST["cur-row"]);
	}else if (isParamEquals($_POST, "action", "problems-plan")){		//Нерешенные проблемы
			logger("problems-plan controller.php");
			echo getProblemPlanTable();
	}		

	function getComponentByName($name) {
		$content = "Содержимое отсутствует";
		if (isParamEquals($_POST, "pageName", "plan")) {
			$content = getPlanTable($_POST["pageId"]); 
		} else {
			$pageId = $_POST["pageId"];
			if ($pageId == "default") {
				$pageId = getDefaultMachineId();
			}
			$content = getMachineTable($pageId);
		}	

		return $content;	
	}

	function applyChanges($items) {
	    $link = mysqli_connect("localhost", "root", "mysql", "desk");
	    mysqli_set_charset($link, "utf8");   
	    foreach ($items as $value) {
	         $query = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '".$value."', '".date('Y-m-d')."', '5', 'ok')";

	         mysqli_query($link, $query);
	     }    
	}

	function addRecord($sel, $dateControl, $inputNotes) {
	        $link = mysqli_connect("localhost", "root", "mysql", "desk");
	        mysqli_set_charset($link, "utf8"); 
	        
	        $query = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '$sel', '$dateControl', "."'4'".", '$inputNotes')";

	        mysqli_query($link, $query);
	        mysqli_close($link);
	}

	function addProblem($selIdMachine, $nameProblem, $dateProblem, $noteProblem){
			$link = mysqli_connect("localhost", "root", "mysql", "desk");
	        mysqli_set_charset($link, "utf8"); 
	        
	        $query = "INSERT INTO `problems` (`id_problems`, `name_problems`, `date_problems`, `notes_problems`, `id_units_problems`, `status_problems`, `id_machine`) VALUES (NULL, '$nameProblem', '$dateProblem', '$noteProblem', NULL, '1', '$selIdMachine')";
	        
	        mysqli_query($link, $query);
	        mysqli_close($link);

	}
	function deleteProblem($items){
		logger("deleteProblem, controller.php");

		$link = mysqli_connect("localhost", "root", "mysql", "desk");
	    mysqli_set_charset($link, "utf8");   
	    foreach ($items as $value) {
	         $query = "DELETE FROM `problems` WHERE `problems`.`id_problems` =" . $value;
	         //logger("function delete_problem " . $query);
	         	//DELETE FROM `problems` WHERE `problems`.`id_problems` = 7;
				//DELETE FROM `problems` WHERE `problems`.`id_problems` = 8;
	         mysqli_query($link, $query);
	     }    
	}



		
?>