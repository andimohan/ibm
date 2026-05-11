function CarServiceMaintenanceRequest(tabID){   
   var thisObj = this;
   var tabObj = $("#" + tabID);    

   this.tabID = tabID;   
   
   this.updateDisplay = function updateDisplay(obj){   
            var selectedType = obj.val();
            tabObj.find(".vehicle-type").hide();
            tabObj.find(".type-"+selectedType).show();
   }
      
   this.rebindEl = function rebindEl(){   
     
   } 
   
   this.loadOnReady = function loadOnReady(){ 
      

       tabObj.find("[name=selType]").change(function(){thisObj.updateDisplay($(this))}) 

      thisObj.rebindEl(); 

   }
   
}
