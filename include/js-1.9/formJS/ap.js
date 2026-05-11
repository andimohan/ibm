function AP(tabID, varConstant, apType){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
 
    this.tablekey = varConstant.TABLEKEY;  
    
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
        apTypeObj = tabObj.find('[name=selAPType]'); 
        amountObj = tabObj.find('[name=amount]'); 
        var amount =  Math.abs(unformatCurrency(amountObj.val())); 

        amount *= apType[apTypeObj.val()].contra;
        amountObj.val(amount).blur(); 
    }
      
    this.rebindEl = function rebindEl(){ }

    this.loadOnReady = function loadOnReady(){ 
        // on change currency, chnage amout decimal type
        tabObj.find("[name=selCurrency]").on('change',function() { thisObj.updateCurrency(this) }); 
        tabObj.find("[name=selCurrency]").change();
         
        tabObj.find("[name=selAPType],[name=amount]").on('change',function() { thisObj.updateAmount() }); 
    }
}