function NewRelease(tabID, arrUploadImage, uploadDetailFolder, rsDetailImage){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

    var detailFolder = uploadDetailFolder;
    var detailImageUploaderTarget = "item-detail-image-uploader";
    var arrDetailImage = Array(); 
    var arrDetailPHPThumbHash = Array();

    var id = tabObj.find("[name=hidId]").val();
      
    this.rebindEl = function rebindEl(){   
    
    } 
    
    this.loadOnReady = function loadOnReady() { 


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

        // detail
          if(id){   
                for($i=0;$i<rsDetailImage.length;$i++) {
                    arrDetailImage.push(rsDetailImage[$i].file);
                    arrDetailPHPThumbHash.push(rsDetailImage[$i].phpthumbhash);
                } 
                createImageUploader({"tabID":tabID, "name":detailImageUploaderTarget},{"folder":detailFolder, "token":id, "arrImage":arrDetailImage,"phpThumbHash":arrDetailPHPThumbHash},true);
 
            }else{ 
                 createImageUploader({"tabID":tabID, "name":detailImageUploaderTarget}, {"folder":detailFolder} ,true);  
            }

        
        multiLang(tabObj); 
        thisObj.rebindEl(); 

    }
    
}
