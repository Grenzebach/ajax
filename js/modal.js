$(document).ready(function(){
	$(document).on("click", ".modal-close", function(){
		closeModal();
	});

	$(document).on("click", ".modal-cancel", function(){
		closeModal();
	});

});

function showModal(){
	$(".modal-background").show();
	$(".modal-content").show();
}

function closeModal(){
	$(".modal-background").hide();
	$(".modal-content").hide();
}