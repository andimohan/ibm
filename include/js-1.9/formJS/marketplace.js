function Marketplace(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
        this.tabID = tabID;   
    
        this.updateMarginDecimal = function updateMarginDecimal(obj){  
                    var parentObj =  $(obj).parent().parent();

                    var objDiscVal = parentObj.find("[name='marginValue']");
                    var discType = $(obj).val();

                    objDiscVal.removeClass("inputnumber").addClass("inputdecimal");

                    if (discType == 1){
                        objDiscVal.unbind("blur").bind( "blur", function(event) {  inputNumberOnBlur($(this)) });
                    }else{
                        objDiscVal.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this),2)}); 
                    } 

                   objDiscVal.blur(); 
 
        }

         
        this.updateDiscDecimal = function updateDiscDecimal(obj){  
                    var parentObj =  $(obj).parent().parent();

                    var objDiscVal = parentObj.find("[name='discountValue']");
                    var discType = $(obj).val();

                    objDiscVal.removeClass("inputnumber").addClass("inputdecimal");

                    if (discType == 1){
                        objDiscVal.unbind("blur").bind( "blur", function(event) {  inputNumberOnBlur($(this)) });
                    }else{
                        objDiscVal.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this),2)}); 
                    } 

                   objDiscVal.blur(); 
 
        }
        
        
        this.updatePriceType= function updatePriceType(obj){
            if (obj.val() == 1)
                tabObj.find(".isDiscount").hide();
            else
                tabObj.find(".isDiscount").show();   
        }
        
 
        this.rebindEl = function rebindEl(){}
         
        this.loadOnReady = function loadOnReady(){ 
            
            tabObj.find("[name=selFinalPriceType]" ).change(function(){thisObj.updatePriceType($(this))});
            tabObj.find("[name=selFinalPriceType]" ).change();
            thisObj.rebindEl(); 
        }
}