<?php 
function getDefaultMachineId() {
    labelCode("component.php", "getDefaultMachineId");
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8  
    $query = "SELECT machines.id_machines FROM machines LIMIT 0, 1";
    $result = mysqli_query($link, $query);  
    while ($row = mysqli_fetch_array($result)) {
        return $row["id_machines"];
    } 

    logger("Не найдено ни одной машины");
    return 0;    
}

function getMachineContent($id) {
    labelCode("component.php", "getMachineContent");  
    $table = getMachineTable($id);
    return Array("header" => getMachineHeader($id), "content" => "<div id=\"content-data\">$table</div>");
}

function getMachineHeader($id) {
    labelCode("component.php", "getMachineHeader");
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8  
    $query = "SELECT machines.name_machines FROM machines WHERE machines.id_machines=$id";
    $result = mysqli_query($link, $query);  
    $header = "Заголовок не определен";
    while ($row = mysqli_fetch_array($result)) {
        $header = $row["name_machines"];
    } 

    return "<div id='content-header'><h2>$header</h2></div>";
}

function getMachineTable($id) {
    labelCode("component.php", "getMachineTable");

    if ($id == "default") {
        $id = getDefaultMachineId();
    }

    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8 
   
    $query_diff = "SELECT machines.id_machines, machines.name_machines, units.id_units, units.name_units, units.info_units, units.pozname_units, units.id_categories, c1.id_units, c1.date_control, c1.notes_control, categories.id_categories, categories.periodicy 
        FROM units, control c1, machines, categories 
        WHERE units.id_units=c1.id_units 
        AND units.id_machines=machines.id_machines 
        AND units.id_categories=categories.id_categories 
        AND c1.id_control=(SELECT MAX(c2.id_control) FROM control c2 where c2.id_units = c1.id_units)  
        AND units.id_machines=$id 
        GROUP BY units.name_units
        ORDER BY `units`.`id_units` ASC";

    $result_diff = mysqli_query($link, $query_diff);

    $table = "<table><tr><th><img src='img/tick-button.png' alt='Отметка о выполнении'></th>
    <th>Узел</th>
    <th>Инфо</th>
    <th class='hide-col'>Предыдущая проверка</th>
    <th class='hide-col'>Период-ть</th>
    <th class='hide-col'>Дней осталось</th>
    <th>Замечания</th></tr>";

    $current_day = date("d-m-Y");
    while ($row = mysqli_fetch_array($result_diff)) {
        $date_current = date_create($current_day);
        $date_control = date_create($row['date_control']);
        $date_deadline = date_add($date_control, date_interval_create_from_date_string($row['periodicy'] . "days")); // Окончания периода
        $interval = date_diff($date_current, $date_deadline);

        $diff = $interval -> format("%a");

                   
        $icon = "class = 'days-before'";
        
        $result_date = $date_deadline < $date_current;
        if ($result_date){
            $icon = "class = 'days icon warning-icon' title='Просрочено!'";
            $diff = "-".$diff;
        }

        $date_control_rev = date("d-m-Y", strtotime($row['date_control']));
        $table .=  "<tr id = row". $row['id_units'] . " machine='" . $row["id_units"] . "'>
        <td><input type='checkbox' name='a' value='10'></td>
        <td id = col_1>".$row['name_units']." ".$row['pozname_units']."</td>
        <td>".$row['info_units']."</td>
        <td class='hide-col'>".$date_control_rev."</td>
        <td class='hide-col'>".$row['periodicy']."</td>
        <td ".$icon.">".$diff."</td>
        <td class='col-notes' id=col_notes>".$row['notes_control']."</td></tr>";
    }
    mysqli_close($link);
    $table .="</table>";

    return $table;
}

