function CostGrouping(tabID, data){   
    var thisObj = this;
    var tabObj = $("#" + tabID);  
     
    	var objAndValue = new Array; 
        objAndValue.push({object:'hidCoaKey[]', value :'pkey'}); 

        var objAndValueForDetailAutoComplete = objAndValue;
           
        this.tabID = tabID;    
                
        this.rebindEl = function rebindEl(){       
            bindAutoCompleteForTransactionDetail('coaCode[]',objAndValueForDetailAutoComplete,'ajax-coa.php?action=searchData');
        }
         
        this.loadOnReady = function loadOnReady(){  
            thisObj.rebindEl();

             if (!data['rsDetail'] || data['rsDetail'].length == 0)
                addNewTemplateRow("cost-grouping-row-template",null,null,thisObj.rebindEl);
        }
}