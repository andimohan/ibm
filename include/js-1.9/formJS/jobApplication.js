function JobApplication(tabID, uploadFileFolder,rsItemFile){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
  
    var fileFolder = uploadFileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = Array(); 
    
    var id = tabObj.find("[name=hidId]").val();  

    this.rebindEl = function rebindEl(){   
        
    }

    this.loadOnReady = function loadOnReady(){ 
        
        if(id){   

            for($i=0;$i<rsItemFile.length;$i++) {
                arrFile.push(rsItemFile[$i].file);
            } 
            
             createFileUploader(fileUploaderTarget,fileFolder,id,arrFile,true);
            
        }else{ 
             createFileUploader(fileUploaderTarget,fileFolder,"","",true);
        }
        
        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}