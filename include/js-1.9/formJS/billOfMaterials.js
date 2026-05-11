 function BillOfMaterials(tabID,varConstant) {
        var thisObj = this;
		var tabObj = $("#" + tabID);

        this.tablekey = varConstant.TABLEKEY;  
     
     
		this.tabID = tabID;	
	         var id = tabObj.find("[name=hidId]").val();  

		var objAndValue = new Array;
		objAndValue.push({object:'hidItemKeyDetail[]', value :'pkey'});   
		var objAndValueForDetailAutoComplete = objAndValue;
	 
		this.updateDetail = function updateDetail(target,objAndValue,ui){ 

             var detaiLRow = $(target).closest(".transaction-detail-row");

             for(i=0;i<objAndValue.length;i++){   
                   detaiLRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
             } 

             // harus handle manual utk obj autosearch
             if (ui.item['value'] != ''){  
                 var itemNameObj = detaiLRow.find("[name=\"itemNameDetail[]\"]").first();
                 itemNameObj.val(ui.item['value']); 
                 detaiLRow.find(".baseitemunit").html(ui.item['baseunitname']);    
             } 
         }
		   
		this.rebindEl = function rebindEl() { 
            bindAutoCompleteForTransactionDetail('itemNameDetail[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',thisObj.updateDetail);
		}
		
		this.loadOnReady = function loadOnReady() { 
 			
//			if(!id){
//				addNewTemplateRow("detail-row-template");
//			}
			
			
        	thisObj.rebindEl();

    	}
		 
    } 
