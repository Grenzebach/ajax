$(document).ready(function () {
    $(document).on("click", ".onepage-link", function () {
        getPage(getCurrentPage($(this).attr("href")));
    });

//Обновить дату проведения ТО станка
    $(document).on("click", "#save-link", function() {
        if (getCheckedInputs().length == 0) {
            alert("Нужно выбрать запись");
            return;
        }
        var page = getCurrentPage(location.hash);
        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "save", "items": getCheckedInputs(), "pageName": page["name"], "pageId": page["id"]},
            success: function(response) {
                $("#content-data").html(response);
                setTimeout(function() {
                    alert("Даты обновлены");
                }, 100);
            }
        });
    });

//Добавить запись о проверке в таблицу ТО
    $(document).on("click", "#add-link", function() {

        var unitId = $("#id_select option:selected").val();
        var dateControl = $("#input_date_control_units").val();
        var inputNotes = $("#input_notes").val();
        saveControl(unitId, dateControl, inputNotes)
    });

//Печатать страницу
    $(document).on("click", ".print-link", function() {
        window.print();
        console.log("print");
    });

//Добавить запись в таблицу проблем
    $(document).on("click", "#add-problem-link", function() {
        var selIdMachine = $("#machine-list-problems").val();
        var nameProblem = $("#name-problems").val();
        var dateProblem = $("#date-problems").val();
        var noteProblem = $("#notes-problems").val();
        addProblem(selIdMachine, nameProblem, dateProblem, noteProblem);
        console.log(selIdMachine, nameProblem, dateProblem, noteProblem);

    });

//Удалить запись из таблицы проблем
    $(document).on("click", "#delete-problem-link", function(){
        console.log("delete-link pressed");
        if (getCheckedInputs().length == 0) {
            alert("Нужно выбрать запись");
            return;
        }

        var page = getCurrentPage(location.hash);
        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "delete", "items": getCheckedInputs(), "pageName": page["name"], "pageId": page["id"]},
            success: function(response) {

                $(".maket").html(response);
                setTimeout(function() {
                    alert("Записи удалены");
                }, 100);
            }
        });
    });

//Получения списка нерешенных проблем
    $(document).on("click", "#problems-plan", function(){
        //$('#loading').show();
        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "problems-plan"},
            success: function(response) {
                $(".maket").html(response);
                //$('#loading').hide();
                //window.print();
                $(".link").show();      //Показать кнопку ПЕЧАТЬ при редактировании плана на ремонт

                $("#problems-plan").hide();  //Убрать кнопку сформировать план на странице с планом
                console.log("problems plan");
            }
        });
    });

//Редактирование замечаний к проверке у выделенной строки
    $(document).on("dblclick", "tr[checked] .col-notes", function() {
        var element = $("td[oldValue]");
        element
            .html(element.attr("oldValue"))
            .removeAttr("oldValue");

        $(this).attr("oldValue", $(this).html());
        $(this).html("<input class='col-notes-edit' type='text' value=' " + $(this).html() + " ' />");
    });

//Сохр. изменений в строке таблицы ТО на нажатие ENTER
    $(document).on("keypress", ".col-notes-edit", function(event) {
        if(event.which == 13) {
            var inputNotes = $(this).val();
            var unitId = $(this).parent().closest("tr").attr("machine");
            $(this).parent().closest("td").html(inputNotes);
            saveControl(unitId, new Date().toISOString(), inputNotes);
            //отправка запроса на изменение элемента.
        }
    });

    $(document).on("change", "input[type='checkbox']", function(event) {
        if ($(this).prop("checked")) {
            $(this).parent().closest("tr").attr("checked", "true");

        } else {
            $(this).parent().closest("tr").removeAttr("checked");
        }
    });

//Список станков для конкретного ответственного
    $(document).on("change", "#respons", function(){
        getSelectMachineList($(this).val());
    });

//Поиск в списке станков для сайдбара
    $(document).on("keyup", "#sidebar-search", function() {
        var search = $(this).val();
        $("#menu li").hide();
        $("#menu li a:contains('" + search + "')").each(function() {
            $(this).parent().closest("li").show();
        });
    });

//  Нажатие на кнопку для изменения статуса проблемы. Кнопка преобразуется в селект
    $(document).on("click", ".btn-link", function(){
        var curRow = $(this).parent().closest("tr").attr("value");
        // showModal("Тестовое окно", "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat", function() {
        //     closeModal(function() {
        //         alert("Окно закрыто по ОК");
        //     });
        // })

        $.ajax({
            url: "php/component-controller.php",
            method: "GET",
            data: {"name": "statusList"},
            success: function(response) {
                showModal("Изменение статуса проблемы", response, function(content) {
                    var statusId = $("[name='status']:checked").val();
                    //Валидация
                    console.log($("[name='status']:checked").val());
                    $.ajax({
                        url: "php/controller.php",
                        method: "POST",
                        data: {"action": "select-to-btn", "sel-value": statusId, "cur-row": curRow},
                        success: function(response) {
                            $("tr[value='" + curRow + "'] .status-problem").html(response);

                            closeModal(function () {
                                $("tr[value='" + curRow + "']").addClass("row-changed");
                                setTimeout(function() {
                                    $("tr[value='" + curRow + "']").removeClass("row-changed");
                                }, 1000);                                                        // Подсветка измененной строки
                            });
                        }
                    });
                });
            }
        });
    });

