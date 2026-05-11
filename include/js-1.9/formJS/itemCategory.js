function ItemCategory(tabID, uploadFolder){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
      
    	var objAndValue = new Array;
		objAndValue.push({object:'hidMarketplaceCategoryKey[]', value :'pkey'});    
        var objAndValueForDetailAutoComplete = objAndValue;
     
     	var objAndValue = new Array;
		objAndValue.push({object:'hidStorefrontKey[]', value :'pkey'});    
        var objAndValueForStorefrontDetailAutoComplete = objAndValue;
     
        this.tabID = tabID;    
  
        var id = tabObj.find("[name=hidId]").val(); 
        var marketplaceKey = '';
      
        this.rebindEl = function rebindEl(){
			 // tetep kirim marketplacekey gpp, nanti di class akan otoamtis diambil dr providernya 
             bindAutoCompleteForTransactionDetail('marketplaceCategoryName[]',objAndValueForDetailAutoComplete,'ajax-marketplace.php?action=getMarketplaceCategory&marketplaceKey=' + marketplaceKey);
             bindAutoCompleteForTransactionDetail('marketplaceStorefrontName[]',objAndValueForStorefrontDetailAutoComplete,'ajax-marketplace.php?action=getMarketplaceStorefront&marketplaceKey=' + marketplaceKey);
        }
          
        this.loadOnReady = function loadOnReady(){ 
            
            for($ctr=0;$ctr<uploadFolder.length;$ctr++){

                var folder = uploadFolder[$ctr]['folder'];
                var imageUploaderTarget = uploadFolder[$ctr]['imageUploaderTarget'];
                var rsImage = uploadFolder[$ctr]['rsImage'];
                var type  = uploadFolder[$ctr]['type'] ?? 'image';
                var arrImage = Array(); 
                var arrPHPThumbHash = Array();

                if(id){   
                    for($i=0;$i<rsImage.length;$i++) {
                        arrImage.push(rsImage[$i].file);
                        arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
                    } 
                   
                    if(type == 'file')
                     createFileUploader(imageUploaderTarget,folder, id ,arrImage,false);   
                    else         
                      createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);
              
                  
                }else{ 
                  if(type == 'file')
                     createFileUploader(imageUploaderTarget, folder, "", "", false);
                  else
                     createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,false); 
                    
                }
            }
            
            
            
            tabObj.find("[name='marketplaceCategoryName[]']").focus(function() {
              marketplaceKey = $(this).closest(".marketplace-category-row").find("[name='hidMarketplaceKey[]']").val();
              thisObj.rebindEl();
            });
            
            tabObj.find("[name='marketplaceStorefrontName[]']").focus(function() {
              marketplaceKey = $(this).closest(".marketplace-storefront-row").find("[name='hidStoreFrontMarketplaceKey[]']").val(); 
              thisObj.rebindEl();
            });
            
            multiLang(tabObj); 
            thisObj.rebindEl();
        }
}