function Campaign(tabID,arrVoucher){  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID; 
     
        var objAndValue = new Array;
		objAndValue.push({object:'hidCategoryKey[]', value :'pkey'});   
        var objAndValueForDetailCategoryAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidBrandKey[]', value :'pkey'});   
        var objAndValueForDetailBrandAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
        var objAndValueForDetailItemAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidMarketplaceKey[]', value :'pkey'});   
        var objAndValueForDetailMarketplaceAutoComplete = objAndValue;
    
        function toggleCriteriaDetail(obj){
            
            var showChk = obj.val();
            row = obj.closest(".div-table").find(".transaction-detail-row");
            
            if (showChk == 1)
                row.hide();
            else
                row.show();
        }
  
        this.rebindEl = function rebindEl(){   
            
     	    bindAutoCompleteForTransactionDetail('categoryName[]',objAndValueForDetailCategoryAutoComplete,'ajax-item-category.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('brandName[]',objAndValueForDetailBrandAutoComplete,'ajax-brand.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailItemAutoComplete,'ajax-item.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('marketplaceName[]',objAndValueForDetailMarketplaceAutoComplete,'ajax-marketplace.php?action=searchData');   
                   
        } 
        
        this.loadOnReady = function loadOnReady(){  
            tabObj.find("[name='selDiscountType']").change(function(){updateFinalDiscountDecimal(this, tabObj.find("[name=value]"))}); 
  
            if(arrVoucher.rsItemCategoryDetail.length == 0) 
                 addNewTemplateRow("category-row-template",null,tabObj.find('.item-category-criteria'),thisObj.rebindEl); 
            
            if(arrVoucher.rsBrandDetail.length == 0) 
                 addNewTemplateRow("brand-row-template",null,tabObj.find('.brand-criteria'),thisObj.rebindEl); 
            
            if(arrVoucher.rsItemDetail.length == 0) 
                 addNewTemplateRow("item-row-template",null,tabObj.find('.item-criteria'),thisObj.rebindEl); 
   
            if(arrVoucher.rsMarketplaceDetail.length == 0) 
                 addNewTemplateRow("marketplace-row-template",null,tabObj.find('.marketplace-criteria'),thisObj.rebindEl); 
   
            tabObj.find("[name=chkAllItem], [name=chkAllItemCategory], [name=chkAllBrand], [name=chkAllMarketplace]").change(function(){toggleCriteriaDetail($(this))});   
            
            tabObj.find("[name=chkAllItem], [name=chkAllItemCategory], [name=chkAllBrand], [name=chkAllMarketplace]").change();
            thisObj.rebindEl();  
        }
        
}
