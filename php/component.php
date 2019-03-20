<?php 
include_once("utils.php");

function getDefaultMachineId() {
    labelCode("component.php", "getDefaultMachineId");
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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

    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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
        $table .=  "<tr id = row". $row['id_units'] . " value='" . $row["id_units"] . "'>
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
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8  
    $query = "SELECT machines.name_machines FROM machines WHERE machines.id_machines=$id";
    $result = mysqli_query($link, $query);  
    $header = "Заголовок не определен";
    while ($row = mysqli_fetch_array($result)) {
        $header = $row["name_machines"];
    } 
    
    return 
        "<p id='content-header'>" .
            "<a class ='nav onepage-link' href='#machine=$id'>$header" . " / " . "</a><span class=\"plan-header\">План на обслуживание</span>" .
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
    
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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
                <a id=\"save-link\" title=\"Обновить выделенные строки\" href=\"javascript: void(0);\">ОБНОВИТЬ</a>       
            </div>
            <div class=\"link\">
                <a id=\"mkplan-link\" class=\"onepage-link\" title=\"Сформировать план на предстоящий четверг\" href=\"#plan=$id\">СФОРМИРОВАТЬ ПЛАН</a>        
            </div>
            <div class=\"link\">
                <a class=\"print-link\" title=\"Таблица на печать\" href=\"javascript: void(0);\">ПЕЧАТЬ</a>       
            </div>  
            <div class=\"clear\"></div>
        </div>";    
}

function contentHeader($header){
    return "<h2>" . $header . "</h2>";
}

function getProblemsPanel($id) {
    labelCode("component.php", "getProblemsPanel");
    $rowsPerPage = 10;
    $block = "<div class=\"maket\">
        
            <div class=\"table-component\">
                <div class=\"buttons-container table-controls\">
                    
                </div>";

    $block .= getPagination($rowsPerPage, getProblemsCount($id));

    $block .= "<div class=\"table-content\">" . getProblemsTablePage(1, $id) . "</div>";    
    
    $block .= 
            "</div>
        </div>"; 
    return $block;    
}

//Общее количество записей в таблице проблем
function getProblemsCount($id) {
    $appendTOsql = " and m.id_machines=" . $id;
    if ($id == "default") {
        $appendTOsql = "";
    } 
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8     
    $queryForCount = "SELECT 
                    count(*) as rows_count
                FROM problems p, machines m, users u 
                WHERE m.id_machines=p.id_machine 
                AND m.respons_machines=u.id_user" . $appendTOsql;    

    $countResult = mysqli_query($link, $queryForCount); 
    $result = 0;
    if ($rowsCount = mysqli_fetch_array($countResult)) {
        $result = $rowsCount["rows_count"];
    }

    mysqli_close($link);               
    return $result;    
}

function getProblemsTablePage($page, $id, $currentPage = 1) {
    $rowsPerPage = 15;
    $fromIndex = ($page - 1) * $rowsPerPage;
    $appendTOsql = " and m.id_machines=" . $id;
    if ($id == "default") {
        $appendTOsql = "";
    }
    
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8 
    $query = " SELECT 
                    p.id_problems, p.name_problems, p.date_problems, p.notes_problems, p.status_problems, p.id_machine, 
                    m.id_machines, m.name_machines, m.respons_machines,
                    u.id_user, u.name_user
                FROM problems p, machines m, users u 
                WHERE m.id_machines=p.id_machine 
                AND m.respons_machines=u.id_user" . $appendTOsql . " ORDER BY p.id_problems DESC LIMIT $fromIndex, $rowsPerPage";   

    $result = mysqli_query($link, $query);

    $block = "<table class=\"problem\">
                    <tr class=\"problem-table-head\"><th class=\"fst-col\" title=\"Выделить всё\"><input type=\"checkbox\" id=\"check-all\"></th>
                        <th>Оборудование</th>
                        <th colspan=\"2\">Проблема</th>
                        <th>Дата</th>
                        <th>Примечания</th>
                        <th>Состояние</th>
                        <th>Ответственный</th>
                    </tr>";
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        
        $i++;               //Счетчик для нумерации строк в таблице
        $statusProblems = $row['status_problems'];
        if ($statusProblems == "1"){
            $statusString = "Не решена";
            $statusClass = "status-problem-create";
        } elseif ($statusProblems == "2"){
            $statusString = "В работе";
            $statusClass = "status-problem-doing";
        } elseif ($statusProblems == "4") {
            $statusString = "Выполнена";
            $statusClass = "status-problem-done";
        }

        $status = "<div class='btn-link " . $statusClass . "'><a title=\"Статус записи\" href=\"javascript: void(0);\">$statusString</a></div>";
        $block .= 
        "<tr value=" . $row['id_problems'] . ">
            <td><input type=\"checkbox\" value=" . $row['id_problems'] . "></td>
            <td class = \"td-name-machines col-left-align\">" . $row['name_machines'] . "</td>
            <td class = \"td-name-problems col-left-align tooltip\" title=" . $row['name_problems'] . ">" 
            . $row['name_problems'] . "</td>
            <td class=\"td-icons\">
                <a href=\"#\" class=\"icon-button\" value=\"1\"><img src=\"img/icon-photo.png\"></img></a></td>
            <td>" . date("d-m-Y", strtotime($row['date_problems'])) . "</td>
            <td class = \"td-notes-problems col-left-align tooltip\" title=" . $row['notes_problems']. ">" . $row['notes_problems'] . "</td>
            <td class = \"status-problem\">" . $status . "</td>
            <td class = \"td-name-user col-left-align\">" . $row['name_user'] . "</td>
        </tr>"; 
    }
    mysqli_close($link);
    $block .= "</table>";
    return $block;
}

