function ZoneTransfer(tabID) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    this.tabID = tabID;


    this.importData = function importData() {
        thisObj.activeAjaxConnections = 0;

        var warehouselayoutoriginkey = tabObj.find("[name=hidWarehouseLayoutOriginKey]").val();

        if (warehouselayoutoriginkey == '') {
            return;
        }

        var refkey = tabObj.find("[name=hidRefKey]").val();

        if (refkey == '') {
            return;
        }

        $.ajax({
            type: "GET",
            url: "ajax-item.php",
            beforeSend: function (xhr) {
                clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++;
            },
            data: "action=getDataForZoneTransfer&pkey=" + refkey + "&warehouselayoutoriginkey=" + warehouselayoutoriginkey,
            success: function (data) {

                if (!data) {
                    addNewTemplateRow("detail-row-template");
                    thisObj.rebindEl();
                    return;
                }

                var data = parseJSON(data);

                var i;
                for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];

                    arrPostValue.push({ "selector": "hidItemKey", "value": data[i].pkey });
                    arrPostValue.push({ "selector": "itemName", "value": data[i].name });
                    // arrPostValue.push({ "selector": "qty", "value": data[i].qtyinbaseunit });

                    addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));
                }

                thisObj.rebindEl();

                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

                decreaseActiveAjaxConnections(thisObj);

            },
            error: function (xhr, errDesc, exception) {
                decreaseActiveAjaxConnections(thisObj);
            }
        });



    }

    this.rebindEl = function rebindEl() {

    }

    this.loadOnReady = function loadOnReady() {

        thisObj.rebindEl();

    }


}