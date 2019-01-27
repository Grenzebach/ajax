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
        
        $query_diff = "SELECT machines.id_machines, machines.name_machines, units.id_units, units.name_units, units.info_units, units.pozname_units, units.id_categories, control.id_units, control.date_control, control.notes_control, categories.id_categories, categories.periodicy FROM units, control, machines, categories where units.id_units=control.id_units and units.id_machines=machines.id_machines and units.id_categories=categories.id_categories and units.id_machines=".$id;
        $result_diff = mysqli_query($link, $query_diff);

//-----------Заголовок таблицы---------------
        
//-------------------------------------------
        //$resultOut = "<table><tr><th><img src='img/tick-button.png' alt='Отметка о выполнении'></th><th>Узел</th><th>Текущая дата</th><th>Предыдущая проверка</th><th>Период-ть</th><th>Дней осталось</th></tr>";
        $resultOut = "<table><tr><th><img src='img/tick-button.png' alt='Отметка о выполнении'></th><th>Узел</th><th>Инфо</th><th>Предыдущая проверка</th><th>Период-ть</th><th>Дней осталось</th><th>Замечания</th></tr>";

        //echo date("d-m-Y");
        $current_day = date("d-m-Y");

        //echo dateDifference("2018-10-31","2018-12-25");
        $flag = true;                                       //Для выполнения условия исполнения один раз в цикле        
        while ($row = mysqli_fetch_array($result_diff)) {
                   if ($flag == true)
                        {
                            echo "<p id='content-header'>".$row['name_machines']."</p>";    //Вывести заголовок только один раз
                            $flag = false;
                        }
                    $date_current = date_create($current_day); //Теущая дата
                    $date_control = date_create($row['date_control']); //Дата последней проверки
                    $date_deadline = date_add($date_control,date_interval_create_from_date_string($row['periodicy']."days")); // Окончания периода
                    $interval = date_diff($date_current, $date_deadline);   //Разница между датами
                    
                    $diff = $interval -> format("%a"); //Количество дней в строку
                    
                    $id_color = "td_green"; //Цвет ячейки по-умолчанию
                    
                    $result = $date_deadline < $date_current;
                    if($result)
                        $id_color = "td_red";   //Маркировка просроченной даты
                    
                    if ($diff < 3)
                        $id_color = "td_yellow";    //Осталось меньше трёх дней?
                   
                   
                   $date_control_rev = date("d-m-Y",strtotime($row['date_control'])); // изменение формата даты из Y-m-d в d-m-Y
                    //$resultOut .=  "<tr id=\"" . $row["id_units"] .  "\"><td><input type='checkbox' name='a' value='10'></td><td id = col_1>".$row['name_units']."</td><td>".$current_day."</td><td>".$date_control_rev."</td><td>".$row['periodicy']."</td><td id=".$id_color.">".$diff."</td></tr>";
                    $resultOut .=  "<tr id=\"" . $row["id_units"] .  "\"><td><input type='checkbox' name='a' value='10'></td><td id = col_1>".$row['name_units']."</td><td>".$row['info_units']."</td><td>".$date_control_rev."</td><td>".$row['periodicy']."</td><td class = 'days' id=".$id_color.">".$diff."</td><td id=col_notes>".$row['notes_control']."</td></tr>";
                }
                    //$resultOut .= "</table>";
                mysqli_close($link); //ЗАКРЫТИЕ СОЕДИНЕНИЯ
        }
        
            //Формирование в последней строчке выпадающего списка узлов
        $link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8"); //кодировка в utf8 
        $query = "SELECT name_units FROM `units` WHERE id_machines =".$id; //Список узлов в селект
        $result = mysqli_query($link, $query); 

        $resultOut .="<tr><td></td><td>";
        $resultOut .="<select>";
        $i=0;
        while ($row = mysqli_fetch_array($result)) {
            $resultOut.="<option value='id_select_'".$i++.">".$row['name_units']."</option>";
    }
        mysqli_close($link);

        $resultOut .= "</select></td><td>";        
        $resultOut .="<input type='text' id='input_info_units'>";                                   //Доп. информация по узлу
        $resultOut .="</td><td>";
        $resultOut .="<input type='date' value=".date('Y-m-d')." id='input_date_control_units'>";   //Выбор даты
        $resultOut .="</td><td>";
        
        //Список периодичности в селект
        $link = mysqli_connect("localhost", "root", "", "desk");
        mysqli_set_charset($link, "utf8"); //кодировка в utf8 
        $query = "SELECT periodicy FROM categories";
        $result = mysqli_query($link, $query);
        
        $resultOut .="<select>";
        while ($row = mysqli_fetch_array($result)) {
            $resultOut .="<option>".$row['periodicy']."</option>";
        }

        mysqli_close($link);
        $resultOut .="</select>";     //Периодичность проверки
        $resultOut .="</td><td></td><td>";
        $resultOut .='<textarea name="input_notes_table" id="input_notes" cols="30" rows="1"></textarea>';
        $resultOut .="</td></tr></table>";
        $resultOut .= "";
    
    return $resultOut;
}
function add_record()
{

}
//echo getContent($_POST['value']);
//https://dayte2.com/editable-table

?>