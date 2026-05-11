function DisposalWorkOrderDispatcher(tabID) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    this.tabID = tabID;
    var id = tabObj.find("[name=hidId]").val();

    var objAndValue = new Array;
    objAndValue.push({ object: 'hidJobOrderKey[]', value: 'pkey' }); 
    objAndValue.push({ object: 'customerName[]', value: 'customername'});
    objAndValue.push({ object: 'contractName[]', value: 'contractname'});
    objAndValue.push({ object: 'hidCustomerKey[]', value: 'customerkey' });
    objAndValue.push({ object: 'serviceName[]', value: 'servicename'  });
    objAndValue.push({ object: 'hidServiceKey[]',  value: 'servicekey'  });
    objAndValue.push({ object: 'quota[]',  value: 'maximumweight' });
    var objAndValueForDetailAutoComplete = objAndValue;
   
    this.updateDetail = function updateDetail(target, objAndValue, ui) {
        var detailRow = $(target).closest(".transaction-detail-row"); 
        thisObj.updateRowInformation(detailRow, objAndValue, ui);  
    }


    this.updateDriver = function updateDriver() {
        var carKey = tabObj.find("[name=hidCarKey]").val();
        if (!carKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-car.php',
            async: false,
            data: "action=searchData&pkey=" + carKey,
        }).done(function (data) { 
            if (!data)   return;
            
            data = JSON.parse(data);
            data = data[0];
            
            tabObj.find("[name=hidDriverKey]").val(data.driverkey);
            tabObj.find("[name=driverName]").val(data.drivername);
        });
    }

    this.updateRowInformation = function updateRowInformation(detailRow, objAndValue, ui) {
 
        for (i = 0; i < objAndValue.length; i++) { 
            detailRow.find("[name='" + objAndValue[i].object + "']").first().val(ui.item[objAndValue[i].value]).blur(); 
        }
 
        // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
        detailRow.find("[name='jobOrderCode[]']").first().val(ui.item['code']); 
    }
   
    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('jobOrderCode[]', objAndValueForDetailAutoComplete, 'ajax-disposal-job-order.php?action=searchDataForWOList', thisObj.updateDetail);
    }

    this.loadOnReady = function loadOnReady() { 
        thisObj.rebindEl();
    }
}
