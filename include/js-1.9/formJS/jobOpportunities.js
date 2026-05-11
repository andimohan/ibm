function JobOpportunities(tabID, uploadFolder, uploadFileFolder, rsImage,rsItemFile){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    
/*     var folder = uploadFolder;
    var imageUploaderTarget = "item-image-uploader";
    var arrImage = Array(); 
    var arrPHPThumbHash = Array();
    
    var fileFolder = uploadFileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = Array(); */
    
    var id = tabObj.find("[name=hidId]").val(); 
   

    this.rebindEl = function rebindEl(){   
        
    }

    this.loadOnReady = function loadOnReady(){ 
  /*      
        if(id){   
            for($i=0;$i<rsImage.length;$i++) {
                arrImage.push(rsImage[$i].file);
                arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
            } 
            
            for($i=0;$i<rsItemFile.length;$i++) {
                arrFile.push(rsItemFile[$i].file);
            } 
            
            createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);
            createFileUploader(fileUploaderTarget,fileFolder,id,arrFile,true);
            
        }else{ 
             createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,false); 
             createFileUploader(fileUploaderTarget,fileFolder,"","",true);
        }*/
        
        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}