function Voucher(tabID,arrVoucher){  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID; 
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidCityKey[]', value :'pkey'});   
        var objAndValueForDetailCityAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidCityCategoryKey[]', value :'pkey'});   
        var objAndValueForDetailCityCategoryAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidCategoryKey[]', value :'pkey'});   
        var objAndValueForDetailCategoryAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidBrandKey[]', value :'pkey'});   
        var objAndValueForDetailBrandAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
        var objAndValueForDetailItemAutoComplete = objAndValue;
    
    
            /*this.calculateDiscount = function calculateDiscount(){

                    var maxDiscount =  parseInt(unformatCurrency(tabObj.find("[name='maxDiscount']").val()));
                    var discountType =  unformatCurrency(tabObj.find("[name='selDiscountType']").val());
                        
                    maxdiscount = 0;
                    if (maxDiscount != 0 && discountType == 2){
                        maxdiscount = maxDiscount/100;
                    }else{
                        maxdiscount = maxDiscount;
                    }
            
            
                    tabObj.find("[name='maxDiscount']").val(maxdiscount).blur();
            
	       }*/
    
        this.onChangeVoucherCategory = function onChangeVoucherCategory(){
            
            // gk bisa, karena bisa saja free ongkir utk produk / brand tertentu
            
           /* var categorykey = tabObj.find("[name=selCategory]").val();
             
            if(categorykey == 3){
                tabObj.find(".city-category-criteria").show();
                tabObj.find(".brand-criteria, .item-criteria, .item-category-criteria ").hide();
            }else{ 
                tabObj.find(".city-category-criteria").hide();
                tabObj.find(".brand-criteria, .item-criteria, .item-category-criteria ").show();
            }
            */
        }
        
        
        this.rebindEl = function rebindEl(){   
            
     	     bindAutoCompleteForTransactionDetail('cityName[]',objAndValueForDetailCityAutoComplete,'ajax-city.php?action=searchData');   
     	     bindAutoCompleteForTransactionDetail('cityCategoryName[]',objAndValueForDetailCityCategoryAutoComplete,'ajax-city-category.php?action=searchData');   
             bindAutoCompleteForTransactionDetail('categoryName[]',objAndValueForDetailCategoryAutoComplete,'ajax-item-category.php?action=searchData');   
             bindAutoCompleteForTransactionDetail('brandName[]',objAndValueForDetailBrandAutoComplete,'ajax-brand.php?action=searchData');   
             bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailItemAutoComplete,'ajax-item.php?action=searchData');   
                  
        } 
        
        this.loadOnReady = function loadOnReady(){  
            tabObj.find("[name='selDiscountType']").change(function(){updateFinalDiscountDecimal(this, tabObj.find("[name=value]"))}); 
  
            if(arrVoucher.rsCityDetail && arrVoucher.rsCityDetail.length == 0) 
                 addNewTemplateRow("city-row-template",null,tabObj.find('.city-criteria'),thisObj.rebindEl); 
            
            if(arrVoucher.rsCityCategoryDetail && arrVoucher.rsCityCategoryDetail.length == 0) 
                 addNewTemplateRow("city-category-row-template",null,tabObj.find('.city-category-criteria'),thisObj.rebindEl); 
            
            if(arrVoucher.rsItemCategoryDetail && arrVoucher.rsItemCategoryDetail.length == 0) 
                 addNewTemplateRow("category-row-template",null,tabObj.find('.item-category-criteria'),thisObj.rebindEl); 
            
            if(arrVoucher.rsBrandDetail && arrVoucher.rsBrandDetail.length == 0) 
                 addNewTemplateRow("brand-row-template",null,tabObj.find('.brand-criteria'),thisObj.rebindEl); 
            
            if(arrVoucher.rsItemDetail && arrVoucher.rsItemDetail.length == 0) 
                 addNewTemplateRow("item-row-template",null,tabObj.find('.item-criteria'),thisObj.rebindEl); 
  
            bindEl(tabObj.find("[name=selCategory]"),'change', function() { thisObj.onChangeVoucherCategory(); });      
            
            thisObj.onChangeVoucherCategory();
            thisObj.rebindEl();  
        }
        
}
