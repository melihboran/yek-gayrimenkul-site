/* genel uyarı şablonu */
function alert(Ititle, Itext, Iicon) {
    swal({
        title: Ititle,
        text: Itext,
        icon: Iicon,
        button: {
            visible: false,
        },
    });
};

/* uyarı sonrası yönlendirme */
function alertWithRedirect(Ititle, Itext, Iicon, IUrl) {
    swal({
        title: Ititle,
        text: Itext,
        icon: Iicon,
        button: {
            visible: false,
        },
        timer: 3000
    }).then(function () {
        window.location = "" + IUrl + "";
    }), 400
};

/* ajax: id ve dil kodu verilen bilginin karşılığını language.json'dan getirir. */
function getLang(byID) {
    return JSON.parse($.ajax({ url: '/assets/language.json', async: false }).responseText).filter(x => x.ID == byID)[0]["TR"];
}

/* post ajax */
function postAjax(PageURL, Parameters) {
    return JSON.parse($.ajax({
        type: "POST",
        contentType: "application/json; charset=utf-8",
        url: "/AjaxRequest.asmx/" + PageURL,
        data: Parameters,
        datatype: "json",
        async: false
    }).responseText);
}

/* post ajax data */
function postAjaxData(pageurl, dataString) {
    let paramaters = JSON.stringify({ value: dataString });
    return JSON.parse($.ajax({
        type: "POST",
        contentType: "application/json; charset=utf-8",
        url: "/AjaxRequest.asmx/" + pageurl,
        data: paramaters,
        datatype: "json",
        async: false
    }).responseText);
}

/* post ajax file */
function postAjaxFile(dataString) {
    let returningValue = "false";
    $.ajax({
        type: "POST",
        url: "/AjaxRequest.asmx/SaveFile",
        data: dataString,
        contentType: false,
        processData: false,
        async: false,
        success: function (data) {
            returningValue = data.documentElement.textContent;
        },
        error: function () {
            returningValue = "false";
        }
    });
    return returningValue;
}

/* dropdown doldur */
function FillDropdown(id, drpname, firstitem, data) {
    let drpDropdown = $("[id*=" + drpname + "]");
    drpDropdown.empty().append('<option selected="selected" value="0">' + firstitem + '</option>');
    $.each(data, function () {
        if (id != "") {
            if (this['Value'] == id) { drpDropdown.append($("<option selected></option>").val(this['Value']).html(this['Text'])); }
            else { drpDropdown.append($("<option></option>").val(this['Value']).html(this['Text'])); }
        }
        else { drpDropdown.append($("<option></option>").val(this['Value']).html(this['Text'])); }
    });
}

/* search */
var input = document.getElementById("SiteSearch");
input.addEventListener("keyup", function (event) {
    if (event.keyCode === 13) {
        Search();
    }
});
input.addEventListener("keypress", function (event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        Search();
    }
});

function Search() {
    var veriarama = $("#SiteSearch").val()
    veriarama = $.trim(veriarama)
    if (veriarama == "") {
        alert(getLang(2), getLang(4), "warning");
        return false;
    }
    else {
        window.location = "/arama/" + veriarama;
        return false;
    }
}