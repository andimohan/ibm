function SalesPricing(tabID) {   
    
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    var objAndValue = new Array;
	objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
    var objAndValueForDetailAutoComplete = objAndValue;

    this.tabID = tabID;    


    this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();

            for(i=0;i<objAndValue.length;i++)  
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
    
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(decodeHTMLEntities(ui.item['value']));   
        }  
    
    this.rebindEl = function rebindEl(){   
        bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',thisObj.updateDetail);
    } 
    
    this.loadOnReady = function loadOnReady(){ 
        thisObj.rebindEl(); 
    }
    
}
