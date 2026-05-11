function Achievement(tabID, uploadFolder, rsImage){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    

    var folder = uploadFolder;
    var imageUploaderTarget = "item-image-uploader";
    var arrImage = Array(); 
    var arrPHPThumbHash = Array();

    var id = tabObj.find("[name=hidId]").val();
    
//    var  objAndValue = new Array;
//    objAndValue.push({object:'hidCategoryKey[]', value :'pkey'});
//    objAndValue.push({object:'categoryName[]', value :'name'}); 
//    var objAndValueForDetailAutoComplete  = objAndValue; 
     
    
    this.rebindEl = function rebindEl(){   
//        bindAutoCompleteForTransactionDetail('categoryName[]',  objAndValueForDetailAutoComplete,'ajax-article-category.php?action=searchData'); 
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
        
        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}