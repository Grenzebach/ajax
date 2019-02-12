$(document).ready(function () {
	getPage(getCurrentPage(location.hash));	
});

function getPage(page) {
    $.ajax({
        url: "php/pager.php",
        method: "GET",
        data: {"name": page["name"], "id": page["id"]},
        success: function(response) {
            $("#content").html(response);
        } 
    });    
}