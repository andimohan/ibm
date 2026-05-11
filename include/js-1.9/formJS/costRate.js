function CostRate(tabID){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      

    var objAndValue = new Array;
    objAndValue.push({object:'hidJobTypeKey[]', value :'pkey'});    
    var objAndValueForDetailAutoComplete = objAndValue;
   
    this.tabID = tabID;    
 
    var id = tabObj.find("[name=hidId]").val(); 

    this.rebindEl = function rebindEl(){   
         bindAutoCompleteForTransactionDetail('jobTypeName[]',objAndValueForDetailAutoComplete,'ajax-trucking-job.php?action=searchData'); 
    };

    this.loadOnReady = function loadOnReady(){ 
        thisObj.rebindEl(); 
    };
}