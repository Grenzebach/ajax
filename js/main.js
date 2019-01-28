$(document).ready(function () {

    var id = 1;
    if (window.location.hash != "") {
        id = window.location.hash.split("=")[1];       
        if (id == undefined) {
            id = 1;
        }
    }
	getContent(id);


	$("li").on("click", function () {
        var val = $(this).attr("value");
        getContent(val);
    });

    $("#save-link").on("click", function() {
        $.ajax({
            url: "php/server.php",
            method: "POST",
            data: {"type": "save", "ids": getCheckedInputs()},
            success: function(response) {
                console.log(response);
                console.log("save");
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
            data: {"type": "add", "sel": sel, "date_control": date_control, "input_notes": input_notes },
            //success: getContent(1),
             
        });        
    })
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

function getCheckedInputs() {
    var ids = [];
    $("input:checked").each(function() {
        ids.push($(this).parent().closest("tr").attr("id"));
    });
    
    return ids;
}

