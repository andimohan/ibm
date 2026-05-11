function EmployeeAttendanceImport(tabID, varConstant,uploadFileFolder,rsFile){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.tablekey = varConstant.TABLEKEY;  

    var id = tabObj.find("[name=hidId]").val();
     
	var fileFolder = uploadFileFolder;
	var fileUploaderTarget = "item-file-uploader";
	var arrFile = Array(); 

    this.rebindEl = function rebindEl(){   
    }

    this.loadOnReady = function loadOnReady(){ 
 
		 if(id){    
			for($i=0;$i<rsFile.length;$i++) 
				arrFile.push(rsFile[$i].file); 

			createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  
		}else{  
			 createFileUploader(fileUploaderTarget, fileFolder, "", "", false);
		}

        thisObj.rebindEl();
    }
}