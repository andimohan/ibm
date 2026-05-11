function CurrencyRate(tabID,varConstant) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;  
        this.tablekey = varConstant.TABLEKEY;   
                  
        this.rebindEl = function rebindEl(){
          
        }
         
        this.loadOnReady = function loadOnReady(){ 
             
            thisObj.rebindEl(); 
        }
}