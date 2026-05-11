function Tax(tabID, LANG){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
    var LANG = JSON.parse(LANG);
    
    this.tabID = tabID;     

    var id = tabObj.find("[name=hidId]").val();
      
    this.rebindEl = function rebindEl(){   

    }

    this.loadOnReady = function loadOnReady(){  
        
        console.log(LANG)
        tabObj.find('[name="selTaxType"]').change(function() { 
            if ($(this).val() == 1){ 
                $(".pph-type").hide(); 
                $(".label-in").html( LANG.vatIn);
                $(".label-out").html(LANG.vatOut);
            }else{ 
                $(".pph-type").show(); 
                $(".label-in").html( LANG.payableTax23);
                $(".label-out").html(LANG.prepaidTax23);
            }
        });
         
        
        tabObj.find('[name="selTaxType"]').change();
        
        
        thisObj.rebindEl();
    }
}