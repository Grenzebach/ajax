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
            console.log("кря!");
            //$("#row5").hide();
            
        } else {
            $(this).parent().closest("tr").removeAttr("checked");
        }
    });    
});

function getCheckedInputs() {
    var ids = [];
    $("input:checked").each(function() {
        ids.push($(this).parent().closest("tr").attr("machine"));
    });
    
    return ids;
}

function getCurrentPage(href) {
    var hrefElements = href.split("=");
    var type = hrefElements[0];
    var id = hrefElements[1];
    if (type == undefined) {
        type = "default";
    } else {
        type = type.replace("#", "");
    }

    if (id == undefined) {
        id = "default";
    }

    return {"name": type, "id": id};
}

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