function SalesOrderRecurringSubscription(tabID, rs, cashTOP,varConstant)
{

   var thisObj = this;
   var tabObj = $("#" + tabID);

   this.tabObj = tabObj;
   this.tablekey = varConstant.TABLEKEY;  
   this.customCodeCache=[];

   var objAndValue = new Array;  
	objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
	objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
	objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
   var objAndValueForDetailAutoComplete = objAndValue; 
 

	this.tabID = tabID;    

	this.rs = (rs.length > 0) ? rs[0] : null;
 
   this.updateDetail = function updateDetail(target,objAndValue,ui){

      var detailRow = $(target).closest(".transaction-detail-row"); 
      var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
      var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first(); 
              
      for(i=0;i<objAndValue.length;i++){    
          //overwrite kalo kg
          if(objAndValue[i].object == 'hidGramasi[]' && ui.item['weightunitkey'] == 2)
              ui.item[objAndValue[i].value] *= 1000;  
          detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
      } 
            
                        

   updateAvailableUnit(itemKeyObj, selUnitObj);
      thisObj.updateUnitPrice(selUnitObj);
            
      // harus handle manual utk obj autosearch
      detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
   
      thisObj.calculateDetail(itemKeyObj);

   }
 

   this.calculateDetail = function calculateDetail(obj) {      
      
      var row =  $(obj).closest(".transaction-detail-row");   
      var itemkey =  row.find("[name='hidItemKey[]']").val();
                
      var qty =  unformatCurrency(row.find("[name='qty[]']").val());
      var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
      var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
      var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
            
      var selUnitObj = row.find("[name='selUnit[]']");
      var unitkey =  selUnitObj.val();
      var conversionmultiplier =  parseFloat(selUnitObj.find("option:selected").attr('relconversionmultiplier'));
            
      var gramasi =  parseFloat(row.find("[name='hidGramasi[]']").val());
               
         if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit;

      var subtotal = qty  *  (priceInUnit - discount);
      row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 
      row.find("[name='hidGramasiSubtotal[]']").val(gramasi * qty * conversionmultiplier).blur(); 

      thisObj.calculateTotal();
	}

   this.calculateTotal = function calculateTotal(){  
 
		
            var subtotal = 0; 
            tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
            tabObj.find("[name='subtotal']").val(subtotal).blur();
			
	 
		
         var totalGramasi = 0; 
         tabObj.find("[name='hidGramasiSubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
         tabObj.find(".total-weight").html(Math.ceil(totalGramasi/1000));
         

			var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0 ;
			var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
			var finalDiscount2 = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount2']").val())) || 0 ;
			var finalDiscountType2 = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType2']").val())) || 0 ;
			var shipmentFee = parseInt(unformatCurrency(tabObj.find("[name='shipmentFee']").val())) || 0 ; 
			var etcCost = parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0 ; 
			var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
			var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ; 
   
         
         if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 

					// level 2
			if(finalDiscount2 > 0){
				var subtotal2 =  subtotal - finalDiscount; 
				if (finalDiscount2 != 0 && finalDiscountType2 == 2)  finalDiscount2 = finalDiscount2/100 * subtotal2; 
				finalDiscount += finalDiscount2;						
			}


         subtotal -= finalDiscount;   

         tabObj.find("[name='beforeTaxTotal']").val(subtotal).blur();

         var taxValue = 0;
         if (includeTax == 0) {
               taxValue = subtotal * taxPercentage / 100;
               subtotal += taxValue;
         }else{
               taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
               tabObj.find("[name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
         }

         tabObj.find("[name='taxValue']").val(taxValue).blur(); 

         var total = subtotal +  shipmentFee + etcCost;
         tabObj.find("[name='grandTotal']").val(total).blur();
                    
         var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

         var balance = totalPayment - total; 
		   tabObj.find("[name='balance']").val(balance).blur();
		
	}
 


   this.updateUnitPrice = function updateUnitPrice(obj){ 
            var row = $(obj).closest(".transaction-detail-row");
            var unitKey = $(obj).val();
            var itemKey = row.find("[name='\hidItemKey[]\']").val();
            var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;
            
            $.ajax({
                type: "GET",
                url:  'ajax-item.php',
                async: false,
                data: "action=getUnitSellingPrice&itemkey="+itemKey+"&unitkey=" + unitKey +"&lastsellingprice=1&customerkey="+customerkey,  
            }).done(function( data ) {  
                   data = JSON.parse(data) ; 
                   row.find("[name=\'priceInUnit[]\']").val(data).blur();
            });
   }

   this.updateSalesman = function updateSalesman(){

            var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  
            
            //update salesman
            tabObj.find("[name=hidSalesKey]").val("");  
            tabObj.find("[name=salesName]").val("");  

            $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async: false,
                data: "action=getSalesman&pkey=" + customerkey ,  
            }).done(function( data ) {  
                if (!data ) return;

                data = JSON.parse(data) ;  
                if ( data.length  == 0  ) return;

                tabObj.find("[name=hidSalesKey]").val(data.pkey);  
                tabObj.find("[name=salesName]").val(data.name);    

            }); 
 
        }



             
   this.afterRemoveRowHandler = function afterRemoveRowHandler(){
      thisObj.calculateTotal(); 
   }
   
   
  this.rebindEl = function rebindEl(){   
      
      bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&limit=25',thisObj.updateDetail);
      bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
      bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });  
      bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); thisObj.calculateDetail(this); }); 
			 
   }
   
	  
   this.loadOnReady = function loadOnReady(){
           


		tabObj.find(".form-detail-field").toggle(); 

		tabObj.find(".form-detail-button").click(function() {   
			 tabObj.find(".form-detail-field").toggle( "highlight" );
			 var temp =  tabObj.find(".form-detail-button").attr("relalt");   
			 tabObj.find(".form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
			 tabObj.find(".form-detail-button").text(temp); 
		}); 

      thisObj.rebindEl(); 
   
   } 

}