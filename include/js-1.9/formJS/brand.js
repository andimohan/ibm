function Brand(tabID, uploadFolder){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      

    var objAndValue = new Array;
    objAndValue.push({object:'hidMarketplaceBrandKey[]', value :'marketplacebrandkey'});    
    var objAndValueForDetailAutoComplete = objAndValue;

    this.tabID = tabID;    
 
    var id = tabObj.find("[name=hidId]").val(); 
    var marketplaceKey = '';

    this.rebindEl = function rebindEl(){   
         bindAutoCompleteForTransactionDetail('marketplaceBrandName[]',objAndValueForDetailAutoComplete,'ajax-marketplace.php?action=getMarketplaceBrand&marketplaceKey=' + marketplaceKey );
    }

    this.loadOnReady = function loadOnReady(){ 

         
            for($ctr=0;$ctr<uploadFolder.length;$ctr++){

                var folder = uploadFolder[$ctr]['folder'];
                var imageUploaderTarget = uploadFolder[$ctr]['imageUploaderTarget'];
                var rsImage = uploadFolder[$ctr]['rsImage'];
                var arrImage = Array(); 
                var arrPHPThumbHash = Array();

                if(id){   
                    for($i=0;$i<rsImage.length;$i++) {
                        arrImage.push(rsImage[$i].file);
                        arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
                    } 
                    createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);

                }else{ 
                     createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,false); 
                }
            }
            

        tabObj.find("[name='marketplaceBrandName[]']").focus(function() {
          marketplaceKey = $(this).closest(".marketplace-brand-row").find("[name='hidMarketplaceKey[]']").val();
          thisObj.rebindEl();
        });

        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}