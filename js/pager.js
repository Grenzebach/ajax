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

            setActiveLink(page);
        } 
    });    
}

function setActiveLink(page) {
	$(".onepage-link.active").removeClass("active");
	$("a[href='#" + page["name"] + "=" + page["id"] + "']").addClass("active");	
}