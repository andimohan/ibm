function Service(tabID, opt, data) {
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     
  
    var id = tabObj.find("[name=hidId]").val();
 
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

    this.updateCommission = function updateCommission(){ 
        var isCommissionPerVisit = tabObj.find("[name=chkIsCommissionPerVisit]").val();  
        if (isCommissionPerVisit == 1) {
            tabObj.find("[name='salesCommission[]']").val(0).prop("readonly", true);
            tabObj.find("[name=commissionPerVisit]").prop("readonly", false);
        } else {
            tabObj.find("[name=commissionPerVisit]").val(0).prop("readonly", true);
            tabObj.find("[name='salesCommission[]']").prop("readonly", false);
        }
    }

    this.updateWasteCategory = function updateWasteCategory(){ 

        var wasteCategoryKey = tabObj.find("[name=hidWasteCategoryKey]").val();  
        if (data['wasteCategory'][wasteCategoryKey]['ismedis'] == 1) {
            $("#" + tabID + " .medis-1-waste-detail-row").show();
            $("#" + tabID + " .medis-0-waste-detail-row").hide();
        } else {
            $("#" + tabID + " .medis-1-waste-detail-row").hide();
            $("#" + tabID + " .medis-0-waste-detail-row").show();
        }
    }
    
    var objAndValueForDetailAutoComplete = objAndValue;
    
    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('assetGroupName[]', objAndValueForDetailAssetAutoComplete, 'ajax-asset-group.php?action=searchData');
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData&itemtype=1');
    }
 

    this.loadOnReady = function loadOnReady(){ 
 
       if(opt.showItemImage){ 
           
            if(id){      
                createImageUploader({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget},{"folder":opt.uploadImageFolder, "token": id, "arrImage":opt.arrImage,"phpThumbHash":opt.arrPHPThumbHash}); 
                createImageUploader({"tabID":thisObj.tabID, "name":opt.iconUploaderTarget},{"folder":opt.uploadIconFolder, "token": id, "arrImage":opt.arrIcon,"phpThumbHash":opt.arrIconPHPThumbHash},false); 
            }else{ 
                createImageUploader({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget},{"folder":opt.uploadImageFolder});
                createImageUploader({"tabID":thisObj.tabID, "name":opt.iconUploaderTarget},{"folder":opt.uploadIconFolder},false); 
            } 
       }

 
        // untuk kompabilitas services tanpa detail 
        if(data != undefined){ 
            if (!data['assetGroupDetail'] || data['assetGroupDetail'].length == 0)
                addNewTemplateRow("asset-row-template",null,null,thisObj.rebindEl);

            if (!data['itemDetail'] || data['itemDetail'].length == 0)
                addNewTemplateRow("item-row-template",null,null,thisObj.rebindEl);
        }

        tabObj.find("[name=chkIsCommissionPerVisit]").change(function() { thisObj.updateCommission(); });
        tabObj.find("[name=chkIsCommissionPerVisit]").change();

        tabObj.find("[name=hidWasteCategoryKey]").change(function() { thisObj.updateWasteCategory(); });
        tabObj.find("[name=hidWasteCategoryKey]").change();

 
        tabObj.find("[name=btnAddDescription]").on('click', function() { addNewTemplateRow("item-description-row-template"); }); 
        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}
