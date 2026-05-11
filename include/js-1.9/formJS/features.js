function Features(tabID, arrUploadImage){
    
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     

    var id = tabObj.find("[name=hidId]").val();

    this.loadOnReady = function loadOnReady(){ 

    $.each(arrUploadImage, function( index,item ) {
     
        var folder = item.uploadFolder;
        var uploaderTarget = item.uploaderTarget;
        var rsImage =  item.rsImage;
        var arrImage = Array(); 
        var arrPHPThumbHash = Array();

        if(id){   
            for($i=0;$i<rsImage.length;$i++) {
                arrImage.push(rsImage[$i].file);
                arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
            } 
            createImageUploader({"tabID":tabID, "name":uploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);

        }else{ 
            createImageUploader({"tabID":tabID, "name":uploaderTarget}, {"folder":folder} ,false); 
        }
        
    });
 
        multiLang(tabObj); 
//        thisObj.rebindEl();
    }

}