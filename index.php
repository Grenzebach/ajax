<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Система ТОиР ЗАО "Муром"</title>
	<link rel="shortcut icon" href="img/icons8-maintenance-16.png" type="image/png">
	<link rel="stylesheet" href="css/index.css">
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
</head>
<body>
<div id="container">
    <div id="header">
    	<div class="logo"><a href="/"><img src="img/logo.jpg" alt=""></a>
			
    	</div>
		<div id='title'><p id=logo_title>Система ТОиР ЗАО "Муром"</p>
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
		            echo '<li value='.$row["id_machines"].'><a href="#machine=' . $row["id_machines"] . '">' . $row["name_machines"] . '</a></li>';
		        }
		        echo "</ul>";
		        mysqli_close($link);
    ?>
    
    </div>
    <script type="text/javascript" src="js/main.js"></script>
    <div id="content">
    	<div id="content-data">  
    	</div>    	
			<a id="save-link" title="Применить изменения" href="javascript: void(0);">ПРИМЕНИТЬ</a>		
			<a id="save-link" title="Добавить запись о проверке" href="php/add_record.php">ДОБАВИТЬ ЗАПИСЬ</a>
    </div>
    <div id="clear"></div>
    <div id="footer"><a href="http://localhost/phpmyadmin/">phpmyadmin</a>
	<p><a href="http://sublimetext.ru/documentation/hotkeys/windows">Горячие клавиши</a></p>
	<a href="http://localhost:8080/phpmyadmin/">phpmyadmin HOME</a>
    </div>
</div>
	
	
	
</body>
</html>