function TemplateCustomer(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID); 
    
        var objAndValue = new Array; 
        objAndValue.push({object:'hidCustomerKey[]', value :'pkey'});  	    	 
        var objAndValueForDetailAutoComplete = objAndValue;
    
        this.tabID = tabID;    

 
        this.rebindEl = function rebindEl(){    
            bindAutoCompleteForTransactionDetail('customerName[]',objAndValueForDetailAutoComplete,'ajax-customer.php?action=searchData&statuskey=2'); 
        } 
        
        this.loadOnReady = function loadOnReady(){   
            thisObj.rebindEl();  
        }
        
}
