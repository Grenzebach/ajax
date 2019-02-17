function getCurrentPage(href) {
    var hrefElements = href.split("=");
    var type = hrefElements[0];
    var id = hrefElements[1];
    if (type == undefined || type == "") {
        type = "default";
    } else {
        type = type.replace("#", "");
    }

    if (id == undefined || id == "") {
        id = "default";
    }

    return {"name": type, "id": id};
}