function getPagination($pageSize, $rowsCount){
    
    $resultOut = "  <div class=\"pagination\">
                        <div class=\"pager-button\">    
                            <a href=\"javascript: void(0);\" value=\"first\">
                                <<
                            </a>
                        </div>    
                        <div class=\"pager-button\">    
                            <a href=\"javascript: void(0);\" value=\"prev\">
                                <
                            </a>
                        </div>
                    ";
    $pageCount = ceil($rowsCount / $pageSize); 

    for($i = 1; $i <= $pageCount; $i++) {
        $resultOut .=
        "<div id=\"page" . $i . "\" class=\"pager-button" . ($i == 1 ? " active" : "") . "\">    
            <a href=\"javascript: void(0);\" value=\"" . $i . "\">" . $i . "</a>
        </div>";
    }
    $resultOut .=
                        "<div id=\"next\" class=\"pager-button\">    
                            <a href=\"javascript: void(0);\" value=\"next\">
                                >
                            </a>
                        </div>    
                        <div id=\"last\" class=\"pager-button\">    
                            <a href=\"javascript: void(0);\" value=\"last\">
                                >>
                            </a>
                        </div>
                    </div>";  
    return $resultOut;
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
            <div id=\"inputs\">
                <div class=\"select-list\">
                    <p>Ответственный за оборудование:</p>
                    <select id=\"respons\">
                    <option value=\"\"  selected>Без выбора ответственного</option>";       //Список ответственных за оборудование

    $link = mysqli_connect("localhost", "root", "mysql", "desk");
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
                <p>Оборудование:</p>
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
        $query = "SELECT name_machines, id_machines FROM machines WHERE respons_machines = $userId";
    }   else{
        $query = "SELECT name_machines, id_machines FROM machines"; //ЗАПРОС
    }
        
        $link = mysqli_connect("localhost", "root", "mysql", "desk");
        mysqli_set_charset($link, "utf8");                          //кодировка в utf8 
        
        logger($query);

        $result = mysqli_query($link, $query);    
        
        while ($row = mysqli_fetch_array($result)) {
            $items .= "<option value=" . $row['id_machines'] . ">" . $row['name_machines'] . "</option>";        
        }

        mysqli_close($link);
       

    return str_replace("[#items#]", $items, $select); 
}

function wrapElements($class, $targetContent){
    labelCode("component.php", "wrapElements");
    return "<div class = \"$class\">$targetContent</div>"; //Оборачивает содержимое в div
}

function getStatusList(){
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
    mysqli_set_charset($link, "utf8"); 
    $query = "SELECT * FROM `status` WHERE enable_status=true";
    $result = mysqli_query($link, $query);
    $resultOut = "";
    while ($row = mysqli_fetch_array($result)) {
        $resultOut .= "<p><input type=\"radio\" name=\"status\" id='radio" . $row['id_status'] . "' value=" . $row['id_status'] . ">
        <label for='radio" . $row['id_status'] ."' >" . $row['name_status'] . "</label></p>" ;
    }
    mysqli_close($link);  
    return $resultOut;
}

