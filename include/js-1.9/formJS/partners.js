function Partners(tabID, uploadFolder, rsImage){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    

    var folder = uploadFolder;
    var imageUploaderTarget = "item-image-uploader";
    var arrImage = Array(); 
    var arrPHPThumbHash = Array();

    var id = tabObj.find("[name=hidId]").val();  

    this.rebindEl = function rebindEl(){   
        
    }

    this.loadOnReady = function loadOnReady(){ 

        if(id){   
            for($i=0;$i<rsImage.length;$i++) {
                arrImage.push(rsImage[$i].file);
                arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
            } 
            createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);

        }else{ 
             createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,false); 
        }
        

        tabObj.find(".map-panel").click(function(e){
            var offset = $(this).offset();  
            var relX = e.pageX - offset.left;
            var relY = e.pageY - offset.top; 
             
            tabObj.find("[name='txtMapLocation']").val(relX + ", " + relY);
            var adj = -6;
            
            tabObj.find(".marker").css({top: (relY+adj)+"px", left: (relX+adj)+"px", position:'absolute'});
        });


        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}