function PrepaidExpense(tabID, varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
 
    
    this.updateCurrency = function updateCurrency(obj){ 
        
       obj = $(obj);
       var amountObj = tabObj.find("[name=amount]");
       var outstandingObj = tabObj.find("[name=outstanding]");
        
       if(obj.val() == varConstant.CURRENCY.idr){  
            changeNumberDecimal(amountObj,true);
            changeNumberDecimal(outstandingObj,true);
       }else{
            changeNumberDecimal(amountObj,false);
            changeNumberDecimal(outstandingObj,false);
       }
        
        amountObj.blur();
        outstandingObj.blur();
    }
      
    this.updateAmount = function updateAmount(){  
        
        amountObj = tabObj.find('[name=amount]'); 
        var amount =  Math.abs(unformatCurrency(amountObj.val())); 

        amountObj.val(amount).blur(); 
    }
      
    this.rebindEl = function rebindEl(){ }

    this.loadOnReady = function loadOnReady(){ 
        // on change currency, chnage amout decimal type
        tabObj.find("[name=selCurrency]").on('change',function() { thisObj.updateCurrency(this) }); 
        tabObj.find("[name=selCurrency]").change();
         
    }
}