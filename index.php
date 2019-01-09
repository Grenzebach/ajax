<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Система ТОиР ЗАО "Муром"</title>
	<link rel="shortcut icon" href="img/icons8-maintenance-16.png" type="image/png">
	<link rel="stylesheet" href="css/index.css">
</head>
<body>
<div id="container">
    <div id="header">
    	<div class="logo"><a href="/"><img src="img/logo.gif" alt=""></a>
			<p id=logo_title>Система ТОиР ЗАО "Муром"</p>
    	</div>
		

    </div>
    <div id="navigation">навигация</div>
    <div id="sidebar">
    	<p id=head-menu>Оборудование</p>
    <?php 
	    //БОКОВОЕ МЕНЮ

	    $link = mysqli_connect("localhost", "root", "", "desk");
	    mysqli_set_charset($link, "utf8"); //кодировка в utf8 

		$query = "SELECT name_machines, id_machines FROM machines";	//ЗАПРОС
		$result_menu = mysqli_query($link, $query);
		
		//ОТРИСОВКА МЕНЮ
			//ШАПКА
			//echo "Оборудование";
			
			//ТЕЛО
			echo "<ul id='menu'>";
		        //$i=0;
		        while ($row = mysqli_fetch_array($result_menu)) {
		            //$i++;
		            echo '<li id=item_'.$row["id_machines"].'><a href="javascript: void(0);">' . $row["name_machines"] . '</a></li>';
		        }
		        echo "</ul>";

    ?>
    
    </div>
    <div id="content">
    	
    	<?php

    	//Функция возвращает разницу между датами; %а - результат в днях
    	function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
			{
    			$datetime1 = date_create($date_1);
    			$datetime2 = date_create($date_2);

    
    			$interval = date_diff($datetime1, $datetime2);
    
    	return $interval->format($differenceFormat);
    
}
    		//разница в датах
    	$query_diff = "SELECT units.name_units,control.date_control,control.date_prev_control,control.id_units,categories.periodicy FROM control, categories, units where control.id_units = units.id_units and categories.id_categories = units.id_categories";
    	$result_diff = mysqli_query($link, $query_diff);
    	echo "<table><tr><th>Узел</th><th>Текущая дата</th><th>Предыдущая проверка</th><th>Период-ть</th><th>Дней осталось</th></tr>";
    	
    	//echo date("d-m-Y");
    	$current_day = date("d-m-Y");

    	//echo dateDifference("2018-10-31","2018-12-25");
    	    	
    	while ($row = mysqli_fetch_array($result_diff)) {
		           
    				$date_current = date_create($current_day); //Теущая дата
    				$date_control = date_create($row['date_control']); //Дата последней проверки
					$date_deadline = date_add($date_control,date_interval_create_from_date_string($row['periodicy']."days")); // Окончания периода
    				$interval = date_diff($date_current, $date_deadline);	//Разница между датами
    				
    				$diff = $interval -> format("%a"); //Количество дней в строку
    				
		           	$id_color = "td_green"; //Цвет ячейки
		           if ($diff < 3){
		           		$id_color = "td_yellow";
		           }  elseif ($diff < 0){
		           		$id_color = "td_red";
		           }
		           
		           $date_control_rev = date("d-m-Y",strtotime($row['date_control'])); // изменение формата даты из Y-m-d в d-m-Y
		            echo "<tr><td id = col_1>".$row['name_units']."</td><td>".$current_day."</td><td>".$date_control_rev."</td><td>".$row['periodicy']."</td><td id=".$id_color.">".$diff."</td></tr>";
		        }
					echo "</table>";

			echo "<p></p>";
			

			//СПИСОК ДВИГАТЕЛЕЙ
			//УСТАНОВКА СОЕДИНЕНИЯ С БД	
			$link = mysqli_connect("localhost", "root", "", "desk");
			if (!$link) {
			    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
			    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
			    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
			    exit;
			}      

				$query = "SELECT * FROM units";	//ЗАПРОС К БАЗЕ УЗЛОВ
				mysqli_set_charset($link, "utf8"); //кодировка в utf8
				$result = mysqli_query($link, $query);
				
			//ОТРИСОВКА ТАБЛИЦЫ
				//ШАПКА
				echo "<table><tr><th>№</th><th>Функция</th><th>Мощность, кВт</th></tr>";
				//ТЕЛО
				
				while ($row = mysqli_fetch_array($result))
					echo "<tr><td>", $row["id_units"], "</td><td>", $row["name_units"], "</td><td>", $row["power_motors"], "</td></tr>";
				echo "</table>";
			
			// выборка результатов проверок
				echo "<p>  </p>";
				$query_controls = "select control.id_control, machines.name_machines, units.name_units, control.date_control, control.state_control, control.notes_control from control, machines, units where units.id_units = control.id_units and machines.id_machines = units.id_machines";
				$result_controls = mysqli_query($link, $query_controls);
			//ШАПКА
				echo "<table><tr><th>№</th><th>Оборудование</th><th>Узел</th><th>Дата проверки</th><th>Состояние</th><th>Замечания</th></tr>";
			//ТЕЛО
				while ($row = mysqli_fetch_array($result_controls))
					echo "<tr><td>", $row["id_control"], "</td><td>", $row["name_machines"], "</td><td>", $row["name_units"], "</td><td>", $row["date_control"], "</td><td>", $row["state_control"], "</td><td>", $row["notes_control"], "</td></tr>";
				echo "</table>";

				mysqli_close($link); //ЗАКРЫТИЕ СОЕДИНЕНИЯ
				


	 ?>
		

    </div>
    <div id="clear"></div>
    <div id="footer"><a href="http://localhost/phpmyadmin/">phpmyadmin</a>
	<p><a href="http://sublimetext.ru/documentation/hotkeys/windows">Горячие клавиши</a></p>
	<a href="http://localhost:8080/phpmyadmin/">phpmyadmin HOME</a>
    </div>
</div>
	
	
	
</body>
</html>