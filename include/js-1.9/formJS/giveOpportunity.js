function GiveOpportunity(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID;    
    
        this.getOpportunity = function getOpportunity() {
            var val = tabObj.find('[name=selType]').val();
            switch (val) {
                case '3': // pro
                    tabObj.find(".opportunity").show();
                    break;
                default: 
                    tabObj.find(".opportunity").hide();
                    break;
            }

        }
    	  
        this.rebindEl = function rebindEl(){   
        } 
         
        this.loadOnReady = function loadOnReady(){ 
            tabObj.find('[name=selType]').on('change', function () {
                thisObj.getOpportunity(); 
            })
            thisObj.getOpportunity();             

            thisObj.rebindEl(); 

        }
        
}
