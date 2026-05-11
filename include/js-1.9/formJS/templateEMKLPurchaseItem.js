function TemplateEMKLPurchaseItem(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;
     
    	var objAndValue = new Array;
		objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue;
               
        this.getRowObj = function getRowObj(obj){
            return obj.closest(".div-table-row");
        } 
         
          
         this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
         }
         
        
        this.rebindEl = function rebindEl(){   
            bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=3');   
        }
        
           
        this.loadOnReady = function loadOnReady(){   
            thisObj.rebindEl();
        }
}
