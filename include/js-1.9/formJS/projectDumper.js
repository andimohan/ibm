function ProjectDumper(tabID) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj; 
    
		var objAndValue = new Array;  
		objAndValue.push({object:'hidLocationDetailKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'pricePerDistance[]', value :'priceperdistance'});
        var objAndValueForDetailAutoComplete = objAndValue;  
        
        this.tabID = tabID;     
     
        
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('locationDetailName[]',objAndValueForDetailAutoComplete,'ajax-location.php?action=searchData'); 
        }
        
        this.loadOnReady = function loadOnReady(){
               
            
            thisObj.rebindEl(); 
  
        } 
     }
