function TemplateSupplier(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID); 
    
        var objAndValue = new Array; 
        objAndValue.push({object:'hidSupplierKey[]', value :'pkey'});  	    	 
        var objAndValueForDetailAutoComplete = objAndValue;
    
        this.tabID = tabID;    

 
        this.rebindEl = function rebindEl(){   
            
            bindAutoCompleteForTransactionDetail('supplierName[]',objAndValueForDetailAutoComplete,'ajax-supplier.php?action=searchData');

   
        } 
        
        this.loadOnReady = function loadOnReady(){

              
            thisObj.rebindEl(); 

        }
        
}
 