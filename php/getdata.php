<?php

function getContent($id = "")
{
    $resultOut = "";
    if ($id == "") {
        $result = "<p>getContent = empty</p>";
    } else {
        $link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8"); //кодировка в utf8 

            //разница в датах
        
        $query_diff = "SELECT machines.id_machines, machines.name_machines, units.id_units, units.name_units, units.info_units, units.pozname_units, units.id_categories, c1.id_units, c1.date_control, c1.notes_control, categories.id_categories, categories.periodicy 
            FROM units, control c1, machines, categories 
            WHERE units.id_units=c1.id_units 
            AND units.id_machines=machines.id_machines 
            AND units.id_categories=categories.id_categories 
            AND c1.date_control=(SELECT MAX(c2.date_control) FROM control c2 where c2.id_units = c1.id_units) 
            AND units.id_machines=$id 
            GROUP BY units.name_units
            ORDER BY `units`.`id_units` ASC";

        $result_diff = mysqli_query($link, $query_diff);

//-----------Заголовок таблицы---------------
        
//-------------------------------------------
        //$resultOut = "<table><tr><th><img src='img/tick-button.png' alt='Отметка о выполнении'></th><th>Узел</th><th>Текущая дата</th><th>Предыдущая проверка</th><th>Период-ть</th><th>Дней осталось</th></tr>";
        $resultOut = "<table><tr><th><img src='img/tick-button.png' alt='Отметка о выполнении'></th><th>Узел</th><th>Инфо</th><th>Предыдущая проверка</th><th>Период-ть</th><th>Дней осталось</th><th>Замечания</th></tr>";

        //echo date("d-m-Y");
        $current_day = date("d-m-Y");
        $i=0;               //Счетчик итераций
        //echo dateDifference("2018-10-31","2018-12-25");
        $machineName = "";       
        while ($row = mysqli_fetch_array($result_diff)) {
            $machineName = "<p id='content-header'>".$row['name_machines']."</p>";

            $date_current = date_create($current_day); //Теущая дата
            $date_control = date_create($row['date_control']); //Дата последней проверки
            $date_deadline = date_add($date_control, date_interval_create_from_date_string($row['periodicy'] . "days")); // Окончания периода
            $interval = date_diff($date_current, $date_deadline);   //Разница между датами

            $diff = $interval -> format("%a"); //Количество дней в строку

            //$id_color = "td_green"; //Цвет ячейки по-умолчанию
            
            $icon = "";
            $i++;
            $result = $date_deadline < $date_current;
            if ($result)
                $icon = "class = 'days icon warning-icon' title='Просрочено!'";                   //Маркировка просроченной даты

            if ($diff < 8)
            {
                //  $icon = "attention-icon";    //Осталось меньше трёх дней?
                $plan[$i] = $row['name_units'];
            }


            $date_control_rev = date("d-m-Y", strtotime($row['date_control'])); // изменение формата даты из Y-m-d в d-m-Y
            //$resultOut .=  "<tr id=\"" . $row["id_units"] .  "\"><td><input type='checkbox' name='a' value='10'></td><td id = col_1>".$row['name_units']."</td><td>".$current_day."</td><td>".$date_control_rev."</td><td>".$row['periodicy']."</td><td id=".$id_color.">".$diff."</td></tr>";
            $resultOut .=  "<tr id=\"" . $row["id_units"] .  "\"><td><input type='checkbox' name='a' value='10'></td><td id = col_1>".$row['name_units']." ".$row['pozname_units']."</td><td>".$row['info_units']."</td><td>".$date_control_rev."</td><td>".$row['periodicy']."</td><td ".$icon.">".$diff."</td><td class='col-notes' id=col_notes>".$row['notes_control']."</td></tr>";
            }
                    //$resultOut .= "</table>";
            mysqli_close($link); //ЗАКРЫТИЕ СОЕДИНЕНИЯ
        }
        print_r($plan);
    $resultOut .="</table>";
    $resultOut .= "";
        //$request = 
    return $machineName . $resultOut;
}

function getCombos($id) {
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
    return $resultOut;
}
//$_POST['ids']
function applyChanges($ids)
{
    //данные из пост массива ids сохраняются в базу
    //обновление даты проверки
    //UPDATE `control` SET `date_control` = '2019-01-27' WHERE `control`.`id_control` = 9;
    //INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '23', '2019-01-23', '5', 'ok');
    $link = mysqli_connect("localhost", "root", "", "desk");
    mysqli_set_charset($link, "utf8"); //кодировка в utf8    
    foreach ($ids as $value) {
         //$query = "UPDATE `control` SET `date_control` = '".date('Y-m-d')."' WHERE `control`.`id_units` = $value";
         $query = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '".$value."', '".date('Y-m-d')."', '5', 'ok')";
         //echo "Запись добавлена" ;
         //echo $query;
         //echo $id;
         mysqli_query($link, $query);
     }    
}

function addRecord($sel, $dateControl, $inputNotes) {
//query_col = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '"+ sel+"', '"+date_control+"', "+"'4'"+", '"+input_notes+"')";
        $link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8"); //кодировка в utf8 
        
        $query = "INSERT INTO `control` (`id_control`, `id_units`, `date_control`, `state_control`, `notes_control`) VALUES (NULL, '$sel', '$dateControl', "."'4'".", '$inputNotes')";

        mysqli_query($link, $query);

        mysqli_close($link);
}

function getPlan($id) {
    $link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8"); //кодировка в utf8 

        $query_diff = "SELECT machines.id_machines, machines.name_machines, units.id_units, units.name_units, units.info_units, units.pozname_units, units.id_categories, c1.id_units, c1.date_control, c1.notes_control, categories.id_categories, categories.periodicy 
            FROM units, control c1, machines, categories 
            WHERE units.id_units=c1.id_units 
            AND units.id_machines=machines.id_machines 
            AND units.id_categories=categories.id_categories 
            AND c1.date_control=(SELECT MAX(c2.date_control) FROM control c2 where c2.id_units = c1.id_units) 
            AND units.id_machines=$id 
            GROUP BY units.name_units
            ORDER BY `units`.`id_units` ASC";

        $result_diff = mysqli_query($link, $query_diff);
        echo "fsvsdfsdf";
        //print_r($plan);
}

?>