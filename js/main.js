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