//Селект преобразуется обратно в кнопку
    $(document).on("change mouseleave", ".select-status-problem", function(){
        console.log($(this).parent().closest("tr").attr("value"));
        var selValue = $(this).val();
        var curRow = $(this).parent().closest("tr").attr("value");

        $.ajax({
            url: "php/controller.php",
            method: "POST",
            data: {"action": "select-to-btn", "sel-value": selValue, "cur-row": curRow},
            success: function(response){

                $(".status-problem[value='selected']").html(response)
                    .removeAttr("value");
            }
        });
    });

//Управление кнопками пейджера
    $(document).on("click", ".table-component .pager-button a", function() {
        var totalPages = parseInt($(this).parent().closest(".pagination").attr("pageCount"));

        var parent = $(this).parent().closest(".pager-button");
        if (parent.hasClass("active")) {
            return;
        }
        var nextPage = $(this).attr("value");
        var currentPage = parseInt($(this).parent().closest(".table-component").find(".pager-button.active a").attr("value"));
        if (nextPage == "first" || nextPage == "prev") {
            if (currentPage <= 1) {
                return
            }
        }

        var paginationRangeCount = 5;
        if (nextPage == "first") {
            nextPage = 1;

            if (totalPages > paginationRangeCount) {
                var newPagination = buildByRange(1, paginationRangeCount, 1, totalPages);
                $(this).parent().closest(".pagination").find(".pagination-range").html(newPagination);
            }
        } else if (nextPage == "prev") {
            nextPage = currentPage - 1;

            if (totalPages > paginationRangeCount && nextPage > 2 && nextPage < totalPages - 2) {
                var newPagination = buildByRange(nextPage - 2, nextPage + 2, nextPage, totalPages);
                $(this).parent().closest(".pagination").find(".pagination-range").html(newPagination);
            }
        }

        if (nextPage == "next" || nextPage == "last") {
            if (currentPage >= totalPages) {
                return;
            }
        }
        var parentComponent = $(this).parent().closest(".table-component");
        if (nextPage == "next") {
            nextPage = currentPage + 1;
            if (totalPages > paginationRangeCount && nextPage > 3 && nextPage < totalPages - 1) {
                var newPagination = buildByRange(nextPage - 2, nextPage + 2, nextPage, totalPages);
                $(this).parent().closest(".pagination").find(".pagination-range").html(newPagination);
            }
        } else if (nextPage == "last") {
            nextPage = totalPages;

            if (totalPages > paginationRangeCount) {
                var newPagination = buildByRange(nextPage - 4, nextPage, nextPage, totalPages);
                $(this).parent().closest(".pagination").find(".pagination-range").html(newPagination);
            }
        } else if (nextPage != "prev" && nextPage != "first") {
            nextPage = parseInt(nextPage);
            if (totalPages > paginationRangeCount && nextPage > 1 && nextPage < totalPages) {
                var newPagination = buildByRange(nextPage - 2, nextPage + 2, nextPage, totalPages);
                $(this).parent().closest(".pagination").find(".pagination-range").html(newPagination);
            }
        }
        getProblemsTablePage(parentComponent, nextPage)
    });
});

//Ввод данных в модальном окне
$(document).on("click", ".get-problem-panel", function(){
    $.ajax({
        url: "php/component-controller.php",
        method: "GET",
        data: {"name": "get-problem-panel"},
        success: function(response){
            showModal("Добавление записи о проблеме", response, function(content){

                var selIdMachine = $("#machine-list-problems").val();
                var nameProblem = $("#name-problems").val();
                var dateProblem = $("#date-problems").val();
                var noteProblem = $("#notes-problems").val();
                addProblem(selIdMachine, nameProblem, dateProblem, noteProblem);
                closeModal(function () {
                    $(".problem-table-head + tr").addClass("row-changed");
                    setTimeout(function() {
                        //$(".problem-table-head + tr").removeClass("row-changed");
                    }, 1000);                                                        // Подсветка измененной строки
                });
            });
        }
    });
});

//Построение пагинации для текущей страницы и пары соседних
function buildByRange(from, to, active, totalPages) {
    if (from < 1) {
        from = 1;
        to++;
    }
    if (to > totalPages) {
        to = totalPages;
        from--;
    }
    var pagination = "";
    for(var i = from; i <= to; i++) {
        pagination +=
            "<div id=\"page" + i + "\" class=\"pager-button" + (i == active ? " active" : "") + "\">" +
            "<a href=\"javascript: void(0);\" value=\"" + i + "\">" + i + "</a>" +
            "</div>";
    }
    return pagination;
}

//Формирование таблицы проблем в соответствии с пейджером
function getProblemsTablePage(parentComponent, page, currentPage) {
    var currentPage = parentComponent.find(".pager-button.active a").attr("value");
    parentComponent.find(".pager-button.active").removeClass("active");
    var sitePage = getCurrentPage(location.hash);
    $.ajax({
        url: "php/component-controller.php",
        method: "GET",
        data: {"name": "tablePage", "type": "problems", "page": page, "id": sitePage["id"], "currentPage": currentPage, "rowsPerPage": 10},
        success: function(response){
            parentComponent.find(".table-content").html(response);
            parentComponent.find(".pager-button a[value='" + page + "']").parent().closest(".pager-button").addClass("active");
        }
    });
}



//Получение массива выбранных строк таблицы
function getCheckedInputs() {
    var ids = [];
    $("input:checked").each(function() {
        ids.push($(this).parent().closest("tr").attr("value"));
    });
    return ids;
}


