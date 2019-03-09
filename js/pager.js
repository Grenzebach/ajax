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

            var callback = getCallbacks()[page["name"]];
            if (callback == undefined) {
            	getCallbacks()["default"].call(this, page);
            } else {
            	callback.call(this, page);
            }          
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
	        $('#data-table').DataTable({	        	
	         	"columnDefs": [
    		 		{ "width": "40%", "targets": 2},
    		 		{ "width": "8%", "targets": 3 },    		 		
    		 		{ "width": "30%", "targets": 4 }    		 		
  				 ],				
		        "language": {
        			url: 'localisation/ru_RU.json'},
		        "processing": true,
		        "serverSide": true,
		        "ajax": "php/datagrid.php",

		        "bLengthChange": true

		    });		
		},
		"default" : function(page) {
			
		}
	}
}