function Customer(tabID, opt){   
        var thisObj = this;
        var tabObj = $("#" + tabID);   
    
        var data = opt.data;
        var useStorage = opt.useStorage;
     
        this.tabID = tabID; 
    
		var id = tabObj.find("[name=hidId]").val(); 
	
		var fileFolder = (opt.uploadFileFolder) ? opt.uploadFileFolder : ''; 
        var fileUploaderTarget = (opt.fileUploaderTarget) ? opt.fileUploaderTarget : 'item-file-uploader';   
        var arrFile =  (opt.arrFile) ? opt.arrFile : Array();  
	
		var imageFolder = (opt.uploadImageFolder) ? opt.uploadImageFolder : '';
        var imageUploaderTarget =  (opt.imageUploaderTarget) ? opt.imageUploaderTarget : 'item-image-uploader'; 
        var arrImage =  (opt.arrImage) ? opt.arrImage : Array(); 
        var arrPHPThumbHash =  (opt.arrPHPThumbHash) ? opt.arrPHPThumbHash : Array();   
	
		/*var objAndValue = new Array;
		objAndValue.push({object:'hidLocationDetailKey[]', value :'pkey'});   
        var objAndValueForDetailAutoComplete = objAndValue;*/
 

        var objAndValue = new Array;
        objAndValue.push({ object: 'hidItemKey[]',  value: 'pkey'});
	
        var objAndValueForDetailAutoComplete = objAndValue;
	
        this.updateMainAccount = function updateMainAccount(){
            //var isMain = tabObj.find("[name=chkIsMainAccount]").val();
            tabObj.find(".main-account").toggle(); 
            tabObj.find(".sub-account").toggle(); 
        }   
		
        this.updateCategory = function updateCategory(){  
            var category = tabObj.find("[name=selCategory]").val();
			tabObj.find(".category-field").hide();
			tabObj.find(".category-field.category-field-" + category).show(); 
        }
		
		this.updateInsuredCompany = function updateInsuredCompany(){   
            var isInsured = tabObj.find("[name=isInsured]" ).val();
            if (isInsured == 1){
                $("#" + tabID + " .insured-company").show();
            }else{ 
                $("#" + tabID + " .insured-company").hide();
            }
            
        }
		
		
        this.rebindEl = function rebindEl(){      

            bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData');
        	//bindAutoCompleteForTransactionDetail('locationDetailName[]',objAndValueForDetailAutoComplete,'ajax-location.php?action=searchData');   
        }   
		
        this.loadOnReady = function loadOnReady(){
			 
			var chkMainAccount = tabObj.find("[name=chkIsMainAccount]");
			if(chkMainAccount) chkMainAccount.change(function() { thisObj.updateMainAccount();});

            if (thisObj.useStorage){
                
            }else{
                if(opt.showItemImage){ 
                    if(id){       
                        if($("."+imageUploaderTarget).length > 0) createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":imageFolder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false); 
                        if($("."+fileUploaderTarget).length > 0) createFileUploader(fileUploaderTarget,fileFolder, id , arrFile,true);  
                    }else{ 
                        if($("."+imageUploaderTarget).length > 0) createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":imageFolder} ,false);  
                        if($("."+fileUploaderTarget).length > 0)  createFileUploader(fileUploaderTarget,fileFolder, "" , "",true); 
                    }

                    tabObj.find(".file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(fileUploaderTarget); }});
                    tabObj.find(".file-list"  ).disableSelection(); 

                } 
            }
			
             
         if ( typeof data !== 'undefined' && data['detailAccount'].length == 0)
                addNewTemplateRow("account-row-template",null,null,thisObj.rebindEl);    
  
		if (!data['customerPersonInCharge'] || data['customerPersonInCharge'].length == 0)
               addNewTemplateRow("pic-row-template",null,null,thisObj.rebindEl);
            
			tabObj.find("[name=selCategory]").change(function () {thisObj.updateCategory()});
	        tabObj.find("[name=selCategory]").change();  

			var isInsured = tabObj.find("[name=isInsured]");
			if(isInsured){ 
				tabObj.find("[name=isInsured]").change(function () { thisObj.updateInsuredCompany() });
				tabObj.find("[name=isInsured]").change();  
			}
			
			tabObj.find("[name=chkICA]").change(function(){
			   var isChk = $(this).val(); 
				if(isChk == 1)
			   		tabObj.find(".ica-group").show();
				else
					tabObj.find(".ica-group").hide();
			});
			tabObj.find("[name=chkICA]").change();
            
            tabObj.find("[name=selTinType]").change(function(){
			   var value = $(this).val(); 
			
               tabObj.find(".tin").hide();
               tabObj.find(".tin"+value).show();
                
			});
			tabObj.find("[name=selTinType]").change();
            
			
            thisObj.rebindEl(); 
        }
        
}
