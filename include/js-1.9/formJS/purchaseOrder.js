function PurchaseOrder(tabID, cashTOP, tablekey, varConstant){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
		var objAndValue = new Array;  
        objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        objAndValue.push({object:'selUnit[]', value :'deftransunitkey'});
        objAndValue.push({object:'chkWeightFixed[]', value :'isweightfixed'});
        objAndValue.push({object:'gramasi[]', value :'gramasi'});
    objAndValue.push({
        object: 'hidNeedSN[]',
        value: 'needsn'
    });

        var objAndValueForDetailAutoComplete = objAndValue;  
        

         var objAndValue = new Array;  
        objAndValue.push({object:'hidBrandDetailKey[]', value :'pkey'}); 
        var objAndValueForDetailBrandAutoComplete = objAndValue; 

        var objAndValue = new Array;  
        objAndValue.push({object:'hidTypeDetailKey[]', value :'pkey'}); 
        var objAndValueForDetailTypeAutoComplete = objAndValue;  

        var objAndValue = new Array;  
        objAndValue.push({object:'hidCategoryDetailKey[]', value :'pkey'}); 
        var objAndValueForDetailCategoryAutoComplete = objAndValue;  
        var firstOpened = true;
    
        this.tabID = tabID;    
        this.tablekey = tablekey;     
    this.snRegex = varConstant.SN_REGEX;
        this.isActiveModulePurchasePrice = varConstant.isActiceModulePurchasePrice;
       
        this.updateSupplier = function updateSupplier(){ 
            var purchasekey = tabObj.find("[name=hidPurchaseRequestKey]" ).val(); 
                
            $.ajax({
                type: "GET",
                url:  'ajax-purchase-request.php',
                data: "action=getDataRowById&pkey=" + purchasekey ,  
            }).done(function( data ) { 
                
                    if (!data) return;
                
                    data = JSON.parse(data); 
                    data = data[0];
                    tabObj.find("[name=hidSupplierKey]" ).val(data.supplierkey); 
                    tabObj.find("[name=supplierName]" ).val(data.suppliername); 

            }); 

        }
      
        this.importData =  function importData(){  
            
                thisObj.updateSupplier();
            
                clearAllRows(tabObj.find(".mnv-transaction"));

                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;

                $.ajax({
                    type: "GET",
                    url:  'ajax-purchase-request.php',
                    beforeSend:function (xhr){
                        clearAllRows(tabObj.find(".mnv-transaction"));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDetailById&pkey=" +  tabObj.find("[name=hidPurchaseRequestKey]" ).val() ,  
                    success: function(data){  
                        
                        if (!data) return;
                       
                        var data = JSON.parse(data);   
                         
                        for(i=0;i<data.length;i++){   
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidDetailKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                            arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                            arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                            arrPostValue.push({"selector":"priceInUnit", "value":data[i].priceinunit}); 
                            arrPostValue.push({"selector":"selUnit", "value":data[i].unitkey});
                            arrPostValue.push({"selector":"detailSubtotal", "value":data[i].qty * data[i].priceinunit});
                             
                            $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                            $newRow.find(".baseitemunit").first().html(data[i].baseunitname);  

                        } 
                            
                        thisObj.calculateTotal();
                        thisObj.rebindEl(); 
                        tabObj.find(".inputnumber, .inputdecimal").blur();
                        decreaseActiveAjaxConnections(thisObj);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
            


        } 
        
        
        this.updateDetailService =  function updateDetailService(){  
            clearAllRows(tabObj.find(".mnv-transaction"));

                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;

                $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-car-service.php',
                    beforeSend:function (xhr){
                        clearAllRows(tabObj.find(".mnv-transaction"));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDetailById&pkey=" +  tabObj.find("[name=hidServiceKey]" ).val() ,  
                    success: function(data){  
                        
                        if (!data) return;
                       
                        var data = JSON.parse(data);
                         
                        for(i=0;i<data.length;i++){  
                             
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidDetailKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                            arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                            arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                            arrPostValue.push({"selector":"selUnit", "value":data[i].unitkey});
                            arrPostValue.push({"selector":"priceInUnit", "value":data[i].priceinunit}); 
                            arrPostValue.push({"selector":"discounValueInUnit", "value":data[i].discount}); 
                            arrPostValue.push({"selector":"selDiscountType", "value":data[i].discounttype}); 
                            arrPostValue.push({"selector":"detailSubtotal", "value":data[i].total}); 
                            $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                            $newRow.find(".baseitemunit").first().html(data[i].baseunitname);  
                        } 
                            

                        thisObj.rebindEl(); 
                        tabObj.find(".inputnumber, .inputdecimal").blur();

                        decreaseActiveAjaxConnections(thisObj);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
            


        } 
        
        this.updateTransactionType = function updateTransactionType(){ 
            var selType = tabObj.find("[name=selType]");  
            var purchaseObj = tabObj.find(".ispurchase");
            var serviceObj = tabObj.find(".isservice");
            
            var transactionType = selType.val(); 
             
            if (transactionType == varConstant.TRANSACTIONTYPE.Request){ 
                purchaseObj.show();
                serviceObj.hide(); 
            }else{
                purchaseObj.hide();
                serviceObj.show(); 
            }  
        }

        this.onChangePurchaseRequest =  function onChangePurchaseRequest(event, ui){  
                var obj = this; 
     
            if (tabObj.find("[name=hidCurrentPurchaseRequestKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah pelanggan akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Penawaran Pembelian", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidPurchaseRequestKey]" ).val(tabObj.find("[name=hidCurrentPurchaseRequestKey]" ).val());
                        tabObj.find("[name=purchaseRequestCode]" ).val(tabObj.find("[name=hidCurrentPurchaseRequestCode]" ).val());
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        thisObj.rebindEl(); 
                  },
                  buttons : {
                      OK : function (){  
                             if (ui.item == null) { 
                                clearAutoCompleteInput(obj,'hidPurchaseRequestKey');	
                                tabObj.find("[name=hidCurrentPurchaseRequestKey]" ).val(''); 
                                tabObj.find("[name=hidCurrentPurchaseRequestCode]" ).val(''); 
                             }else{
                                tabObj.find("[name=hidCurrentPurchaseRequestKey]" ).val(ui.item.pkey); 
                                tabObj.find("[name=hidCurrentPurchaseRequestCode]" ).val(ui.item.value);  
                             } 
 
                            thisObj.importData(); 
                            $( this ).dialog( "close" );
                      },
                      Cancel : function (){  
                            $( this ).dialog( "close" );
                      }
                  },
                });	 
            }else{ 
                 if (ui.item == null) {
                    clearAutoCompleteInput(obj,'hidPurchaseRequestKey');	
                    tabObj.find("[name=hidCurrentPurchaseRequestKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentPurchaseRequestCode]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentPurchaseRequestKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentPurchaseRequestCode]" ).val(ui.item.value); 
                    thisObj.importData(); 

                 } 	

                 thisObj.rebindEl(); 
            } 
        }
    
    this.updateDetail = function updateDetail(target, objAndValue, ui) {
        var detailRow = $(target).closest(".transaction-detail-row");
        var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
        var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();

        disabledButton(detailRow.find("[name=btnMoreOptions]"));
        detailRow.find(".options-row").hide();

        for (i = 0; i < objAndValue.length; i++)
            detailRow.find("[name='" + objAndValue[i].object + "']").first().val(ui.item[objAndValue[i].value]).blur();

        updateAvailableUnit(itemKeyObj, selUnitObj);
        thisObj.updatePrice(itemKeyObj);

        // harus handle manual utk obj autosearch
        detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);

        if (ui.item['needsn'] == 1) {
            calculateSNNeeded(tabObj, target);
        }
        
        var qtyInPcs = tabObj.find("[name='qtyInPcs[]']");
        
        if (qtyInPcs.length > 0) {
            var isWeightFixed = ui.item['isweightfixed'] || 0;
            var disabled = (isWeightFixed == 1) ? true : false;

            qtyInPcs.attr("readonly", disabled);
        }

        updateSNOptions(tabObj, detailRow);

        if(thisObj.isActiveModulePurchasePrice) {
            thisObj.updatePurchasePrice(detailRow);
        }
    }
        
        this.calculateDetail = function calculateDetail(obj){   
     
            var row =  $(obj).closest(".transaction-detail-row");  
            var itemkey =  row.find("[name='hidItemKey[]']").val(); 

            var qty =  unformatCurrency(row.find("[name='qty[]']").val()) || 0;
            var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
            var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
            var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
            var unitkey =  row.find("[name='selUnit[]']").val(); 
 
            // harus diatas, utk jewelry
            var isWeightFixed =  row.find("[name='chkWeightFixed[]']").val(); 
            if(isWeightFixed == 1){
                var gramasi =  unformatCurrency(row.find("[name='gramasi[]']").val()) || 0; 
                row.find("[name='qtyInPcs[]']").val(qty * gramasi).blur(); 
            }
            

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

            // if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit; 
            // var subtotal = qty * (priceInUnit - discount);
            
            row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 

            thisObj.calculateTotal();
        }


        this.calculateTotal = function calculateTotal(){    
            var subtotal = 0; 
            tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
            tabObj.find("[name='subtotal']").val(subtotal).blur();

            var finalDiscount = parseFloat(unformatCurrency( tabObj.find("[name='finalDiscount']").val())) || 0 ;
            var finalDiscountType = parseInt(unformatCurrency( tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
            var shipmentFee = parseFloat(unformatCurrency( tabObj.find("[name='shipmentFee']").val())) || 0 ; 
            var etcCost = parseFloat(unformatCurrency( tabObj.find("[name='etcCost']").val())) || 0 ; 
            var includeTax =    tabObj.find("[name='chkIncludeTax']").val();
            var taxPercentage =  parseFloat(unformatCurrency( tabObj.find("[name='taxPercentage']").val())) || 0 ;  

            if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 

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
             tabObj.find("[name='total']").val(total).blur();

/*            var totalPayment = 0; 
             tabObj.find("[name='paymentMethodValue[]']").each(function() {   
                totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
            }) */

            var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));
            
            var balance = totalPayment - total;
             tabObj.find("[name='balance']").val(balance).blur();
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
                            data = JSON.parse(data) ; 
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

        this.updateInvoiceReference = function updateInvoiceReference()
        {
            $( "#dialog-message" ).html("Apakah anda ingin mengubah data Referensi Invoice ?");
            $( "#dialog-message" ).dialog({
            width: 300,
            modal: true,
            title:"Konfirmasi Perubahan Data Referensi Invoice", 
            open: function() {
                $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
            }, 
            buttons : {
                    OK : function (){    
                        thisObj.updateRefInvoiceCode();
                        $( this ).dialog( "close" );
                    },
                    Cancel : function (){  
                        $( this ).dialog( "close" );
                    }
                } 

            });	 
        }
        
        this.updateRefInvoiceCode = function updateRefInvoiceCode()
        {
            var pkey = tabObj.find("[name=hidId]").val();
            var refInvoiceCode = tabObj.find("[name=refInvoiceCode]").val();
 
            var ajaxData = {
                pkey: pkey,
                refInvoiceCode: refInvoiceCode
            };

            $.ajax({
                type: "POST",
                async: false,
                url: "ajax-purchase-order.php",
                data: {
                    action: "updateInvoiceReference",
                    data: ajaxData
                },
                success: function(response) {
    
                    if(!response) return;
                    var data = parseJSON(response);
                    var result = data[0];
                    if(result.valid) {
                        alert(result.message);
                    } else {
                        alert(result.message);
                    }
                
                }, 
                error: function(xhr, status, error) {
                    console.error(error);
                },
                complete:function(xhr, desc) { 
//                    thisObj.rebindEl();
                }
            });

           
        }
         
        this.onChangeServiceOrder = function onChangeServiceOrder(){ 
            thisObj.updateDetailService();
        }
         
        this.updateSupplierInformation =  function updateSupplierInformation (topkey){
            if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + topkey + "']").length > 0)
                tabObj.find("[name=selTermOfPaymentKey]").val(topkey).change();  
        }


        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal(); ;   
        }
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.updateAutoNumberRow();
         thisObj.calculateTotal(); 
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


        this.updateAutoNumberRow = function updateAutoNumberRow() {
            var number = 1;

            $(".transaction-detail-row").each(function() {
                var input = $(this).find("input[name='numberDetail[]']");
                if (!input.length) return;

                input.each(function() {
                    $(this).val(number).trigger("change");
                });

                number++;
            });
        }

        this.updatePurchasePrice = function updatePurchasePrice(row)
        {
            var supplierkey = tabObj.find("[name=hidSupplierKey]").val();
            var itemkey = row.find("[name=\"hidItemKey[]\"]").first().val();

            if(!supplierkey) return;

            $.ajax({
                type: "GET",
                url:  'ajax-purchase-price.php',
                async : false,
                data: "action=getPurchasePrice&itemkey=" + itemkey +"&supplierkey=" + supplierkey,  
            }).done(function( data ) { 
                    
                
                var data = parseJSON(data);
                
                if(!data || data.length == 0) {
                    row.find("[name='priceInUnit[]']").val(0);
                    return;             
                }
   
                row.find("[name='priceInUnit[]']").val(data[0].price).change();
                tabObj.find(".inputnumber").blur();  
            }); 

        }
  this.onChangePurchaseGroup = function onChangePurchaseGroup(obj) {
            var row = $(obj).closest(".transaction-detail-row"); 
            
            var purchaseGroup = row.find("[name='selPurchaseGroup[]']").val();
    
            var sparePartObj = row.find(".is-spare-part");
            var unitObj = row.find(".is-unit");
            if (purchaseGroup == 1){ 
                sparePartObj.show();
                unitObj.hide(); 
                row.find("[name='detailCode[]']").prop("readonly", true); 
                row.find("[name='brandNameDetail[]']").prop("readonly", true); 
                row.find("[name='typeNameDetail[]']").prop("readonly", true); 
                row.find("[name='serialNumber[]']").prop("readonly", true); 
                row.find("[name='categoryDetailName[]']").prop("readonly", true); 
                row.find("[name='qty[]']").prop("readonly", false); 
                row.find("[name='selUnit[]']").prop("readonly", false); 
                console.log('tutup');
            }else{
                row.find("[name='detailCode[]']").prop("readonly", false); 
                row.find("[name='brandNameDetail[]']").prop("readonly", false); 
                row.find("[name='typeNameDetail[]']").prop("readonly", false); 
                row.find("[name='serialNumber[]']").prop("readonly", false); 
                row.find("[name='categoryDetailName[]']").prop("readonly", false); 
                row.find("[name='qty[]']").prop("readonly", true).val(1); 
                row.find("[name='selUnit[]']").prop("readonly", true).val(1); 
                console.log('buka');
    
                sparePartObj.hide();
                unitObj.show(); 
            }  
        }
            

    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData&limit=25', thisObj.updateDetail);
        bindAutoCompleteForTransactionDetail('brandNameDetail[]', objAndValueForDetailBrandAutoComplete, 'ajax-brand.php?action=searchData');
        bindAutoCompleteForTransactionDetail('typeNameDetail[]', objAndValueForDetailTypeAutoComplete, 'ajax-car-series.php?action=searchData');
        bindAutoCompleteForTransactionDetail('categoryDetailName[]', objAndValueForDetailCategoryAutoComplete, 'ajax-category-asset-item.php?action=searchData');
        bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]'],  [name='selUnit[]'], [name='chkPriceInPcs[]'], [name='qtyInPcs[]'], [name='priceInPcs[]']"), 'change', function () {
           calculateSNNeeded(tabObj,this); thisObj.calculateDetail(this);
        });
        bindEl(tabObj.find("[name='selDiscountType[]']"), 'change', function () {
            updateDecimal(this);
            thisObj.calculateDetail(this);
        });
        bindEl(tabObj.find("[name='selPurchaseGroup[]']"), 'change', function () {
            thisObj.onChangePurchaseGroup(this);
        });
        bindEl(tabObj.find("[name='chkPriceInPcs[]']"), 'change', function () {
            thisObj.onChangePriceInPcs($(this));
        });
        bindEl(tabObj.find(".btn-sn-options"), 'click', function () {
            SNOptHander(tabObj, this, thisObj.snRegex);
            mnvOptionsRowOnClick($(this));
        });
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
                
        tabObj.find("[name=chkIsFullReceive]").on('change', function () {
            updateSNOptions(tabObj);
        });
           tabObj.find(".transaction-detail-row").each(function(){ calculateSNNeeded(tabObj, $(this));  }); 
            tabObj.find("[name=selTermOfPaymentKey]" ).change();   
           
            tabObj.find(" [name=chkIsFullReceive]" ).change(); 
            tabObj.find(".form-detail-field").toggle(); 

            tabObj.find(".form-detail-button").click(function() {   
                 tabObj.find(".form-detail-field").toggle( "highlight" );
                var temp =  tabObj.find(".form-detail-button").attr("relalt");   
                $("#" + tabID+ " .form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
                 tabObj.find(".form-detail-button").text(temp); 
            }); 
               
            tabObj.find("[name=btnUpdate]").on('click', function() {
                thisObj.updateInvoiceReference();
            });

            tabObj.find("[name=btnAddRows]").on("click", function(){
                thisObj.updateAutoNumberRow();
            });

            tabObj.find("[name=selType]").change(function() { thisObj.updateTransactionType(); }); 
            tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal(this)}) 
            tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)}) 

            tabObj.find("[name=selType]" ).change(); 
            thisObj.updateAutoNumberRow(); 

            thisObj.rebindEl(); 
  
        }
}
