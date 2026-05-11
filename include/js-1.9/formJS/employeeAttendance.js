function EmployeeAttendance(tabID, varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.tablekey = varConstant.TABLEKEY;  

    var id = tabObj.find("[name=hidId]").val();
     
    this.rebindEl = function rebindEl(){   
    }

    this.loadOnReady = function loadOnReady(){ 
 
        thisObj.rebindEl();
    }
}