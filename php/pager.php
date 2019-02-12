<?php
	include_once("utils.php");
	include_once("component.php");

	if (isParamEquals($_GET, "name", "machine")) {
		echo getMachine($_GET["id"]);
	} else if (isParamEquals($_GET, "name", "plan")) {
		echo getPlan($_GET["id"]);
	} else if (isParamEquals($_GET, "name", "machines")) {
		echo getMachines();
	} else if (isParamEquals($_GET, "name", "problems")) {
		echo getProblems();
	} else if (isParamEquals($_GET, "name", "parts")) {
		echo getParts();
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
	    	$machineHeader .
	    	$machineTable . 
	    	$actionsLinks . 
	    	$problemsPanel . 
	    	$combos . 
	    	$controlsPanel;
	}	

	function getPlan($id) {
		logger("Получение плана для $id");
	    
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

	function getMachines() {	
		return getMachineList();			
	}

	function getProblems() {
		return "Список всех проблем?";
	}

	function getParts() {
		return "Список всех запчастей?";
	}
?>