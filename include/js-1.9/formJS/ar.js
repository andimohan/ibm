function AR(tabID,tablekey, varConstant, arType){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.tablekey = tablekey;   

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
        arTypeObj = tabObj.find('[name=selARType]'); 
        amountObj = tabObj.find('[name=amount]'); 
        var amount =  Math.abs(unformatCurrency(amountObj.val()));  

        amount *= arType[arTypeObj.val()].contra; 
        amountObj.val(amount).blur(); 
         
        thisObj.updateTotalIDR();
    }
          
     this.updateTotalIDR = function updateTotalIDR(){  
        var rate = unformatCurrency(tabObj.find('[name=currencyRate]').val()); 
        var amount = unformatCurrency(tabObj.find('[name=amount]').val()); 
        var total = amount * rate; 
       
        tabObj.find('[name=amountIDR]').val(total).blur();    
    }
    
    this.rebindEl = function rebindEl(){ }

    this.loadOnReady = function loadOnReady(){ 
        // on change currency, chnage amout decimal type
        tabObj.find("[name=selCurrency]").on('change',function() { thisObj.updateCurrency(this) }); 
        tabObj.find("[name=selCurrency]").change();
          
        tabObj.find("[name=selARType],[name=currencyRate],[name=amount]").on('change',function() { thisObj.updateAmount() });  
    }
}