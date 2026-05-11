function CarCategory(tabID, data){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

    var objAndValue = new Array;
    objAndValue.push({object:'hiditempositionkey[]', value :'pkey'});   
    var objAndValueForDetailItemPositionAutoComplete = objAndValue;


    this.showItemPositionTireAccess = function showItemPositionTireAccess(obj){  
              
                if ($(obj).val() == 1){ 
                    tabObj.find("item-position-access-list").hide(); 
                }else{  
                    tabObj.find("item-position-access-list").show();
                }
              
            }
    
    this.rebindEl = function rebindEl(){   
        
    } 

    this.loadOnReady = function loadOnReady(){ 

        tabObj.find(".section-panel .title" ).click(function() {  
            $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
			$(this).find(".icon-expand").toggle();
        });

        thisObj.rebindEl(); 

    }
    
}

