function Supplier(tabID, data){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    	
    	
        this.tabID = tabID;    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidServiceKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'});   
        var objAndValueForDetailContainerAutoComplete = objAndValue;
	
		var objAndValue = new Array;
		objAndValue.push({object:'hidLocationDetailKey[]', value :'pkey'});   
        var objAndValueForDetailLocationAutoComplete = objAndValue;
 
        this.rebindEl = function rebindEl(){   
        	bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailContainerAutoComplete,'ajax-container.php?action=searchData');   
        	bindAutoCompleteForTransactionDetail('locationDetailName[]',objAndValueForDetailLocationAutoComplete,'ajax-location.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=3'); 
 
        } 
         
        this.loadOnReady = function loadOnReady(){ 
		 
			// BCL gk ad data
			if(data != undefined){
				if(data['rsSupplierBankDetail'] != undefined){
					if (!data['rsSupplierBankDetail'] || data['rsSupplierBankDetail'].length  < 1)
						addNewTemplateRow("supplier-detail-bank-row-template",null,null,thisObj.rebindEl);

				} 
			}
			            
						
			tabObj.find("[name=chkICA]").change(function(){
			   var isChk = $(this).val(); 
				if(isChk == 1)
			   		tabObj.find(".ica-group").show();
				else
					tabObj.find(".ica-group").hide();
			});
			tabObj.find("[name=chkICA]").change();
			
            thisObj.rebindEl(); 

        }
        
}
