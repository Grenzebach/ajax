$(document).ready(function(){
	$(document).on("click", ".modal-close", function(){
		closeModal();
	});

	$(document).on("click", ".modal-cancel", function(){
		closeModal();
	});

});

function showModal(title, content, okAction) {
	//Добавить тайтл
	$(".modal-title").html(title);
	$(".modal-background").show();
	$(".modal-content").show();
	$(".modal-form").html(content);

	$(".modal-ok").on("click", function() {
		okAction.call(this, content);
	});
}

function closeModal(closeAction){
	$(".modal-background").hide();
	$(".modal-content").hide();

	$(".modal-ok").off();

	if (closeAction != undefined) {
		closeAction.call(this);
	}
}