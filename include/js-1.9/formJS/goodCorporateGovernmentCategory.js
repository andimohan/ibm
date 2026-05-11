function GoodCorporateGovernmentCategory(tabID) { 

    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
      
    this.rebindEl = function rebindEl(){   
        
    }

    this.loadOnReady = function loadOnReady(){ 

        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}