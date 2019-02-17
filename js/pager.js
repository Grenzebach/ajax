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

	var pageName = page["name"];
	if (pageName == "default") {
		pageName = startingPage;
	}

	$("a[href='#" + pageName + "=" + page["id"] + "']").addClass("active");	
}