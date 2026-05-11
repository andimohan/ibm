function NewsCategory(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
        
        this.tabID = tabID;    
 
        this.rebindEl = function rebindEl(){
			 // tetep kirim marketplacekey gpp, nanti di class akan otoamtis diambil dr providernya 
       }
          
        this.loadOnReady = function loadOnReady(){ 
         
            multiLang(tabObj); 
            thisObj.rebindEl();
        }
}