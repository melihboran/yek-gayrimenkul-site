/* formu gönder */
$(".btn-send-data").on("click", function () {

    let prefix = "." + $(this).data("prefix") + "-form-data";
    let formID = $(this).data("formid");
    let params = [{ id: formID, properties: [] }];
    let canSend = true;
    //------------------------------------------
    let person = "";
    $(prefix).each(function () {
        let dataID = $(this).attr("id");
        let dataName = $(this).data("field");
        let dataType = $(this).attr("type");
        let dataValue = $(this).val();
        //------------------------------------------
        if (dataID != "Kvkk" && dataValue != null && dataValue != "") {
            if (dataID == "NameSurname")
                person = dataValue;
            //------------------------------------------
            if (dataType == "file") {
                let dataFiles = new FormData();
                let files = $(this).get(0).files;
                if (files.length > 0) {
                    dataFiles.append("Title", person);
                    dataFiles.append("UploadedFile", files[0]);
                    dataValue = postAjaxFile(dataFiles);
                }
                else
                    return true;
            }
            //------------------------------------------
            if (dataType == "checkbox") {
                if ($(this)[0].checked == true)
                    params[0].properties.push({ "id": dataID, "type": dataType, "name": dataName, "value": "Evet" });
                else
                    params[0].properties.push({ "id": dataID, "type": dataType, "name": dataName, "value": "Hayır" });
            }
            else
                params[0].properties.push({ "id": dataID, "type": dataType, "name": dataName, "value": dataValue });

        }
        //------------------------------------------
        else if ($(this).prop('required') && $(this).prop('checked') != true) {
            canSend = false;
            alert(getLang(2), getLang(4), "warning");
        }
    });
    //------------------------------------------
    if (canSend) {
        swal({
            title: "Form gönderiliyor.",
            text: "Bilgileriniz işleniyor lütfen bekleyiniz...",
            timer: 2000,
            button: {
                visible: false,
            },
        }).then(function () {
            let data = postAjaxData("Connect", params);
            if (data.d == "true")
                alertWithRedirect(getLang(1), getLang(6), "success", window.location.href);
            else if (data.d == "false")
                alert(getLang(3), getLang(7), "error");
            else
                alert(getLang(3), data.d, "error");
        }), 4000;
    }
});