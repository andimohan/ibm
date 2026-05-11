function GoodCorporateGovernment(tabID, data,  uploadFolder, rsImage,uploadFileFolder, rsFile) { 

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
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidRefTeamKey[]', value :'pkey'});   
    objAndValue.push({object:'teamName[]', value :'name'}); 
    var objAndValueForDetailAutoComplete = objAndValue;
    
    
    var objAndValue = new Array;
    objAndValue.push({object:'hidReportKey[]', value :'pkey'});   
    objAndValue.push({object:'reportName[]', value :'title'});    
    var objAndValueForDetailReportAutoComplete = objAndValue;

    
    this.rebindEl = function rebindEl(){   
        bindAutoCompleteForTransactionDetail('reportName[]',objAndValueForDetailReportAutoComplete,'ajax-good-corporate-government-report.php?action=searchData');   
        bindAutoCompleteForTransactionDetail('teamName[]',  objAndValueForDetailAutoComplete,'ajax-management-team.php?action=searchData'); 
    }

    this.loadOnReady = function loadOnReady(){ 

        if (!data['rsGoodCorporateGovernmentReport'] || data['rsGoodCorporateGovernmentReport'].length  < 1)
            addNewTemplateRow("good-corporate-government-report-row-template", null, null, thisObj.rebindEl);
        
        if (!data['rsGoodCorporateGovernmentTeam'] || data['rsGoodCorporateGovernmentTeam'].length  < 1)
                addNewTemplateRow("good-corporate-government-team-row-template",null,null,thisObj.rebindEl);

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