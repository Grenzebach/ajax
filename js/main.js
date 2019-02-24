function saveControl(unitId, dateControl, inputNotes) {
    var page = getCurrentPage(location.hash);
    $.ajax({
        url: "php/controller.php",
        method: "POST",
        data: {"action": "add", "sel": unitId, "date_control": dateControl, "input_notes": inputNotes, "pageName": page["name"], "pageId": page["id"] },
        success: function(response) {
            $("#content-data").html(response);
            setTimeout(function() {
                alert("Проверка добавлена");
            }, 100); 
        }
    });     
}

function addProblem(selIdMachine, nameProblem, dateProblem, noteProblem){
    var page = getCurrentPage(location.hash);
    $.ajax({
        url: "php/controller.php",
        method: "POST",
        data: {"action": "add-problem", "selIdMachine":selIdMachine, "nameProblem": nameProblem, "dateProblem": dateProblem, "noteProblem": noteProblem, "pageId": page["id"]},
        success: function(response) {
            $(".maket").html(response);        
        }
    });
}

function getGrid(){
    $("#jqGrid").jqGrid({
        url: "php/controller.php",
        datatype: "local",
        
        colNames: ["№", "Оборудование", "Проблема", "Дата", "Замечания", "Состояние", "Ответственный"],
        colModel: [
            {name: "num", width: 35},
            {name: "machine", width: 200},
            {name: "problem", width: 320},
            {name: "date", width: 90},
            {name: "notes", width: 340},
            {name: "status", width: 90},
            {name: "respons", width: 130},
        ],
        pager: $("#pager"),
        rowNum: 10,
        rowList: [10,20,30],
        sortname: "num",
        sortorder: "asc",
        viewrecords: true,
        gridView: true,
        autoencode: true,
        caption: "Журнал еженедельного осмотра оборудования"
    })
}