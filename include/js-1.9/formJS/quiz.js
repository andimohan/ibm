function Quiz(tabID, uploadFolder, rsImage){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID;    
        
        var folder = uploadFolder;
 	    var imageUploaderTarget = "item-image-uploader"; 
		var arrImage = Array();
		var arrPHPThumbHash = Array();
    
        var id = tabObj.find("[name=hidId]").val();  

       /* this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(){
            var detailObj = tabObj.find(".transaction-detail-row").last().find(".detail-col-detail");
            console.log(detailObj.length);
           // thisObj.updateFieldOnChangeInvoiveDownpayment();
        } */
        
        this.rebindEl = function rebindEl(){  } 
        
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
            
            
        questionRows = tabObj.find(".question-row");
        questionRows.each(function() {  
              var itemrow = $(this).find(".detail-item .transaction-detail-row");  
              if(itemrow.length == 0)  addNewTemplateRow('item-row-template',null,$(this),thisObj.rebindEl);  
        });  
            
            
        descRows = tabObj.find(".desc-row");
        descRows.each(function() {  
              var itemrow = $(this).find(".transaction-detail-row");   
              if(itemrow.length == 0)  addNewTemplateRow('desc-row-template',null,$(this),thisObj.rebindEl);  
        });  
            
        tabObj.find("[name=btnAddDesc]").on('click', function() { addNewTemplateRow("desc-row-template",null,null,thisObj.rebindEl); });


         thisObj.rebindEl();
        
        }
        
}