//Построение списка агрегатов в сайдбар
function getMachineList() {
    labelCode("component.php", "getMachineList");
    
    $result = "<div id=\"sidebar\">
        <p id=head-menu>Оборудование</p>
        <input id=\"sidebar-search\" type =\"text\" placeholder=\"Поиск...\" />";
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8 

    $query = "SELECT name_machines, id_machines FROM machines"; //ЗАПРОС
    $result_menu = mysqli_query($link, $query);
    
    //ОТРИСОВКА МЕНЮ
        //ШАПКА
        //echo "Оборудование";        
        //ТЕЛО
    $result .= "<ul id='menu'>";
            //$i=0;
    while ($row = mysqli_fetch_array($result_menu)) {
        //$i++;
        $result .= '<li><a class="onepage-link" href="#machine=' . $row["id_machines"] . '" title="'. $row["name_machines"] .'">' . $row["name_machines"] . '</a></li>';
    }
    $result .= "</ul>";
    mysqli_close($link);

    $result .= "</div>";
    return $result;   
}

function getPlanContent($id) {
    labelCode("component.php", "getPlanContent");
    $table = getPlanTable($id);   

    return Array("header" => getPlanHeader($id), "content" => "<div id=\"content-data\">$table</div>");
}

function getPlanHeader($id) {
    labelCode("component.php", "getPlanHeader");
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8  
    $query = "SELECT machines.name_machines FROM machines WHERE machines.id_machines=$id";
    $result = mysqli_query($link, $query);  
    $header = "Заголовок не определен";
    while ($row = mysqli_fetch_array($result)) {
        $header = $row["name_machines"];
    } 
    
    return 
        "<p id='content-header'>" .
            "<a class ='nav onepage-link' href='#machine=$id'>$header</a> -> План на обслуживание" .
        "</p>";
}

function getPlanTable($id) {
    labelCode("component.php", "getPlanTable");
    $table = "<table><tr><th><img src='img/tick-button.png' alt='Отметка о выполнении'></th>
    <th>Узел</th>
    <th>Инфо</th>
    <th class='hide-col'>Предыдущая проверка</th>
    <th class='hide-col'>Период-ть</th>
    <th class='hide-col'>Дней осталось</th>
    <th>Замечания</th></tr>";
    
    $link = mysqli_connect("localhost", "root", "", "desk");
            mysqli_set_charset($link, "utf8"); //кодировка в utf8 

    $query = "SELECT c1.date_control, 
        categories.periodicy, 
        date_add(c1.date_control, interval categories.periodicy day) as deadline, 
        datediff(date_add(c1.date_control, interval categories.periodicy day),curdate()) as diff,
        machines.id_machines, machines.name_machines,
        date_ADD(case 
        WHEN WEEKDAY(CURDATE()) + 3 > 6 THEN date_add(CURDATE(), interval 7 - WEEKDAY(CURDATE()) + 3 day)
        else date_add(CURDATE(), interval 3 - WEEKDAY(CURDATE()) day)
        END, interval 7 day),
        units.id_units, 
        units.name_units, 
        units.info_units, 
        units.pozname_units, 
        units.id_categories, 
        c1.id_units, 
        c1.notes_control, 
        categories.id_categories
        FROM units, control c1, machines, categories 
        WHERE units.id_units=c1.id_units 
        AND units.id_machines=machines.id_machines 
        AND units.id_categories=categories.id_categories 
        AND c1.date_control = (SELECT MAX(c2.date_control) 
                             FROM control c2 
                             where c2.id_units = c1.id_units)
        
        AND units.id_machines=$id
        and date_add(c1.date_control, interval categories.periodicy day) < date_ADD(case 
        WHEN WEEKDAY(CURDATE()) + 3 > 6 THEN date_add(CURDATE(), interval 7 - WEEKDAY(CURDATE()) + 3 day)
        else date_add(CURDATE(), interval 3 - WEEKDAY(CURDATE()) day)
        END, interval 7 day)
        GROUP BY units.name_units
        ORDER BY `units`.`id_units` ASC" ;

    $result = mysqli_query($link, $query);   
          
    while ($row = mysqli_fetch_array($result)) {
        $icon = "class = 'days-before'";            
        if ($row['diff'] < 0) {               
            $icon = "class = 'days icon warning-icon' title='Просрочено!'";
        }
        
        $header = "<p id='content-header'><a class ='nav onepage-link' href='#machine=" . $row["id_machines"] . "'>" . $row["name_machines"] . "</a> -> План на обслуживание </p>";
        $table .=  "<tr id=\"" . $row["id_units"] .  "\" machine='" . $row["id_units"] . "'>
        <td><input type='checkbox' name='a' value='10'></td><td id = col_1>".$row['name_units']." ".$row['pozname_units']."</td>
        <td>".$row['info_units']."</td>
        <td class='hide-col'>".$row['date_control']."</td>
        <td class='hide-col'>".$row['periodicy']."</td>
        <td ".$icon.">".$row['diff']."</td>
        <td class='col-notes' id=col_notes>".$row['notes_control']."</td></tr>";   
    }
    $table .="</table>";        
    mysqli_close($link); 
    return $table;    
}

