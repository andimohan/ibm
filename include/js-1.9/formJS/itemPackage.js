function ItemPackage(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
      
        
		var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue;  
 
        this.tabID = tabID;    
 
        this.updateCommissionDecimal = function updateCommissionDecimal(obj){  
            var parentObj =  $(obj).closest(".div-table-row"); 
            
            var objDiscVal = parentObj.find("[name='commissionValue']");
            var discType = $(obj).val();

            objDiscVal.removeClass("inputnumber").addClass("inputdecimal");

            if (discType == 1){
                objDiscVal.unbind("blur").bind( "blur", function(event) {  inputNumberOnBlur($(this)) });
            }else{
                objDiscVal.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this),2)}); 
            } 

           objDiscVal.blur(); 

       } 
              
          this.updateDetail = function updateDetail(target,objAndValue,ui){
             
                var detailRow = $(target).closest(".transaction-detail-row");
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();

                for(i=0;i<objAndValue.length;i++){   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
 
              
                //updateAvailableUnit(itemKeyObj, selUnitObj);
               // update combobox services
                var newOptions = {}; 
                newOptions[ui.item['baseunitkey']] =  ui.item['baseunitname'];       

                var options = (selUnitObj.prop) ? selUnitObj.prop('options') : selUnitObj.attr('options');  

                $('option', selUnitObj).remove();

                $.each(newOptions, function(val, text) {
                    options[options.length] = new Option(text, val);
                });

                //selUnitObj.find('option:eq(0)').prop('selected', true).change();
                //selUnitObj.val(data[0]['deftransunitkey']);
 
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);
                detailRow.find(".baseitemunit").html(ui.item['baseunitname']);   

                thisObj.calculateDetail(itemKeyObj);
         }
                 
          
	    this.calculateDetail = function calculateDetail(obj){   
     
            var row =  $(obj).closest(".transaction-detail-row");  
            var itemkey =  row.find("[name='hidItemKey[]']").val(); 

            var qty =  unformatCurrency(row.find("[name='qty[]']").val());
            var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
            var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
            var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
            var unitkey =  row.find("[name='selUnit[]']").val(); 

            if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit; 

            var subtotal = qty  *  (priceInUnit - discount);
            row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 

            thisObj.calculateTotal();
        }
	
	   this.calculateTotal = function calculateTotal(){  
         
                    var subtotal = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  }) 
                    tabObj.find("[name='subtotal']").val(subtotal).blur();

                    var finalDiscount = parseFloat(unformatCurrency($("#" + tabID + " [name='finalDiscount']").val())) || 0 ;
                    var finalDiscountType = parseInt(unformatCurrency($("#" + tabID + " [name='selFinalDiscountType']").val())) || 0 ;
           
                    if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 

                    subtotal -= finalDiscount; 
 
                    var total = subtotal ;
                    tabObj.find("[name='total']").val(total).blur();
 
	       }
       
       

        this.rebindEl = function rebindEl(){ 
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail); 

            bindEl(tabObj.find("[name='qty[]'],  [name='selUnit[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]']"), 'change',  function(){ thisObj.calculateDetail(this) });
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });  
        }

        this.loadOnReady = function loadOnReady(){   
            tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)})  
            thisObj.rebindEl();  
            thisObj.calculateTotal();
        }
        
}
