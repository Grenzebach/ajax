<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Система ТОиР ЗАО "Муром"</title>
	<link rel="shortcut icon" href="img/icons8-maintenance-16.png" type="image/png">
	<link rel="stylesheet" href="css/index.css">
	<link rel="stylesheet" href="css/print.css">
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
</head>
<body>
<div id="container">
    <div id="header">
    	<div class="logo"><a href="/"><img src="img/logo.jpg" alt=""></a>
			
    	</div>
		<div id='title'><p>Система ТОиР ЗАО "Муром"</p>
		</div>
		<div class="clear"></div>
    </div>
    <div id="navigation">
    	<ul class="nav-menu">
    		<li class="nav-item" id="machines"><a href="javascript: void(0);">ОБОРУДОВАНИЕ</a></li>
    		<li class="nav-item" id="problems"><a href="javascript: void(0);">ПРОБЛЕМЫ</a></li>
    		<li class="nav-item" id="parts"><a href="javascript: void(0);">ЗАПЧАСТИ</a></li>
    	</ul>
    	

    </div>
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

    	<div class="links-container">
    		<div class="link">
    			<a id="save-link" title="Применить изменения" href="javascript: void(0);">ПРИМЕНИТЬ</a>		
    		</div>
    		<div class="link">
    			<a id="mkplan-link" title="Сформировать план на предстоящий четверг" href="javascript: void(0);">СФОРМИРОВАТЬ ПЛАН</a>		
    		</div>
    		<div class="link">
    			<a id="print-link" title="Таблица на печать" href="javascript: void(0);">ПЕЧАТЬ</a>		
    		</div>	
			<div class="clear"></div>
		</div>
		
		<div class="maket">
			<table class="problem">
				<tr><td class="fst-col"><input type="checkbox"></td><td>Проблема</td><td>Устранение</td><td>Примечания</td></tr>
				<td></td><td></td><td></td><td></td>

			</table>
		</div>
    	<div id="combos">
    	</div>    	
    	<div class="links-container">
    		<div class="link">		
				<a id="add-link" title="Добавить запись о проверке" href="javascript: void(0);">ДОБАВИТЬ ЗАПИСЬ</a>
			</div>
			<div class="clear"></div>
		</div>
    </div>
    <div id="clear"></div>
    <div id="footer"><a href="http://localhost/phpmyadmin/">phpmyadmin</a>
	<p><a href="http://sublimetext.ru/documentation/hotkeys/windows">Горячие клавиши</a></p>
	<p><a href="http://localhost/ajax/pages/plan.php/">План</a></p>
	<a href="http://localhost:8080/phpmyadmin/">phpmyadmin HOME</a>
    </div>
</div>
	
	
	
</body>
</html>