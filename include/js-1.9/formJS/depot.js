function Depot(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue; 
    
        this.tabID = tabID;  
                  
        this.rebindEl = function rebindEl(){
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');
        }
         
        this.loadOnReady = function loadOnReady(){ 
             
            thisObj.rebindEl(); 
        }
}