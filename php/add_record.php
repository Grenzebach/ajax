<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<title>Добавление записи в таблицу</title>
</head>
<body>
	<p>Добавление записи в таблицу</p>
	<p>	INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '27', '2019-01-15', '4', 'ок');</p>
	
	<?php	
		$link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8"); //кодировка в utf8 
        $query = "SELECT name_units FROM `units` WHERE id_machines = 1";
        $result = mysqli_query($link, $query); 
	
	echo "<p>Выбрать узел</p> 
	<select>";
	while ($row = mysqli_fetch_array($result)) {
			echo "<option value='id_select_'".$i++.">".$row['name_units']."</option>";
	}
		
	echo "</select>";

	?>
	<p>Доп. информация по узлу</p>
	<input type="text" id="input_info_units">			
	<p>Дата проверки</p>
	<input type="date" id="input_date_control_units">	
	<p>Периодичность обслуживания</p>
	<select name="periodicy"  id="periodicy_select">
		<option value=""></option>
	</select>   	
	<p>Примечания</p>
	<textarea name="input_notes" id="1" cols="30" rows="10"></textarea>
	<a id="save-link" title="Применить изменения" href="javascript: void(0);">ПРИМЕНИТЬ</a>			
</body>
</html>