function getCombos($id) {
    labelCode("component.php", "getCombos");
    //Формирование в последней строчке выпадающего списка узлов
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8 
    $query = "SELECT name_units,id_units,pozname_units FROM `units` WHERE id_machines =".$id; //Список узлов в селект
    $result = mysqli_query($link, $query);     
    $resultOut ="<div><p class = 'add-record'>Добавление записи в таблицу:</p>Узел: <select id='id_select'>";
    while ($row = mysqli_fetch_array($result)) {
        $resultOut.="<option value=".$row['id_units'].">".$row['name_units']." ".$row['pozname_units']."</option>";
    }
    mysqli_close($link);

    $resultOut .= "</select>Дата: <input type='date' value=".date('Y-m-d')." id='input_date_control_units'></div>";//Выбор даты
                    
    $resultOut .="<div id='comment'><p>Комментарий:</p>";   
    $resultOut .='<textarea name="input_notes_table" id="input_notes" cols="71" rows="1"></textarea></div>';    

    return "<div id=\"combos\">$resultOut</div>";
}

function getActionsLinks($id) {
    labelCode("component.php", "getActionsLinks");
    return 
        "<div class=\"links-container\">
            <div class=\"link\">
                <a id=\"save-link\" title=\"Применить изменения\" href=\"javascript: void(0);\">ПРИМЕНИТЬ</a>       
            </div>
            <div class=\"link\">
                <a id=\"mkplan-link\" class=\"onepage-link\" title=\"Сформировать план на предстоящий четверг\" href=\"#plan=$id\">СФОРМИРОВАТЬ ПЛАН</a>        
            </div>
            <div class=\"link\">
                <a id=\"print-link\" title=\"Таблица на печать\" href=\"javascript: void(0);\">ПЕЧАТЬ</a>       
            </div>  
            <div class=\"clear\"></div>
        </div>";    
}

function getProblemsPanel($id) {
    labelCode("component.php", "getProblemsPanel");
    
    $sql = "SELECT * FROM problems";  //Формируем таблицу проблем: по единице оборудования или по всем станкам
    $appendTOsql = "and machines.id_machines=" . $id;

    //SELECT * FROM problems, machines, users WHERE machines.id_machines=problems.id_machine and users.id_user=machines.respons_machines
    if ($id == "default"){
        $appendTOsql = "";
    } 
    logger("В функции getProblemsPanel id = " . $id);
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8 
    $query = "SELECT * FROM problems, machines WHERE machines.id_machines=problems.id_machine " . $appendTOsql; //WHERE id_machine =.$id; //
    logger("getProblemsPanel() ".$query);
    $result = mysqli_query($link, $query);
    //logger($result);
    $block = "<div class=\"maket\">
        <h2>Список текущих проблем:</h2>
            <table class=\"problem\">
                <tr><th class=\"fst-col\"><input type=\"checkbox\"></th>
                <th>Оборудование</th>
                <th>Проблема</th>
                <th>Дата</th>
                <th>Примечания</th>
                <th>Ответственный</th></tr>";

    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        
        $i++;
        $block .= "<tr><td>$i</td><td>".$row['name_machines']."</td><td>".$row['name_problems']."</td><td>".$row['date_problems']."</td><td>".$row['notes_problems']."</td></tr>"; 
    }
    mysqli_close($link);
    $block .= "</table></div>"; 
    return $block;    
}

