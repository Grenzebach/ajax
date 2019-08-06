

$(document).ready(function () {
	getPage(getCurrentPage(location.hash));
	
});

function getPage(page) {
	$('#loading').show();
    $.ajax({
        url: "php/pager.php",
        method: "GET",
        data: {"name": page["name"], "id": page["id"]},
        success: function(response) {
            $("#content").html(response);

            setActiveLink(page);
			$('#loading').hide();
            // var callback = getCallbacks()[page["name"]];
            // if (callback == undefined) {
            // 	getCallbacks()["default"].call(this, page);
            // } else {
            // 	callback.call(this, page);
            // }              
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

function getCallbacks() {
	return {
		"problems": function(page) {		
	        	
		},
		"default" : function(page) {

		}
	}
}