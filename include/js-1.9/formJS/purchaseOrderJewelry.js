function PurchaseOrderJewelry(tabID, cashTOP,  varConstant){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID;    
        this.tablekey = varConstant.TABLEKEY;     
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
            
            var detailRow = $(target).closest(".transaction-detail-row"); 
            
            // var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
            // var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();
            
            //     for(i=0;i<objAndValue.length;i++)   
            //         detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
               
            //     updateAvailableUnit(itemKeyObj, selUnitObj);
            //     thisObj.updatePrice(itemKeyObj);
    
            //     // harus handle manual utk obj autosearch
            //     detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
            thisObj.updateRowInformation(detailRow, objAndValue, ui);
            //thisObj.updateItemDetail(detailRow);

            
        }  
        
        this.updateRowInformation = function updateRowInformation(detailRow, objAndValue, ui) { 
            
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
            var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();
            
                for(i=0;i<objAndValue.length;i++)   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
               
                updateAvailableUnit(itemKeyObj, selUnitObj);
                thisObj.updatePrice(itemKeyObj);
    
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);  
        }
            
        this.calculateDetail = function calculateDetail(obj){   
     
                var row =  $(obj).closest(".transaction-detail-row");  
                var itemkey =  row.find("[name='hidItemKey[]']").val(); 

                var qty =  parseFloat(unformatCurrency(row.find("[name='qty[]']").val())) || 0;
                var priceInUnit =  parseFloat(unformatCurrency(row.find("[name='priceInUnit[]']").val())) || 0;
                var discount =  parseFloat(unformatCurrency(row.find("[name='discountValueInUnit[]']").val())) || 0;
                var discountType =  parseFloat(unformatCurrency(row.find("[name='selDiscountType[]']").val())) || 0;
                var unitkey =  row.find("[name='selUnit[]']").val(); 
                var priceInPcs = parseFloat(unformatCurrency(row.find("[name='priceInPcs[]']").val())) || 0;
                var qtyInPcs =  parseFloat(unformatCurrency(row.find("[name='qtyInPcs[]']").val())) || 0;
               
                var isPriceInPcs = row.find("[name='chkPriceInPcs[]']").val(); 


                var subtotal = 0;

                if(isPriceInPcs == 1) {
                    var priceInBaseUnit = 0;
                    
                    if (qty > 0) {
                        priceInBaseUnit = (qtyInPcs * priceInPcs) / qty;
                    } 

                    row.find("[name='priceInUnit[]']").val(priceInBaseUnit).blur(); 
                    
                    if (discount != 0 && discountType == 2) discount = discount / 100 * priceInPcs; 
                    subtotal = qtyInPcs  *  (priceInPcs - discount);
   
                } else {
                    var priceInPcsVal = 0; 
                    
                    if(qtyInPcs > 0) {
                        priceInPcsVal = (qty * priceInUnit) / qtyInPcs;
                    }
                    
                    row.find("[name='priceInPcs[]']").val(priceInPcsVal).blur(); 
                    
                    if (discount != 0 && discountType == 2) discount = discount / 100 * priceInUnit; 
                    subtotal = qty  *  (priceInUnit - discount);
                }

                //if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit; 
                //var subtotal = qty  *  (priceInUnit - discount);

            row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 

            thisObj.calculateTotal();
        }


        this.calculateTotal = function calculateTotal(){    
            var subtotal = 0; 


            tabObj.find("[name='detailSubtotal[]']").each(function () { subtotal += parseInt(unformatCurrency($(this).val())) || 0; })


            tabObj.find("[name='subtotal']").val(subtotal).blur();

            var finalDiscount = parseFloat(unformatCurrency( tabObj.find("[name='finalDiscount']").val())) || 0 ;
            var finalDiscountType = parseInt(unformatCurrency( tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
            var shipmentFee = parseFloat(unformatCurrency( tabObj.find("[name='shipmentFee']").val())) || 0 ; 
            var etcCost = parseFloat(unformatCurrency( tabObj.find("[name='etcCost']").val())) || 0 ; 
            var includeTax =    tabObj.find("[name='chkIncludeTax']").val();
            var taxPercentage =  parseFloat(unformatCurrency( tabObj.find("[name='taxPercentage']").val())) || 0 ;  
         
            var finalDiscount2 = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount2']").val())) || 0;
            var finalDiscountType2 = parseInt(tabObj.find("[name='selFinalDiscount2Type']").val()) || 0 ;

            if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 

            subtotal -= finalDiscount;   
            tabObj.find("[name='afterFirstDiscount']").val(subtotal).blur();

            if (finalDiscount2 != 0 && finalDiscountType2 == 2)  
                finalDiscount2 = finalDiscount2/100 * subtotal;  

            subtotal -= finalDiscount2;

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
             tabObj.find("[name='total']").val(total).blur();

/*            var totalPayment = 0; 
             tabObj.find("[name='paymentMethodValue[]']").each(function() {   
                totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
            }) */

            var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));
            
            var balance = totalPayment - total;
             tabObj.find("[name='balance']").val(balance).blur();

            thisObj.calculateTotalQty();
        } 

        this.calculateTotalQty =  function calculateTotalQty() {

            var totalQty = 0;
            var totalQtyInPcs = 0;

            tabObj.find("[name='qty[]']").each(function () { totalQty += parseInt(unformatCurrency($(this).val())) || 0; })
            tabObj.find("[name='qtyInPcs[]']").each(function () { totalQtyInPcs += parseInt(unformatCurrency($(this).val())) || 0; })
        

            tabObj.find("[name='totalQty']").val(totalQty).blur();
            tabObj.find("[name='totalQtyInPcs']").val(totalQtyInPcs).blur();
        }
 
         this.updatePrice = function updatePrice(obj){
             
                var row =  $(obj).closest(".transaction-detail-row");  
                var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val(); 
                var itemkey =  row.find("[name='hidItemKey[]']").val(); 

                   $.ajax({
                        type: "GET",
                        url:  'ajax-purchase-order.php',
                        async : false,
                        data: "action=getPriceDetail&itemkey=" + itemkey +"&supplierkey=" + supplierkey,  
                    }).done(function( data ) { 
                            data = parseJSON(data) ; 
                            data = data[0];
                            var price = (data) ? data.priceinunit : 0;
                            row.find("[name='priceInUnit[]']").val(price).change();
                            tabObj.find(".inputnumber").blur();  
                    }); 

        }

         this.updateTOP = function updateTOP(){
          
                    var selTermOfPaymentKey = tabObj.find("[name=selTermOfPaymentKey]" ).val();   
                    var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val(); 

                       $.ajax({
                            type: "GET",
                            url:  'ajax-supplier.php',
                            data: "action=getDataRowById&pkey=" + supplierkey ,  
                        }).done(function( data ) {
                                
                               if(!data) return;
                           
                                data = JSON.parse(data) ; 
                                data = data[0];
                            
                                if (firstOpened == true){
                                    firstOpened = !firstOpened;
                                    thisObj.updateSupplierInformation(data.termofpaymentkey);
                                }else if (selTermOfPaymentKey != data.termofpaymentkey ){

                                        $( "#dialog-message" ).html("Apakah Anda ingin mengganti data pembayaran dengan data default untuk pemasok ini ?");
                                        $( "#dialog-message" ).dialog({
                                          width: 300,
                                          modal: true,
                                          title:"Konfirmasi Perubahan Data Pembayaran", 
                                          open: function() {
                                              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                                          }, 
                                          buttons : {
                                              OK : function (){    
                                                    thisObj.updateSupplierInformation(data.termofpaymentkey);
                                                   $( this ).dialog( "close" );
                                              },
                                              Cancel : function (){  
                                                    $( this ).dialog( "close" );
                                              }
                                          } 

                                        });	    
                                } 

                        }); 

        }
         
        this.updateSupplierInformation =  function updateSupplierInformation (topkey){
            if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + topkey + "']").length > 0)
                tabObj.find("[name=selTermOfPaymentKey]").val(topkey).change();  
        }

        this.updateAutoNumberRow = function updateAutoNumberRow() {
            var number = 1;

            tabObj.find(".transaction-detail-row").each(function() {
                var input = $(this).find("input[name='numberDetail[]']");
                if (!input.length) return;

                input.each(function() {
                    $(this).val(number).trigger("change");
                });

                number++;
            });
        }

        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal(); ;   
        }
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
            thisObj.updateAutoNumberRow();
        }

        this.onChangePriceInPcs = function onChangePriceInPcs(obj) {
            
            var row = $(obj).closest(".transaction-detail-row"); 
            var priceInPcs = row.find("[name='chkPriceInPcs[]']").val();

            if (priceInPcs == 0) {
                row.find("[name='priceInUnit[]']").prop("readonly", false); 
                row.find("[name='priceInPcs[]']").prop("readonly", true); 
            } else {
                row.find("[name='priceInUnit[]']").prop("readonly", true);  
                row.find("[name='priceInPcs[]']").prop("readonly", false);
            }

        }

        this.rebindEl = function rebindEl(){  

            var handling = [];
            handling.onSelectFunction = 'getTabObj().updateDetail'; 
          
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]'],  [name='selUnit[]'], [name='chkPriceInPcs[]'], [name='qtyInPcs[]'], [name='priceInPcs[]']" ), 'change',  function(){ thisObj.calculateDetail(this); });  
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });  
            bindEl(tabObj.find("[name='chkPriceInPcs[]']"), 'change', function () { thisObj.onChangePriceInPcs($(this)) });          
        
            thisObj.updateAutoNumberRow(); 
        }
        
        this.loadOnReady = function loadOnReady(){

            tabObj.find("[name=selTermOfPaymentKey]" ).change(function() {
        
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

                tabObj.find(".cashTOP").show();
            });   
                
            tabObj.find("[name=selTermOfPaymentKey]" ).change();   
        
            //tabObj.find(" [name=chkIsFullReceive]" ).change(); 
            tabObj.find(".form-detail-field").toggle(); 

            tabObj.find(".form-detail-button").click(function() {   
                 tabObj.find(".form-detail-field").toggle( "highlight" );
                var temp =  tabObj.find(".form-detail-button").attr("relalt");   
                $("#" + tabID+ " .form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
                 tabObj.find(".form-detail-button").text(temp); 
            });
             
            tabObj.find("[name=selFinalDiscountType], [name=finalDiscount],[name=selFinalDiscount2Type], [name=finalDiscount2], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal(this)}) 
            tabObj.find("[name=selFinalDiscountType],[name=selFinalDiscount2Type]").change(function(){updateFinalDiscountDecimal(this)}); 
            
            // var rowTemplate = $(this).closest('.detail-row-template');
            // rowTemplate.find("[name='chkPriceInPcs[]']").val(0);
            // rowTemplate.find("[name='dummychkPriceInPcs[]']").val(0);
            // rowTemplate.find("[name='dummychkPriceInPcs[]']").removeAttr("checked");

            thisObj.calculateTotalQty();

            thisObj.rebindEl(); 
  
        }
}
