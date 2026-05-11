function DisposalContract(tabID, opt, data) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    this.tabID = tabID;  
    var id = tabObj.find("[name=hidId]").val(); 

    this.useStorage = opt.useStorage;  
    
    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = (opt.arrFile) ? opt.arrFile : Array();

    var objAndValueForDetailAssetAutoComplete = {};    
    objAndValue = new Array;
    objAndValue.push({
        object: 'hidAssetGroupKey[]',
        value: 'pkey'
    });
    var objAndValueForDetailAssetAutoComplete = objAndValue;

    var objAndValueForDetailAutoComplete = {};    
    objAndValue = new Array;
    objAndValue.push({
        object: 'hidItemKey[]',
        value: 'pkey'
    });
    var objAndValueForDetailAutoComplete = objAndValue;

    var objAndValueForDetailWasteAutoComplete = {};    
    objAndValue = new Array;
    objAndValue.push({
        object: 'hidWasteKey[]',
        value: 'pkey'
    });
    var objAndValueForDetailWasteAutoComplete = objAndValue;

    this.updateService = function updateService() {
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
            
            tabObj.find("[name=hidWasteCategoryKey]").val(data.wastecategorykey);
            tabObj.find("[name=wasteCategoryName]").val(data.wastecategoryname);
            tabObj.find("[name=duration]").val(data.duration).blur();
            tabObj.find("[name=maximumWeight]").val(data.qtyweight).blur();
            tabObj.find("[name=qtyService]").val(data.qtyservice).blur();
            thisObj.updateCostArea(); 
            thisObj.updateAsset(); 
            thisObj.updateItem(); 
            thisObj.updateWaste(); 
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
            thisObj.updateService(); 
        });
    }

    this.updateAsset = function updateAsset() {
        var serviceKey = tabObj.find("[name=hidServiceKey]").val();
        if (!serviceKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-service.php',
            beforeSend: function (xhr) {
                    clearAllRows(tabObj.find('.asset-detail'));
            },
            async: false,
            data: "action=getAssetGroupDetail&pkey=" + serviceKey,
        }).done(function (data) {
            
            var data = JSON.parse(data);
            if (data.length == 0) {
                $newRow = addNewTemplateRow("asset-row-template",null,null,thisObj.rebindEl);
                return;
            }
                    
            var disabled = false;
            for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];
                     
                    arrPostValue.push({
                            "selector": "hidAssetGroupKey",
                            "value": data[i].assetgroupkey,
                            "disabled": disabled
                    });
                    arrPostValue.push({
                            "selector": "assetGroupName",
                            "value": data[i].assetgroupname,
                            "disabled": disabled
                            
                });
                arrPostValue.push({
                        "selector": "qtyAsset",
                        "value": data[i].qty,
                        "disabled": disabled
                });
            
                $newRow = addNewTemplateRow("asset-row-template",JSON.stringify(arrPostValue),null,thisObj.rebindEl);
            }

            tabObj.find(".inputnumber, .input-integer, .inputdecimal").blur();
        });
    }

    this.updateItem = function updateItem() {
        var serviceKey = tabObj.find("[name=hidServiceKey]").val();
        if (!serviceKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-service.php',
            beforeSend: function (xhr) {
                    clearAllRows(tabObj.find('.item-detail'));
            },
            async: false,
            data: "action=getItemDetail&pkey=" + serviceKey,
        }).done(function (data) {
            
            var data = JSON.parse(data);
            if (data.length == 0) {
                $newRow = addNewTemplateRow("item-row-template",null,null,thisObj.rebindEl);
                return;
            }
                    
            var disabled = false;
            for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];
                     
                    arrPostValue.push({
                            "selector": "hidItemKey",
                            "value": data[i].itemkey,
                            "disabled": disabled
                    });
                    arrPostValue.push({
                            "selector": "itemName",
                            "value": data[i].itemname,
                            "disabled": disabled
                            
                });
                arrPostValue.push({
                        "selector": "qty",
                        "value": data[i].qty,
                        "disabled": disabled
                });
            
                $newRow = addNewTemplateRow("item-row-template",JSON.stringify(arrPostValue),null,thisObj.rebindEl);
            }

            tabObj.find(".inputnumber, .input-integer, .inputdecimal").blur();
        });
    }
    this.updateWaste = function updateWaste() {
        var serviceKey = tabObj.find("[name=hidServiceKey]").val();
        var serviceDetailWasteKey = tabObj.find("[name=hidServiceDetailWasteKey]").val();
        var wasteCategoryKey = tabObj.find("[name=hidWasteCategoryKey]").val();
        if (!serviceKey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-service.php',
            beforeSend: function (xhr) {
                    clearAllRows(tabObj.find('.waste-detail'));
            },
            async: false,
            data: "action=getDetailWaste&pkey=" + serviceDetailWasteKey +"&wasteCategoryKey=" +wasteCategoryKey ,
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
                        "value": data[i].sellingprice,
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

    this.updateCostArea = function updateCostArea() {
        var cityCategoryKey = tabObj.find("[name='hidAreaKey']").val();
        var serviceKey = tabObj.find("[name=hidServiceKey]").val();

        if (!cityCategoryKey || !serviceKey)
            return;

        $.ajax({
            type: "GET",
            url: 'ajax-service.php',
            async: false,
            data: "action=getDetailArea&pkey=" + serviceKey+ '&cityCategoryKey=' + cityCategoryKey,
        }).done(function (data) {
            
            if (!data)  return; 
            
            data = JSON.parse(data);
            if (data.length == 0)  {
            
                    
                tabObj.find("[name=sellingPrice]").val(0).blur();
                tabObj.find("[name=hidServiceDetailWasteKey]").val(0).blur();
                tabObj.find("[name=exceedSellingPriceArea]").val(0).blur();

                return;
            
            }
            
            data = data[0];
            
            tabObj.find("[name=sellingPrice]").val(data.sellingprice).blur();
            tabObj.find("[name=hidServiceDetailWasteKey]").val(data.pkey).blur();
            tabObj.find("[name=exceedSellingPriceArea]").val(data.exceedsellingpricearea).blur();
        });

        // thisObj.calculateTotal(); 
    }

    // this.calculateTotal = function calculateTotal() {
    //     var sellingPrice = parseFloat(unformatCurrency(tabObj.find("[name='sellingPrice']").val()));
    //     var duration = tabObj.find("[name=duration]").val();
    //     var contractDuration = tabObj.find("[name=contractDuration]").val();

    //     if (contractDuration == 0 || duration == 0) {
    //         tabObj.find("[name=total]").val(0).blur();
    //         return;
    //     }

    //     var total = contractDuration / duration * sellingPrice ;
    //     tabObj.find("[name=total]").val(total).blur();
    // }
 
    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        // thisObj.calculateTotal();
    };
  
    this.rebindEl = function rebindEl() {
        
        var serviceDetailWasteKey = tabObj.find("[name='hidServiceDetailWasteKey']").val();
        var wasteCategoryKey = tabObj.find("[name=hidWasteCategoryKey]").val();
        bindAutoCompleteForTransactionDetail('assetGroupName[]', objAndValueForDetailAssetAutoComplete, 'ajax-asset-group.php?action=searchData');
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData&itemtype=1');
        bindAutoCompleteForTransactionDetail('wasteName[]', objAndValueForDetailWasteAutoComplete, 'ajax-service.php?action=getDetailWaste&pkey='+serviceDetailWasteKey+"&wasteCategoryKey=" +wasteCategoryKey );
    }

    this.loadOnReady = function loadOnReady() {
        
        if(thisObj.useStorage){
            
        }else{
               if (fileFolder) {

                    if (id) {
                        createFileUploader(fileUploaderTarget, fileFolder, id, arrFile, true);
                    } else {
                        createFileUploader(fileUploaderTarget, fileFolder, "", "", true);
                    }

                    tabObj.find(".file-list").sortable({
                        placeholder: "sortable-placeholder",
                        stop: function (event, ui) {
                            updateItemFileArray(opt.fileUploaderTarget);
                        }
                    });
                    tabObj.find(".file-list").disableSelection();

                }

        }

        if(data != undefined){ 
            if (!data['assetGroupDetail'] || data['assetGroupDetail'].length == 0)
                addNewTemplateRow("asset-row-template",null,null,thisObj.rebindEl);

            if (!data['itemDetail'] || data['itemDetail'].length == 0)
                addNewTemplateRow("item-row-template",null,null,thisObj.rebindEl);

            if (!data['wasteDetail'] || data['wasteDetail'].length == 0)
                addNewTemplateRow("waste-row-template",null,null,thisObj.rebindEl);
        }
 

  
        thisObj.rebindEl();
    }
}
