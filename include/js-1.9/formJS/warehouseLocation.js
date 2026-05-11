function WarehouseLocation(tabID, data){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;    

        var objAndValue = new Array; 
        objAndValue.push({object:'hidWarehouseLayoutKey[]', value :'pkey'});  
        objAndValue.push({object:'warehouseLayoutName[]', value :'value'});   
        var objAndValueForDetailDownpaymentAutoComplete = objAndValue;
  
        var id = tabObj.find("[name=hidId]").val(); 
      
        this.rebindEl = function rebindEl(){
			 // tetep kirim marketplacekey gpp, nanti di class akan otoamtis diambil dr providernya 
             bindAutoCompleteForTransactionDetail('warehouseLayoutName[]', objAndValueForDetailDownpaymentAutoComplete, 'ajax-warehouse-layout.php?action=searchData&parentkey=0');
        }
          
        this.loadOnReady = function loadOnReady(){ 
            
            if (!data['rsDetailLayout'] || data['rsDetailLayout'].length == 0) {
                addNewTemplateRow("detail-warehouse-row-template",null,null,thisObj.rebindEl);
            }
            thisObj.rebindEl();
        }
}