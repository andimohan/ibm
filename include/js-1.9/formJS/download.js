function Download(tabID, opt){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     
  
    var id = tabObj.find("[name=hidId]").val();
	
	var folder = opt.uploadImageFolder;
	var imageUploaderTarget = "item-image-uploader";
	var arrImage =  (opt.arrImage && opt.arrImage.length > 0) ? opt.arrImage : Array();  
	var arrPHPThumbHash =  (opt.arrPHPThumbHash && opt.arrPHPThumbHash.length > 0) ? opt.arrPHPThumbHash : Array(); 
	
	var fileFolder = opt.uploadFileFolder;
	var fileUploaderTarget = "item-file-uploader"; 
	var arrFile =  (opt.arrFile) ? opt.arrFile : Array();  
 
    this.rebindEl = function rebindEl(){   
        
    }

    this.loadOnReady = function loadOnReady(){  
		if(id){ 
			createImageUploader({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget},{"folder":opt.uploadImageFolder, "token": id, "arrImage":opt.arrImage,"phpThumbHash":opt.arrPHPThumbHash},false); 
			createFileUploader(opt.fileUploaderTarget,opt.uploadFileFolder, id ,opt.arrFile,false);  
		}else{ 
			createImageUploader({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget},{"folder":opt.uploadImageFolder},false);
			createFileUploader(opt.fileUploaderTarget,opt.uploadFileFolder, "" , "",false); 
		}

		tabObj.find(".image-list ").sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemImageArray({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget}); }});
		tabObj.find(".image-list"  ).disableSelection();

		tabObj.find(".file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(opt.fileUploaderTarget); }});
		tabObj.find(".file-list"  ).disableSelection();  
		
		tabObj.find("[name=chkExternal]").change(function(){
            if ( $(this).val() == 1 ) {
               tabObj.find(".isexternal").show();
               tabObj.find(".isinternal").hide();
            }else{ 
               tabObj.find(".isexternal").hide();
               tabObj.find(".isinternal").show();
            }
        }); 
        
        tabObj.find("[name=chkExternal]").change();

        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}