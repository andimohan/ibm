function AssetGroup(tabID ){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;

    var id = tabObj.find("[name=hidId]").val();  

    this.rebindEl = function rebindEl(){ }

    this.loadOnReady = function loadOnReady(){   
        thisObj.rebindEl();
    }
}