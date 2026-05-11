function Location(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
        var adj = -6;
    
        this.tabID = tabID;    
       
 
        this.rebindEl = function rebindEl(){    
        } 
        
        this.updateMarker = function updateMarker(relX,relY ){
             
             relX = parseFloat(relX);
             relY = parseFloat(relY);
            
             tabObj.find(".marker").css({top: (relY+adj)+"px", left: (relX+adj)+"px", position:'absolute'});
        }
        
        this.loadOnReady = function loadOnReady(){ 
             

            tabObj.find(".map-panel").click(function(e){
                
                var offset = $(this).offset();  
                var relX = e.pageX - offset.left;
                var relY = e.pageY - offset.top; 

                tabObj.find("[name='txtMapLocation']").val(relX + ", " + relY);
                
                thisObj.updateMarker(relX,relY);
                
            });

            
            var pos = tabObj.find("[name='txtMapLocation']").val();
            
            if(pos != undefined && pos != ''){
                pos = pos.split(','); 
                thisObj.updateMarker(pos[0].trim(),pos[1].trim()); 
            }
            
            thisObj.rebindEl();  
        }
        
}
