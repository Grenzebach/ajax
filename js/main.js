$(document).ready(function () {
    routing();	
    getCombos(getCurrentPageId());


	$(document).on("click", "li, .nav", function () {
        var val = $(this).attr("value");
        getContent(val);
        getCombos(val);    
    });

    $("#save-link").on("click", function() {
        if (getCheckedInputs().length == 0) {
            alert("Нужно выбрать");
            return;
        }
        $.ajax({
            url: "php/server.php",
            method: "POST",
            data: {"type": "save", "ids": getCheckedInputs(), "page": getCurrentPageId(), "pageType": getCurrentPageType()},
            success: function(response) {
                
                $("#content-data").html(response);
                setTimeout(function() {
                    alert("Даты обновлены");
                }, 100);
                
            } 
        });         
    })

    $("#add-link").on("click", function() {
       
        var unitId = $("#id_select option:selected").val();
        var dateControl = $("#input_date_control_units").val();
        var inputNotes = $("#input_notes").val();        
        saveControl(unitId, dateControl, inputNotes)        
    });

    $("#mkplan-link").on("click", function() {
        getPlan(getCurrentPageId());        
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
            console.log("qwe");
            //$("#row5").hide();
            
        } else {
            $(this).parent().closest("tr").removeAttr("checked");
        }
    });    
});

function routing() {
    var type = getCurrentPageType();

    if (type == "machine") {
        getContent(getCurrentPageId());        
    } else if (type == "plan") {
        getPlan(getCurrentPageId())
    }
    console.log(type);
}


function getContent(id) {
    $.ajax({
        url: "php/server.php?type=page&id=" + id,
        method: "GET",
        data: "",
        success: function(response) {
            $("#content-data").html(response)
        } 
    });
}

function getPlan(id) {
    $.ajax({
        url: "php/server.php",
        method: "GET",
        data: {"type": "plan", "page": id},
        success: function(response) {
            $("#content-data").html(response);
            setTimeout(function() {
                window.location.hash = "#plan=1";                
                console.log("plan is OK");
            }, 100); 
        }
    });     
}

function getCombos(id) {
    $.ajax({
        url: "php/server.php?type=combos&id=" + id,
        method: "GET",
        data: "",
        success: function(response) {
            $("#combos").html(response)
        } 
    });
}

function getCheckedInputs() {
    var ids = [];
    $("input:checked").each(function() {
        ids.push($(this).parent().closest("tr").attr("id"));
    });
    
    return ids;
}

function getCurrentPageId() {
    var id = 1;
    if (window.location.hash != "") {
        id = window.location.hash.split("=")[1];       
        if (id == undefined) {
            id = 1;
        }
    }
    return id;
}

function getCurrentPageType() {
    var type = "machine";
    if (window.location.hash != "") {
        type = window.location.hash.split("=")[0];       
        if (type == undefined) {
            type = "machine";
        }
        type = type.replace("#", "");
    }
    return type;
}

function saveControl(unitId, dateControl, inputNotes) {
    $.ajax({
        url: "php/server.php",
        method: "POST",
        data: {"type": "add", "sel": unitId, "date_control": dateControl, "input_notes": inputNotes, "page": getCurrentPageId(), "pageType": getCurrentPageType() },
        success: function(response) {
            $("#content-data").html(response);
            setTimeout(function() {
                alert("Проверка добавлена");
            }, 100); 
        }
    });     
}