$(document).ready(function () {

	console.log("message from main.js");


	$("li").on("click", function () {

        var sel_id = $(this).attr("id");
        $.ajax({
            url: "php/getdata.php",
            method: "POST",
            data: {"type": "get_select", "id_machine": sel_id}
        })
            .done(function (data) {
                $("#my-select").html(data);
            });


    });
})