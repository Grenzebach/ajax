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
    <div id="navigation"></div>
    <div id="sidebar">
    	<p id=head-menu>Оборудование</p>
    <?php 
	    
    ?>
    
    </div>
    <script type="text/javascript" src="js/main.js"></script>
    <div id="content">
    	
    	<div id="content-data"> 
    	<?php
    		$result = "<table style = "border-color=#cd66cc"><tr>';
    		for($i=1; $i<54; $i++)
    		{
    			$result .= "<td>$i</td>";

    			
    		}
    		$result .= "</tr></table>";
    		echo $result;
    	?> 
    	</div>

    	<div class="links-container">
    		<div class="link">
    			<a id="save-link" title="Применить изменения" href="javascript: void(0);">ПРИМЕНИТЬ</a>		
    		</div>	
			<div class="clear"></div>
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
	<a href="http://localhost:8080/phpmyadmin/">phpmyadmin HOME</a>
    </div>
</div>
	
	
	
</body>
</html>