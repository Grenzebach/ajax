$(document).ready(function () {
	$(document).on("click", ".onepage-link", function () {
        getPage(getCurrentPage($(this).attr("href")));  
    });

    $(document).on("click", "#save-link", function() {
        if (getCheckedInputs().length == 0) {
            alert("Нужно выбрать");
            return;
        }
        var page = getCurrentPage(location.hash);
        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "save", "items": getCheckedInputs(), "pageName": page["name"], "pageId": page["id"]},
            success: function(response) {
                
                $("#content-data").html(response);
                setTimeout(function() {
                    alert("Даты обновлены");
                }, 100);
                
            } 
        });         
    });

    $(document).on("click", "#add-link", function() {
       
        var unitId = $("#id_select option:selected").val();
        var dateControl = $("#input_date_control_units").val();
        var inputNotes = $("#input_notes").val();        
        saveControl(unitId, dateControl, inputNotes)        
    });
    
    $(document).on("click", "#print-link", function() {
        window.print();                 //Печатать страницу
        console.log("print");        
    });

    $(document).on("click", "#add-problem-link", function() {
        console.log("add-problem-link pressed");
        var selIdMachine = $("#machine-list-problems").val();
        var nameProblem = $("#name-problems").val();
        var dateProblem = $("#date-problems").val();
        var noteProblem = $("#notes-problems").val();
        addProblem(selIdMachine, nameProblem, dateProblem, noteProblem);
        console.log("addProblem("+selIdMachine, nameProblem, dateProblem, noteProblem+")");


    });

    $(document).on("dblclick", "tr[checked] .col-notes", function() {
        var element = $("td[oldValue]");
        element
            .html(element.attr("oldValue"))
            .removeAttr("oldValue");

        $(this).attr("oldValue", $(this).html()); 
        $(this).html("<input class='col-notes-edit' type='text' value=' " + $(this).html() + " ' />");
    });

    $(document).on("keypress", ".col-notes-edit", function(event) {
        if(event.which == 13) {
            var inputNotes = $(this).val();
            var unitId = $(this).parent().closest("tr").attr("machine");
            $(this).parent().closest("td").html(inputNotes); 
            saveControl(unitId, new Date().toISOString(), inputNotes);
            //отправка запроса на изменение элемента.
        }             
    });

    $(document).on("change", "input[type='checkbox']", function(event) {
        if ($(this).prop("checked")) {
            $(this).parent().closest("tr").attr("checked", "true");            
            
        } else {
            $(this).parent().closest("tr").removeAttr("checked");
        }
    });    

    $(document).on("change", "#respons", function(){
        getSelectMachineList($(this).val());
    });

    $(document).on("keyup", "#sidebar-search", function() {
        var search = $(this).val();
        $("#menu li").hide();
        $("#menu li a:contains('" + search + "')").each(function() {
            $(this).parent().closest("li").show();
        });
    });

//  Нажатие на кнопку для изменения статуса проблемы. Кнопка преобразуется в селект
    $(document).on("click", ".btn-link", function(){
        //$(".status-problem").removeAttr("id","selected-btn");
        //$(".status-problem").removeAttr("value","selected");
        //$(this).closest("td").attr("id", "selected-btn");  //Добавление id div с нажатой кнопке
        $(this).closest("td").attr("value", "selected");
        console.log("click");
        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "btn-to-select"},
            success: function(response){
                //$("#selected-btn").html(response);
                $(".status-problem [value='selected']").html(response);
                console.log($(".status-problem [value='selected']").hide());
                            
            }
        });
    });
    $(document).on("change mouseleave", ".select-status-problem", function(){
        
        console.log($(this).parent().closest("tr").attr("value"));
        
        var selValue = $(this).val();
        var curRow = $(this).parent().closest("tr").attr("value");

        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "select-to-btn", "sel-value": selValue, "cur-row": curRow},
            success: function(response){
                $("#selected-btn").html(response);

            
            }
        });
    });

    $(document).on("dblclick", ".td-name-problems", function(){
        console.log("click td");
        $(this).closest("td").attr("value", "clicked");
        var content = $(this).text();
        console.log(content);
        $(this).empty();
        $(this).html("<textarea class='problem-textarea'>" + content + "</textarea>");

    });

});

function getCheckedInputs() {
    var ids = [];
    $("input:checked").each(function() {
        ids.push($(this).parent().closest("tr").attr("machine"));
    });
    
    return ids;
}

