function PurchaseRequest(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
		var objAndValue = new Array;  
        objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue;  
        
        var firstOpened = true;
    
        this.tabID = tabID;    
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();
            
                for(i=0;i<objAndValue.length;i++)   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
               
                updateAvailableUnit(itemKeyObj, selUnitObj);

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);  
        }  
        
        this.calculateDetail = function calculateDetail(obj){   
     
            var row =  $(obj).closest(".transaction-detail-row");  
            var itemkey =  row.find("[name='hidItemKey[]']").val(); 

            var qty =  unformatCurrency(row.find("[name='qty[]']").val());
            var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
            var unitkey =  row.find("[name='selUnit[]']").val(); 


            var subtotal = qty  * priceInUnit;
            row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 

        }


        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='selUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) });         
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });  
        
        }
        
        this.loadOnReady = function loadOnReady(){
                   
            thisObj.rebindEl(); 
  
        }
}