function getControlsPanel($id) {
    labelCode("component.php", "getControlsPanel");
    return 
        "<div class=\"links-container\">
            <div class=\"link\">        
                <a id=\"add-link\" title=\"Добавить запись о проверке\" href=\"javascript: void(0);\">ДОБАВИТЬ ЗАПИСЬ</a>
            </div>
            <div class=\"clear\"></div>
        </div>";
}

function inputProblemsPanel() {
    labelCode("component.php", "inputProblemsPanel");
    
    $resultOut = 
        "<div class = input-panel>
            <h2>Создание новой записи:</h2>            
            <div id=\"inputs\">
                <div class=\"select-list\">
                    <p>Ответственный за оборудование:</p>
                    <select id=\"respons\">
                    <option></option>";       //Список ответственных за оборудование

    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8 
    $query = "SELECT id_user, name_user, priority_user FROM `users` WHERE priority_user=3 ORDER BY `users`.`name_user` ASC"; //    
    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_array($result)) {
        $resultOut .= "<option value=" . $row['id_user'] . ">" . $row['name_user'] . "</option>";
    }
    mysqli_close($link);        
    
    $resultOut .=
                    "</select>
                </div>
                <p>Оборудование</p>
                <div class=\"select-list\" id=\"select-machine-list\"> " .
                    getSelectMachineList() .
                "</div>
                <p>Проблема:</p>
                <p>
                    <textarea id=\"name-problems\"type=\"text\" placeholder=\"Краткое описание проблемы. \nНаример: «Плохо срабатывает конечник»\" required></textarea>
                    <span class=\"validity\"></span>
                </p>
                <p>Дата:</p>
                <p>
                    <input id=\"date-problems\"type=\"date\" placeholder=\"Дата\" required value=\"" . date('Y-m-d') . "\">
                    <span class=\"validity\"></span> 
                </p>
                <p>Примечания:</p>
                <p> 
                    <textarea id=\"notes-problems\"type=\"text\" placeholder=\"Что необходимо сделать для устранения проблемы. \nНаример: «Проворачивает флажок, необходимо заменить»\" required></textarea> 
                    <span class=\"validity\"></span> 
                </p>
                <div class=\"links-container\"> 
                    <div class=\"link\">
                        <a id=\"add-problem-link\" href=\"javascript: void(0);\">ДОБАВИТЬ ЗАПИСЬ</a>
                    </div>                
                </div>
            </div>
        </div>"; //источник: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/text
    return $resultOut;
}

function getSelectMachineList($userId = "") {                             //Функция вывода списка оборудования в выпадающий список. 
    labelCode("component.php", "getSelectMachineList");
    $select =
        "<select id=\"machine-list-problems\">
            [#items#]
        </select>";

    $items = "<option></option>";     

    if ($userId != "") {
        $link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8");                          //кодировка в utf8 
        $query = "SELECT name_machines, id_machines FROM machines WHERE respons_machines = $userId"; //ЗАПРОС
        logger($query);

        $result = mysqli_query($link, $query);    
        
        while ($row = mysqli_fetch_array($result)) {
            $items .= "<option value=" . $row['id_machines'] . ">" . $row['name_machines'] . "</option>";        
        }

        mysqli_close($link);
    }   

    return str_replace("[#items#]", $items, $select); 
}

function wrapElements($class, $targetContent){
    labelCode("component.php", "wrapElements");
    return "<div class = \"$class\">$targetContent</div>"; //Оборачивает содержимое в div
}
?>