function getBtnProblem($selValue, $curRow){
    $statusProblems = $selValue;
        if ($statusProblems == "1"){
            $statusString = "Не решена";
            $statusClass = "status-problem-create";
        } elseif ($statusProblems == "2"){
            $statusString = "В работе";
            $statusClass = "status-problem-doing";
        } elseif ($statusProblems == "4") {
            $statusString = "Выполнена";
            $statusClass = "status-problem-done";
        }
        //"UPDATE `problems` SET `status_problems` = '1' WHERE `problems`.`id_problems` = 2;"
    $link = mysqli_connect("localhost", "root", "mysql", "desk");
    mysqli_set_charset($link, "utf8"); 
    $query = "UPDATE `problems` SET `status_problems` = $statusProblems WHERE `problems`.`id_problems` = $curRow";
    $result = mysqli_query($link, $query);    

    $resultOut = "<div class='btn-link " . $statusClass . "'><a title=\"Статус записи\" href=\"javascript: void(0);\">$statusString</a></div>";

    return $resultOut;        
    }

    function controlButtons(){
        $block = 
        "<div class=\"control-buttons-problems\">
            <div id=\"delete-problem-link\" class=\"delete-button link \">    
                        <a href=\"javascript: void(0);\">УДАЛИТЬ</a>
                    </div>
            <div id=\"get-problem-link\" class=\"get-problem-panel add-button link \">    
                        <a href=\"javascript: void(0);\">ДОБАВИТЬ</a>
                    </div>
            <div class=\"link\">
                <a id=\"problems-plan\" href=\"javascript: void(0);\">ПЛАН НА РЕМОНТ</a>
            </div>
            <div class=\"link\" hidden=\"true\">
                <a class=\"print-link\" title=\"Таблица на печать\" href=\"javascript: void(0);\">ПЕЧАТЬ</a>       
            </div>
        </div>";
        //
        return $block;
    }

//Список нерешенных проблем
    function getProblemPlanTable(){     
        $link = mysqli_connect("localhost", "root", "mysql", "desk");
        mysqli_set_charset($link, "utf8"); 
        $query = "SELECT 
                    p.id_problems, p.name_problems, p.date_problems, p.notes_problems, p.status_problems, p.id_machine, 
                    m.id_machines, m.name_machines, m.respons_machines,
                    u.id_user, u.name_user
                FROM problems p, machines m, users u 
                WHERE m.id_machines=p.id_machine 
                AND m.respons_machines=u.id_user
                AND status_problems NOT LIKE 4";
         
        $result = mysqli_query($link, $query);

    $block = "<p class=\"problems-plan-header\"><a class ='nav onepage-link' href='#problems=default'>ЖУРНАЛ / </a> 
            <span class=\"plan-header\">СПИСОК АКТУАЛЬНЫХ ПРОБЛЕМ НА ОБОРУДОВАНИИ</span></p>
                <table class=\"problem-plan\">
                    <tr><th class=\"fst-col\" title=\"Выделить всё\"><input type=\"checkbox\" id=\"check-all\"></th>
                        <th>№</th>
                        <th>Оборудование</th>
                        <th>Проблема</th>
                        <th>Дата</th>
                        <th>Примечания</th>
                        <th>Состояние</th>
                        <th>Ответственный</th>
                    </tr>";
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        
        $i++;               //Счетчик для нумерации строк в таблице
        $statusProblems = $row['status_problems'];
        if ($statusProblems == "1"){
            $statusString = "Не решена";
            $statusClass = "status-problem-create";
        } elseif ($statusProblems == "2"){
            $statusString = "В работе";
            $statusClass = "status-problem-doing";
        } elseif ($statusProblems == "4") {
            $statusString = "Выполнена";
            $statusClass = "status-problem-done";
        }

        $status = "<div class='btn-link " . $statusClass . "'><a title=\"Статус записи\" href=\"javascript: void(0);\">$statusString</a></div>";
        $block .= 
        "<tr value=" . $row['id_problems'] . ">
            <td class=\"fst-col\"><input type=\"checkbox\" value=" . $row['id_problems'] . "></td>
            <td class = \"num\">" . $i . "</td>
            <td class = \"td-name-machines col-left-align\">" . $row['name_machines'] . "</td>
            <td class = \"td-name-problems col-left-align tooltip\" title=" . $row['name_problems'] . ">" . $row['name_problems']."</td>
            <td>" . date("d-m-Y", strtotime($row['date_problems'])) . "</td>
            <td class = \"td-notes-problems col-left-align tooltip\" title=" . $row['notes_problems']. ">" . $row['notes_problems'] . "</td>
            <td class = \"status-problem\">" . $status . "</td>
            <td class = \"td-name-user col-left-align\">" . $row['name_user'] . "</td>
        </tr>"; 
    }
    mysqli_close($link);
    $block .= "</table>";
    return $block;

    }

    //модальное окно - добавление записи в таблицу
?>