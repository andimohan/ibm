function GoodCorporateGovernmentReport(tabID, uploadFolder, rsImage, uploadFileFolder, rsFile){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    
      
    var folder = uploadFolder;
    var imageUploaderTarget = "item-image-uploader";
    var arrImage = Array(); 
    var arrPHPThumbHash = Array();

    
    var fileFolder = uploadFileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = Array(); 
    

    var id = tabObj.find("[name=hidId]").val();
 
    this.rebindEl = function rebindEl(){   
      } 
     
    this.loadOnReady = function loadOnReady() { 
         
        if(id){   
            for($i=0;$i<rsImage.length;$i++) {
                arrImage.push(rsImage[$i].file);
                arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
            } 
            createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);

            for($i=0;$i<rsFile.length;$i++) 
                arrFile.push(rsFile[$i].file); 
            
            createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  
            
        }else{ 
             createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,false);
             createFileUploader(fileUploaderTarget, fileFolder, "", "", false);
        }
        
        
        
        multiLang(tabObj); 
        thisObj.rebindEl(); 
    }
    
}
