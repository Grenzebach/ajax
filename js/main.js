$(document).ready(function () {

	getContent(getCurrentPageId());
    getCombos(getCurrentPageId());


	$("li").on("click", function () {
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
            data: {"type": "save", "ids": getCheckedInputs(), "page": getCurrentPageId()},
            success: function(response) {
                
                $("#content-data").html(response);
                setTimeout(function() {
                    alert("Даты обновлены");
                }, 100);
                
            } 
        });        
    })

    $("#add-link").on("click", function() {
       
        var sel = $("#id_select option:selected").val();
        var date_control = $("#input_date_control_units").val();
        var input_notes = $("#input_notes").val();
        $.ajax({
            url: "php/server.php",
            method: "POST",
            data: {"type": "add", "sel": sel, "date_control": date_control, "input_notes": input_notes, "page": getCurrentPageId() },
            success: function(response) {
                $("#content-data").html(response);
                setTimeout(function() {
                    alert("Проверка добавлена");
                }, 100); 
            }
        });        
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
            $(this).parent().closest("td").html($(this).val()); 
            //отправка запроса на изменение элемента.
        }             
    });

    $(document).on("change", "input[type='checkbox']", function(event) {
        if ($(this).prop("checked")) {
            $(this).parent().closest("tr").attr("checked", "true");
            console.log("qwe");
            
        } else {
            $(this).parent().closest("tr").removeAttr("checked");
        }
    });    
});


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



