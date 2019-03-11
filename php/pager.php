<?php
	include_once("utils.php");
	include_once("component.php");

	if (isParamEquals($_GET, "name", "problems")) {					//Таблица проблем
		echo getProblems();		
	} else if (isParamEquals($_GET, "name", "plan")) {				//План на ТО
		logger("plan pager.php");
		echo getPlan($_GET["id"]);
	} else if (isParamEquals($_GET, "name", "machines")) {
		echo getMachines();
	} else if (isParamEquals($_GET, "name", "machine")) {
		echo getMachine($_GET["id"]);
	} else if (isParamEquals($_GET, "name", "parts")) {
		logger("parts pager.php");				//Запчасти
		echo getParts();
	} else if (isParamEquals($_GET, "name", "default")) {			//По-умолчанию
		echo getProblems(getDefaultMachineId());
	} else {
		echo "404 - страница не найдена";
	}	

	function getMachine($id) {	    
		logger("Получение списка узлов для $id");
		$machineList = getMachineList(); 		
		$machineContent	= getMachineContent($id);
		$machineHeader = $machineContent["header"];
		
	    $machineTable = $machineContent["content"];
	    $actionsLinks = getActionsLinks($id);
	    $problemsPanel = getProblemsPanel($id);
		$combos = getCombos($id);
		$controlsPanel = getControlsPanel($id);
	        
	    return 
	    	$machineList .	    	
	    	wrapElements("machine-content",
	    		$machineHeader . 	    	
	    		$machineTable . 
		    	$actionsLinks . 
		    	$problemsPanel . 
		    	$combos . 
		    	$controlsPanel);
	}	

	function getPlan($id) {
		logger("Получение плана для id = $id");
	    
	    $planContent = getPlanContent($id);
		$planHeader = $planContent["header"]; 
	    $planTable = $planContent["content"];
	    $actionsLinks = getActionsLinks($id);
	    $problemsPanel = getProblemsPanel($id);
		$combos = getCombos($id);
		$controlsPanel = getControlsPanel($id);
	        
	    return 
	    	$planHeader .
	    	$planTable . 
	    	$actionsLinks . 
	    	$problemsPanel . 
	    	$combos . 
	    	$controlsPanel;		
	}
	//Вывод по нвжатию в строке меню 
	function getMachines() {		//Оборудование		
		logger("Получение списка станков, id = 0");		
		return getMachine(1);
						
	}

	function getProblems() {		//Проблемы
		logger("Получение списка проблем для оборудования");				
		$table = getProblemsPanel("default");
		$buttons = downButtons();
		$inputs = inputProblemsPanel();		
		return 
			$table .
			$buttons .
			$inputs;
	}

	function getProblemsPlan(){		//Получение списка проблем на ремонт
		$table = getProblemsPanel("default");

		return
			$table;		
	}

	function getParts() {			//Запчасти
		return "Список всех запчастей?";
	}
?>