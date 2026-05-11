function NotificationLetter(tabID) {   
    
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     
  
    var id = tabObj.find("[name=hidId]").val();
  
    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction")); 
        thisObj.rebindEl();  
    }
 
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
       
    }

    this.updateDetail = function updateDetail(target, objAndValue, ui) {
        
    }

    this.rebindEl = function rebindEl(){   
     
    }

    this.loadOnReady = function loadOnReady(){  
        thisObj.rebindEl();
    }
}