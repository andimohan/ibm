function DisposalJobOrder(tabID) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    this.tabID = tabID;
    var id = tabObj.find("[name=hidId]").val();
 

    this.updateContract = function updateContract() {
        var contactKey = tabObj.find("[name=hidContractKey]").val();
        if (!contactKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-disposal-contract.php',
            async: false,
            data: "action=searchData&pkey=" + contactKey,
        }).done(function (data) { 
            if (!data)   return;
            
            data = JSON.parse(data);
            data = data[0];
            
            tabObj.find("[name=duration]").val(data.duration).blur();
            tabObj.find("[name=maximumWeight]").val(data.maximumweight).blur();
            tabObj.find("[name=qtyService]").val(data.qtyservice).blur();
            tabObj.find("[name=contractDuration]").val(data.duration).blur();
            tabObj.find("[name=hidAreaKey]").val(data.citycategorykey);
            tabObj.find("[name=hidSalesKey]").val(data.saleskey);
            tabObj.find("[name=salesName]").val(data.salesname);
            tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
            tabObj.find("[name=hidServiceKey]").val(data.servicekey);
            tabObj.find("[name=serviceName]").val(data.servicename);
            tabObj.find("[name=customerName]").val(decodeHTMLEntities(data.customername));
            tabObj.find("[name=hidCityKey]").val(data.citykey);
            tabObj.find("[name=area]").val(data.cityandcategoryname);
            tabObj.find("[name=sellingPrice]").val(data.sellingprice).blur();
            tabObj.find("[name=exceedSellingPriceArea]").val(data.exceedprice).blur();
            tabObj.find("[name=exceedWeightPriceArea]").val(data.extraprice).blur();
            tabObj.find("[name=hidAreaKey]").val(data.citycategorykey);
            tabObj.find("[name=hidCityKey]").val(data.citykey);
            tabObj.find("[name=area]").val(data.cityandcategoryname);
            tabObj.find("[name=hidWasteCategoryKey]").val(data.wastekey);
            tabObj.find("[name=wasteCategoryName]").val(data.wastecategoryname);
            tabObj.find("[name=hidServiceDetailWasteKey]").val(data.servicedetailwastekey);

            
            thisObj.updateWaste(); 
        });
    }

    this.updateServicePackage = function updateServicePackage() {
        var serviceKey = tabObj.find("[name=hidServiceKey]").val();
        if (!serviceKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-service.php',
            async: false,
            data: "action=searchData&pkey=" + serviceKey,
        }).done(function (data) { 
            if (!data)   return;
            
            data = JSON.parse(data);
            data = data[0];
            
            tabObj.find("[name=duration]").val(data.duration).blur();
            tabObj.find("[name=maximumWeight]").val(data.qtyweight).blur();
            tabObj.find("[name=qtyService]").val(data.qtyservice).blur();
            tabObj.find("[name=contractDuration]").val(data.duration).blur();

            thisObj.updateCostArea(); 
        });
    }
    
    this.updateCustomerInformation = function updateCustomerInformation() {
        var customerkey = tabObj.find("[name=hidCustomerKey]").val();
        if (!customerkey)
            return;

        $.ajax({
            type: "GET",
            url: 'ajax-customer.php',
            async: false,
            data: "action=getLocationInformation&pkey=" + customerkey,
        }).done(function (data) {
 
            if (!data)    return;
            
            data = JSON.parse(data);
            data = data[0];

            tabObj.find("[name=hidAreaKey]").val(data.citycategorykey);
            tabObj.find("[name=hidCityKey]").val(data.citykey);
            tabObj.find("[name=area]").val(data.cityandcategoryname);
            thisObj.updateCostArea(); 
        });
    }

    this.updateWaste = function updateWaste() {
        var contractKey = tabObj.find("[name=hidContractKey]").val();
        if (!contractKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-disposal-contract.php',
            beforeSend: function (xhr) {
                    clearAllRows(tabObj.find('.waste-detail'));
            },
            async: false,
            data: "action=getWasteDetail&pkey=" + contractKey,
        }).done(function (data) {
            
            var data = JSON.parse(data);
            if (data.length == 0) {
                $newRow = addNewTemplateRow("waste-row-template",null,null,thisObj.rebindEl);
                return;
            }
                    
            var disabled = false;
            for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];
                     
                    arrPostValue.push({
                            "selector": "hidWasteKey",
                            "value": data[i].wastekey,
                            "disabled": disabled
                    });
                    arrPostValue.push({
                            "selector": "wasteName",
                            "value": data[i].wastecodename,
                            "disabled": disabled
                            
                });
                arrPostValue.push({
                        "selector": "weightPrice",
                        "value": data[i].weightprice,
                        "disabled": disabled
                });
                arrPostValue.push({
                        "selector": "minWeight",
                        "value": data[i].minweight,
                        "disabled": disabled
                });
                arrPostValue.push({
                        "selector": "maxWeight",
                        "value": data[i].maxweight,
                        "disabled": disabled
                });
            
                $newRow = addNewTemplateRow("waste-row-template",JSON.stringify(arrPostValue),null,thisObj.rebindEl);
            }

            tabObj.find(".inputnumber, .input-integer, .inputdecimal").blur();
        });
    }

    // this.updateCostArea = function updateCostArea() {
    //     var cityCategoryKey = tabObj.find("[name='hidAreaKey']").val();
    //     var serviceKey = tabObj.find("[name=hidServiceKey]").val();

    //     if (!cityCategoryKey || !serviceKey)
    //         return;

    //     $.ajax({
    //         type: "GET",
    //         url: 'ajax-service.php',
    //         async: false,
    //         data: "action=getDetailArea&pkey=" + serviceKey+ '&cityCategoryKey=' + cityCategoryKey,
    //     }).done(function (data) {
            
    //         if (!data)  return;
            
    //         data = JSON.parse(data);
    //         data = data[0];
            
    //         tabObj.find("[name=sellingPrice]").val(data.sellingprice).blur();
    //         tabObj.find("[name=exceedSellingPriceArea]").val(data.exceedsellingpricearea).blur();
    //         tabObj.find("[name=exceedWeightPriceArea]").val(data.exceedweightpriceaare).blur();
    //     });

    //     thisObj.calculateTotal(); 
    // }

    // this.calculateTotal = function calculateTotal() {
    //     var sellingPrice = parseInt(unformatCurrency(tabObj.find("[name='sellingPrice']").val()));
    //     var duration = tabObj.find("[name=duration]").val();
    //     var contractDuration = tabObj.find("[name=contractDuration]").val();

    //     if (contractDuration == 0 || duration == 0) {
    //         tabObj.find("[name=total]").val(0).blur();
    //         return;
    //     }

    //     var total = contractDuration / duration * sellingPrice ;
    //     tabObj.find("[name=total]").val(total).blur();
    // }
 
    // this.afterRemoveRowHandler = function afterRemoveRowHandler() {
    //     thisObj.calculateTotal();
    // };
  
    this.rebindEl = function rebindEl() {
    }

    this.loadOnReady = function loadOnReady() {
         
        // tabObj.find("[name=contractDuration]").change(function() { 
        //     thisObj.calculateTotal(); 
        // }); 
		

  
        thisObj.rebindEl();
    }
}
