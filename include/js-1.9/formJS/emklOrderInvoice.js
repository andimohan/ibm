function EMKLOrderInvoice(tabID, tablekey, cashTOP,varConstant){    
    
        var thisObj = this;
        var tabObj = $("#" + tabID);   
     
        var taxRounded = varConstant.TAX_ROUND_TYPE;
        var vatOutRoundType = varConstant.VAT_OUT_ROUND_TYPE || 2; // utk pembulatan mau ikut coretax atau tdk
    
		var objAndValue = new Array; 
        objAndValue.push({object:'hidSalesOrderKey[]', value :'pkey'}); 
        objAndValue.push({object:'salesOrderSubtotal[]', value :'subtotalcurrency'}); 	    
        objAndValue.push({object:'doNumberDetail[]', value :'hbl'}); 	   
        //objAndValue.push({object:'salesOrderDate[]', value :'trdate', type : 'date'}); 	 
        var objAndValueForDetailAutoComplete = objAndValue;	
    
		var objAndValue = new Array;  
        objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
        var objAndValueForDetailItemAutoComplete = objAndValue;
    
        var objAndValue = new Array;  
        objAndValue.push({object:'hidInvoiceKey[]', value :'pkey'});  
        objAndValue.push({object:'amount[]', value :'grandtotal'});  
        objAndValue.push({object:'salesOrderSubtotal[]', value :'grandtotal'});  
        var objAndValueForDetailInvoiceAutoComplete = objAndValue;
    
		var objAndValue = new Array;   
        objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'}); 
        objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'});  
        var objAndValueForDPDetailAutoComplete = objAndValue;
 
        var currencyRate = [];
    
        this.tabID = tabID;    
        this.tablekey = tablekey;     
    
        this.recordedCode = [];
    
        /*this.getDownpaymentType = function getDownpaymentType(){ 
            return (tabObj.find("[name='chkDownpayment']").val() == 1) ? true : false; 
        }*/
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row"); 

            thisObj.updateRowInformation(detailRow,objAndValue,ui);
            thisObj.updateSODetail(detailRow); 
            thisObj.calculateTotal();  
            thisObj.updateDefaultDownpayment();
        } 
                
        this.updateDetailForInvoice = function updateDetailForInvoice(target, objAndValue,ui){
            var detailRow = $(target).closest(".transaction-detail-row"); 
            thisObj.updateRowInvoiceInformation(detailRow,objAndValue,ui);


            thisObj.updateInvoiceDetail(detailRow); 
            thisObj.calculateTotal();   
        }

        this.updateRowInvoiceInformation  = function updateRowInvoiceInformation (detailRow,objAndValue,ui){
       
           var i;
           for(i=0;i<objAndValue.length;i++){     

                /*if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);*/

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();   

            }
                        
            thisObj.calculateTotal(); 

            
            // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
            detailRow.find("[name='invoiceCode[]']").first().val(ui.item['value']); 
            
            

       }
        this.updateRowInformation  = function updateRowInformation (detailRow,objAndValue,ui){
       
           var i;
           for(i=0;i<objAndValue.length;i++){     

                /*if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);*/

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();   
                thisObj.updatePartialInvoiceOutstanding(detailRow);    

            }

            thisObj.calculateTotal(); 
            
            // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
            detailRow.find("[name='salesOrderCode[]']").first().val(ui.item['value']); 
       }
        

       this.updatePartialInvoiceOutstanding = function updatePartialInvoiceOutstanding(detailRow){
            /*var headerInvoiceType = tabObj.find("[name=selCustomCode]" ).val();
            var soKey =  detailRow.find("[name='hidSalesOrderKey[]']").first().val();

            if(soKey){
                     // update downpayment and amount
                       $.ajax({
                        type: "GET",
                        url:  'ajax-emkl-job-order.php', 
                        async : false,
                        data: 'action=getTotalInvoicedAndOutstanding&pkey=' + soKey +'&invoiceType=' + headerInvoiceType, 
                        success: function(data){    
                            var data = JSON.parse(data);   
                            detailRow.find("[name='salesOrderDownpayment[]']" ).first().val(data.outstanding).blur();                  
                        }  
                    }); 
            }*/

       }
       
       
        this.checkDownpaymentExist =  function checkDownpaymentExist(dpkey){ 
             
             var found = false;
             
             tabObj.find("[name='hidDownpaymentKey[]']").each(function(){  
                 if ($(this).val() == dpkey){ 
                     found = true; 
                     return;
                 }
             })
             
             return found;
         }
        
        
        this.updateDefaultDownpayment = function updateDefaultDownpayment(){ 
              
             var soKey = new Array();
             tabObj.find("[name='hidSalesOrderKey[]']").each(function(){ if($(this).val()) soKey.push($(this).val()); })
            
              $.ajax({
                type: "GET",
                url:  'ajax-customer-downpayment.php', 
                data: 'action=getDownpaymentForTruckingServiceOrderInvoice&sokey=' + JSON.stringify(soKey), 
                success: function(data){   
                    
                    var data = JSON.parse(data);  
                    var i;
                    for(i=0;i<data.length;i++){ 
                        
                        // kalo blm ad DP yg sama
                        if(! thisObj.checkDownpaymentExist(data[i].pkey)){  
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"downpaymentCode", "value":data[i].code});  
                            arrPostValue.push({"selector":"hidDownpaymentKey", "value":data[i].pkey}); 
                            arrPostValue.push({"selector":"downpaymentAmount", "value":data[i].outstanding});  
                            updateTemplateRow("downpayment-row-template",JSON.stringify(arrPostValue))  
                        }
                         
                    }       
                     
                    //bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete[tabID],'ajax-customer-downpayment.php?action=searchData&customerkey='+customerkey); 
                    thisObj.rebindDownpayment();
                    
                    tabObj.find("[name=\"downpaymentAmount[]\"]").change().blur();
                    thisObj.calculateTotalDownpayment();                         
                }  
            }); 
            
        }
   
        
        this.calculateTotalDownpayment = function calculateTotalDownpayment(){
            var totalDP = 0; 
            tabObj.find("[name='downpaymentAmount[]']").each(function() { totalDP += parseFloat(unformatCurrency($(this).val())) || 0;   })
            tabObj.find("[name='totalDownpayment']").val(totalDP).blur(); 
             
            return totalDP;
         }

        
//    function updateAvailableUnit(itemKeyObj, selUnitObj){
//
//         $.ajax({
//                    type: "GET",
//                    url:  'ajax-item',
//                    data: { action : 'getAvailableUnit',
//                            itemkey: itemKeyObj.val() 
//                          } ,
//                    success: function(data){   
//
//                             if (!data) return;
//                             var data = JSON.parse(data);
//
//                            // update combobox services
//                            var newOptions = {};
//                            for(i=0;i<data.length;i++)  
//                                newOptions[data[i].conversionunitkey] =  data[i].unitname;       
//
//                            var options = (selUnitObj.prop) ? selUnitObj.prop('options') : selUnitObj.attr('options');  
//
//                            $('option', selUnitObj).remove();
//
//                            $.each(newOptions, function(val, text) {
//                                options[options.length] = new Option(text, val);
//                            });
//
//                            // add conversion 
//                            selUnitObj.find("option").each(function(i){ 
//                                    $(this).attr("relconversionmultiplier",data[i].conversionmultiplier);
//                                } 
//                            ) 
//
//                            //selUnitObj.find('option:eq(0)').prop('selected', true).change();
//                            selUnitObj.val(data[0]['deftransunitkey']);
//
//                    } 
//            });    
//    }


        
        this.updateSODetail = function updateSODetail(row){ 
        
            var arrPkey = []; 
            row.each(function(){   
                arrPkey.push($(this).find('[name="hidSalesOrderKey[]"]').val());
            });
            
            //var downpaymentType = thisObj.getDownpaymentType();
   
            var serviceSelector = 'service-row-template'; 
            var currencykey = tabObj.find(" [name=selCurrency]").val();
 
            
            //custome code untk menentukan type invoice / reimburse / void
            var customCode = tabObj.find("[name=selCustomCode]").val();
            
            // if DP type, hide the options-row  
            // ini harus idipindhan kebawah kah ?
            row.find(".options-row").hide();
             
             $.ajax({
	            type: "GET",
	            url:  'ajax-emkl-job-order.php',
	            beforeSend:function (xhr){    
                    row.find(".transaction-detail-row").remove(); //remove all rows  
	            },
                async:false,  
	            data: {action: "getUnInvoicedItemDetail", pkey : arrPkey, typekey: customCode }, 
	            success: function(data){ 
                        if (data == "") return;
                    
	                    var dataSet = JSON.parse(data);
                     
                        updateComboboxReadonly($("[name='taxDetail[]']"),false);
                    
                         row.each(function(){   
                            var itrRow = $(this);
                            var key = itrRow.find('[name="hidSalesOrderKey[]"]').val(); 
                            var data = dataSet[key]; 
							 
							 if(typeof data === 'undefined' || data.length == 0) return;
                            
                             for(i=0;i<data.length;i++){
								 
	                            var arrPostValue = []; 
	                            arrPostValue.push({"selector":"hidRefSODetailKey", "value":data[i].pkey});
	                            arrPostValue.push({"selector":"qtyDetail", "value":data[i].outstandingqty});
	                            arrPostValue.push({"selector":"itemNameDetail", "value":data[i].itemname}); 
	                            arrPostValue.push({"selector":"itemNameAliasDetail", "value":data[i].aliasname});
	                            arrPostValue.push({"selector":"hidItemDetailKey", "value":data[i].itemkey});
	                            arrPostValue.push({"selector":"priceInUnitDetail", "value":data[i].priceinunit});  
	                            arrPostValue.push({"selector":"detailRate", "value":data[i].rate});  
	                            arrPostValue.push({"selector":"currencyName", "value":data[i].currencyname});    
	                            arrPostValue.push({"selector":"hidCurrencyKey", "value":data[i].currencykey});   
  	                            arrPostValue.push({"selector":"hidContainerDetailKey", "value":data[i].containerkey});
	                            arrPostValue.push({"selector":"selDetailItemUnit", "value":data[i].unitkey});

  
                                if(varConstant.usePPNDetail == 1){ 
                                    arrPostValue.push({"selector":"taxDetail", "value":parseFloat(data[i].taxdetail)}); 
                                    arrPostValue.push({"selector": "subtotalDetail", "value": data[i].total });  
                                    arrPostValue.push({"selector":"afterTaxDetail", "value":data[i].total});
                                    arrPostValue.push({"selector":"beforeTaxDetail", "value":data[i].total});
                                }                            
                                //arrPostValue.push({"selector":"selDetailCurrency", "value":data[i].currencykey});   
                            
	                            newrow = addNewTemplateRow(serviceSelector,JSON.stringify(arrPostValue),itrRow );  
                                
                                newrow.addClass("service-detail-row"); // utk perhitungan total detail
                                newrow.find("[name='chkService[]']").val(1).change();
                                newrow.find("[name='chkIsTax23[]']").val(data[i].istax23).change(); 

                                if(varConstant.usePPNDetail == 1){ 
                                    
                                    
//                                    newrow.find("[name='taxDetail[]']").val(parseFloat(data[i].taxdetail));
                                    
                                    newrow.find("[name='chkIsReimburse[]']").val(data[i].isreimburse).change(); 
                                    newrow.find("[name='chkIncludeTaxDetail[]']").val(data[i].ispriceincludetax).change(); 

                                    //readonly tax if isreimburse
                                    thisObj.updateReimburse(newrow);
                                }

                                //thisObj.updateDetailRate(newrow);
                             }
                         });
                              
	                    
                        updateComboboxReadonly($("[name='taxDetail[]']"));
                    
                        tabObj.find(".inputnumber, .inputdecimal, .inputautodecimal").blur();
                    
                        // harus hitung ulang, karena onchangenaya chkbox gk update total karena detail arraynya sudah beda
                        thisObj.calculateTotal(); 

                        if(data.length > 0) row.find(".options-row").show();
                     
                        thisObj.rebindEl(); 
                    
	                    // utk update rate dan hitung ulang
                        tabObj.find("[name='detailRate[]']").change(); 
                     
	            }  
	        });
            
        }
        

  this.updateInvoiceDetail = function updateInvoiceDetail(row){

            var arrPkey = []; 
            row.each(function(){   
                arrPkey.push($(this).find('[name="hidInvoiceKey[]"]').val());
            });
             
	  
            //var downpaymentType = thisObj.getDownpaymentType();
   
            var serviceSelector = 'service-row-template'; 
            var currencykey = tabObj.find(" [name=selCurrency]").val();

			var ajaxUrl = 'ajax-emkl-order-invoice.php';
			var ajaxData = 'getInvoiceItemDetail';

            // if DP type, hide the options-row  
            // ini harus idipindhan kebawah kah ?
            row.find(".options-row").hide();
             
             $.ajax({
	            type: "GET",
	            url:  ajaxUrl,
	            beforeSend:function (xhr){    
                    row.find(".transaction-detail-row").remove(); //remove all rows  
	            },
                async:false,  
	            data: {action: ajaxData, pkey : arrPkey }, 
	            success: function(data){ 
                        if (data == "") return;
                    
	                     var dataSet = JSON.parse(data);
                         row.each(function(){   
                            var itrRow = $(this);
                            var key = itrRow.find('[name="hidInvoiceKey[]"]').val(); 
                            var data = dataSet[key]; 

                             for(i=0;i<data.length;i++){    
	                            var arrPostValue = []; 
	                            arrPostValue.push({"selector":"hidRefSODetailKey", "value":data[i].refsodetailkey});
	                            arrPostValue.push({"selector":"qtyDetail", "value":data[i].qtyinbaseunit});
	                            arrPostValue.push({"selector":"itemNameDetail", "value":data[i].itemname}); 
	                            arrPostValue.push({"selector":"itemNameAliasDetail", "value":data[i].aliasname});
	                            arrPostValue.push({"selector":"hidItemDetailKey", "value":data[i].itemkey});
	                            arrPostValue.push({"selector":"hidContainerDetailKey", "value":data[i].containerkey});
	                            arrPostValue.push({"selector":"priceInUnitDetail", "value":data[i].priceinunit}); 
	                            arrPostValue.push({"selector":"subtotalDetail", "value":data[i].total});   
	                            arrPostValue.push({"selector":"detailRate", "value":data[i].rate});  
	                            arrPostValue.push({"selector":"currencyName", "value":data[i].currencyname});    
	                            arrPostValue.push({"selector":"hidCurrencyKey", "value":data[i].currencykey});   
                            
                                //arrPostValue.push({"selector":"selDetailCurrency", "value":data[i].currencykey});   
                            
	                            newrow = addNewTemplateRow(serviceSelector,JSON.stringify(arrPostValue),itrRow );  
                            
                                newrow.addClass("service-detail-row"); // utk perhitungan total detail
                                newrow.find("[name='chkService[]']").val(1).change();
                                newrow.find("[name='chkIsTax23[]']").val(data[i].istax23).change(); 
                                
                                //thisObj.updateDetailRate(newrow);
                             }
                         });
                              
	                    
                    
                        tabObj.find(".inputnumber, .inputdecimal, .inputautodecimal").blur();
                    
                        // harus hitung ulang, karena onchangenaya chkbox gk update total karena detail arraynya sudah beda
                        thisObj.calculateTotal(); 

                        if(data.length > 0) row.find(".options-row").show();
                     
                        thisObj.rebindEl(); 
                    
	                    // utk update rate dan hitung ulang
                        tabObj.find("[name='detailRate[]']").change();
                    
                     
	            }  
	        });
            
        }
        
        this.showInvoiceRef = function showInvoiceRef(obj){
            $obj = $(obj);
            if ($obj.val() == 25)
                tabObj.find(".invoice-ref").show();
            else 
                tabObj.find(".invoice-ref").hide();
        }
        
        
        this.updateOnChange = function updateOnChange(target,objAndValue,ui){   
                var detailRow = $(target).closest(".transaction-detail-row"); 
               // thisObj.disabledAmount(detailRow); // udah gk perlu lg
        } 
        
          
        this.disabledAmount = function disabledAmount(detailRow){
       
			// utk EMKL sementara, amount selalu readonly, karena
			// 1. tdk ad invoice sebagian
			// 2. tidak ad komponen biaya
  
			var selInvoiceType = (detailRow == undefined) ?  tabObj.find("[name='selInvoiceType[]']") :  detailRow.find("[name='selInvoiceType[]']").first();
			selInvoiceType.each(function(){  
					var transactionRow = $(this).closest(".transaction-detail-row");
					var detailKey = $(this).val();

					// utk jenis kwitansi, readonly semua detail
					$readonly = (detailKey == 1) ? false : true;

					// [name='amount[]'], selalu readonly
					var readonlyObj = transactionRow.find("[name='dummychkService[]'],[name='qtyDetail[]'],[name='itemNameAliasDetail[]'],[name='dummychkIsTax23[]']");
					
					readonlyObj.prop("readonly", $readonly);

					if ($readonly){ 
					  readonlyObj.attr("tabIndex","-1");
						
					  var chkPick = transactionRow.find("[name='dummychkService[]']");
					  if(!chkPick.prop("checked"))
						 chkPick.click().change();
 
					}else{ 
					  readonlyObj.removeAttr("tabIndex"); 
					}
			 });
			
			
			 
        }
        
        this.resetDetails = function resetDetails(){   
//            clearAllRows($("#"+tabID));
            clearAllRows(tabObj.find(".mnv-transaction"));
            clearAllRows(tabObj.find(".mnv-downpayment"));
            thisObj.rebindEl();
            thisObj.calculateTotal(); 
            thisObj.rebindDownpayment();  
        }
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        }
              
        this.calculateServiceDetail = function calculateServiceDetail(obj){ 
            
            var serviceRow = $(obj).closest(".service-detail-row");  
                
            var currencykey = serviceRow.find("[name='hidCurrencyKey[]']").val();
            var currencyheaderkey = tabObj.find("[name=selCurrency]").val();
            
            var qty =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyDetail[]']").val())) || 0;
            var price = parseFloat(unformatCurrency(serviceRow.find("[name='priceInUnitDetail[]']").val())) || 0;
            var rate = parseFloat(unformatCurrency(serviceRow.find("[name='detailRate[]']").val())) || 1;
            var amount = qty * price; 
           
            if(currencyheaderkey==varConstant.CURRENCY.idr){
                if(currencykey != varConstant.CURRENCY.idr)
                    amount *= rate; 
            }else{
                if(currencykey == varConstant.CURRENCY.idr)
                    amount /= rate; 
            }
  
            if(varConstant.usePPNDetail == 1){
            
                var taxValueDetail = parseFloat(unformatCurrency(serviceRow.find("[name='taxDetail[]']").val())) || 0;
                var isInc = serviceRow.find("[name='chkIncludeTaxDetail[]']").val() || 0;
                
                var taxValue = 0;
                var beforeTaxDetail = amount;
                if(taxValueDetail > 0){
                    if (isInc == 0) {
                        
                        taxValue = amount * taxValueDetail / 100;
                
                    }else{

                        taxValue = (taxValueDetail/(100 + taxValueDetail)) * amount;  
                        beforeTaxDetail -= taxValue;
                    } 	
                } 
                
//                taxValue += taxValueRounding; 
                
                var afterTaxDetail = beforeTaxDetail + taxValue;
                serviceRow.find("[name='beforeTaxDetail[]']").val(beforeTaxDetail).blur();
                serviceRow.find("[name='afterTaxDetail[]']").val(afterTaxDetail).blur();
                serviceRow.find("[name='taxValueDetail[]']").val(taxValue).blur();
            }

            serviceRow.find("[name='subtotalDetail[]']").val(amount).blur(); 
            
            thisObj.calculateTotal();
        }
        
        this.calculateTax23 = function calculateTax23(){
             
            var tax23Percentage =  parseFloat(unformatCurrency(tabObj.find("[name='tax23Percentage']").val())) || 0 ; 
            var beforeTaxTotal =  parseFloat(unformatCurrency(tabObj.find("[name='hidTotalBeforeTaxPPH23']").val())) || 0 ; 
            
            useTax23 = 1;

            //tax23Percentage = 0;
            tax23Value = 0; 

            if (useTax23 != 0 && tax23Percentage > 0) { 
                    var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
                    var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ;
 
                   if (includeTax == 1) 
                     beforeTaxTotal = beforeTaxTotal - (taxPercentage/(100 + taxPercentage)) * beforeTaxTotal;    
                
                    tax23Value = (tax23Percentage/100) * beforeTaxTotal; 
            }
     
            //tabObj.find("[name='tax23Percentage']").val(tax23Percentage).blur();
            tabObj.find("[name='tax23Value']").val(tax23Value).blur(); 
        }
 
        this.calculateTotal = function calculateTotal(){
              
            var amount = 0, qty, price, serviceRow;  
            var tax23 = 0 ;
            var totalPPNDetail = 0 ;
            //var downpaymentType = thisObj.getDownpaymentType();
            var totalUsePPN = 0;
             
            tabObj.find("[name='chkPick[]']").each(function(){  
                    
                    if ($(this).val() != 1 )  return;
                
                    row = $(this).closest(".transaction-detail-row");
                    objSubtotal = row.find("[name='salesOrderSubtotal[]']");  
                    //objDownpayment = row.find("[name='salesOrderDownpayment[]']");  
                    objTotal = row.find("[name='amount[]']");  
                 
                    invoiceType = row.find("[name='selInvoiceType[]']").val();  
                    
                
                    //if (invoiceType == 1 && !downpaymentType){

                    // kalau tipe 3 ad kemungkinan ada PPN, 
                    if (invoiceType == 1 ){
                        var rowTotal = 0;
                         
                        row.find("[name='chkService[]']").each(function(){    
                               if ($(this).val() != 1 ) return; 
                             
                            var serviceRow = $(this).closest(".service-detail-row"); 
                            var beforeTaxDetail = parseFloat(unformatCurrency(serviceRow.find("[name='beforeTaxDetail[]']").val())) || 0 ;
                            var subtotalDetail = parseFloat(unformatCurrency(serviceRow.find("[name='subtotalDetail[]']").val())) || 0;
                            var ppnDetail = parseInt(unformatCurrency(serviceRow.find("[name='taxDetail[]']").val())) || 0;

                            var ppnValueDetail = parseFloat(unformatCurrency(serviceRow.find("[name='taxValueDetail[]']").val())) || 0;  

                            if(varConstant.usePPNDetail == 1)
                               totalUsePPN += beforeTaxDetail;                                                         
                               rowTotal += subtotalDetail;  
                            
                               // kalo ad tax23
                               if (serviceRow.find("[name='chkIsTax23[]']").val() == 1)
                                   tax23 += subtotalDetail;  
                               
                            if (ppnDetail > 0)
                               totalPPNDetail += ppnValueDetail; 
                        });

                        objSubtotal.val(rowTotal).blur(); 
                        //downpayment = parseFloat(unformatCurrency(objDownpayment.val()));
                        downpayment = 0;
                        
                        rowTotal = rowTotal - downpayment;
                        if (rowTotal < 0) rowTotal = 0;
                        
                        objTotal.val(rowTotal).blur(); 
                    }else{
                        rowTotal =  parseFloat(unformatCurrency(row.find("[name='amount[]']").val())) || 0; 
                        
                        // hitung before tax23
                         row.find("[name='chkService[]']").each(function(){    
                               if ($(this).val() != 1 ) return; 
                              
                               var serviceRow = $(this).closest(".service-detail-row"); 
                                    
                               // kalo ad tax23
                               if (serviceRow.find("[name='chkIsTax23[]']").val() == 1) 
                                   tax23 += parseFloat(unformatCurrency(serviceRow.find("[name='subtotalDetail[]']").val())) || 0;  
                              
                               
                        });
                        
                    } 

                    amount += rowTotal; 
            })
            
            // HITUNG TOTAL
            var subtotal = amount;
            
            tabObj.find("[name='subtotal']").val(subtotal).blur();
            var totalDP = thisObj.calculateTotalDownpayment(); 
            var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0 ;
            var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ; 
            var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
            var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ; 
            var otherCost =  parseFloat(unformatCurrency(tabObj.find("[name='otherCost']").val())) || 0 ; 
            //var hasTaxID =   tabObj.find("[name='customerTaxId']").val();

            if (finalDiscount != 0){
                if (finalDiscountType == 2)
                    finalDiscount = finalDiscount/100 * subtotal;
            }

            subtotal -= finalDiscount;
            
            var beforeTaxTotal = subtotal; 

            // pembulatan pajak ada 2 jenis, pembulatan pajak biasa (keatas, bawah atau round) dan pembulatan pajak dengan aturan coretax
            // pembulatan pajak biasa menggunakan settingan invoiceTaxRoundType
            // sedangkan utk coretax menggunaka vatOutRoundType
            // PRIORITAS PAJAK CORETAX (vatOutRoundType) lebih tinggi, dan akan mengoverwrite invoiceTaxRoundType


            var taxValue = 0;

            if (varConstant.usePPNDetail == 1) {
                beforeTaxTotal = totalUsePPN;
	            taxValue = totalPPNDetail;

                subtotal = beforeTaxTotal + taxValue;
            }else{
                    if (includeTax == 0) {
                        // kalo pake coretax, 
                        // ppn nya di footer, ppn nya tdk boleh hitung ulang, 
                        // tp harus penjumlahan dari tax detail value (karena ad pembulatan) 
                        
                        if (vatOutRoundType == 1){ // opsi lain biar PPn gk perlu di detail pilihnya
                            // ambil langsung subtotal saja harusnya aman, karena tipe ini yg PPN ny dibawah (footer)
                           // dan subtotal sdh dalam currrency invocie 
                            var subtotalDetail = tabObj.find("[name='subtotalDetail[]']");
                            subtotalDetail.each(function() {   
                                 if ($(this).closest(".transaction-detail-row").find("[name='chkService[]']").val() != 1) return;
                                
                                 var subtotalDetailAmount =  parseFloat(unformatCurrency($(this).val())) || 0; 
                                 taxValue +=  Math.round(subtotalDetailAmount * taxPercentage / 100); // coretax pasti pembulatan 0.5 
                            });
                         
                        }else{ 
                            taxValue =  subtotal * taxPercentage / 100;  
                            taxValue = getInvoiceRoundedTax(taxValue,taxRounded);
                        } 
                        
                       subtotal += taxValue;    
                        
                    }else{ 
                        // pembulatan untuk coretax, akan masalah utk tipe include 
                        // nanti dicek kembali
                        taxValue =  (taxPercentage/(100 + taxPercentage)) * subtotal;
                        taxValue = getInvoiceRoundedTax(taxValue,taxRounded); 
                        beforeTaxTotal = subtotal - taxValue; 
                    }
        }            

            //var tax23Percentage =  (!hasTaxID) ? 4 :  2; 
            //tabObj.find("[name='tax23Percentage']").val(tax23Percentage).blur();
            
            tabObj.find("[name='hidTotalBeforeTaxPPH23']").val(tax23).blur();
            
            tabObj.find("[name='beforeTaxTotal']").val(beforeTaxTotal).blur(); 
            tabObj.find("[name='taxValue']").val(taxValue).blur(); 
 
            var total = subtotal + otherCost;  
            tabObj.find("[name='total']").val(total).blur(); 

            var totalPayment = 0; 
            tabObj.find("[name='paymentMethodValue[]']").each(function() {   
                    totalPayment += parseFloat(unformatCurrency($(this).val())) || 0;
            }) 
            tabObj.find("[name='totalPayment']").val(totalPayment).blur();
 
          
            var balance = totalPayment + totalDP - total ;  

            /*console.log("subtotal " + subtotal);
            console.log("beforeTaxTotal " + beforeTaxTotal);
            console.log("taxValue " + taxValue);
            console.log("Other Cost " + otherCost);
            console.log("total " + total);
            console.log("balance " + balance);*/

            tabObj.find("[name='balance']").val(balance).blur();

            thisObj.calculateTax23(); 
            //thisObj.addPaymentRowIfNeeded(); 
            
        }
        
       /* this.addPaymentRowIfNeeded = function addPaymentRowIfNeeded(){ 
            autoAddNewRowTemplate(tabObj.find("[name='paymentMethodValue[]']"),"payment-method-row-template");
            autoAddNewRowTemplate(tabObj.find("[name='downpaymentAmount[]']"),"downpayment-row-template");
            
            thisObj.rebindDownpayment();
         }*/
        
        this.importData = function importData(){  
            
            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;

            
            var customerkey = tabObj.find("[name=hidCustomerKey]").val(); 
            //var statustype =  (thisObj.getDownpaymentType()) ? 'downpayment' : 'sales'; 
            var currencykey = tabObj.find(" [name=selCurrency]").val();

	        $.ajax({
	            type: "GET",
	            url:  'ajax-emkl-job-order.php',
	            beforeSend:function (xhr){ 
//                    clearAllRows($("#defaultForm-"+tabID));
                    
                    clearAllRows(tabObj.find(".mnv-transaction"));
                    clearAllRows(tabObj.find(".mnv-downpayment"));

                    thisObj.activeAjaxConnections++; 
	            }, 
	            //data: 'action=searchDataForInvoice&statustype='+statustype+'&customerkey=' + customerkey+'&currencykey='+currencykey, 
	            data: 'action=searchDataForInvoice&customerkey=' + customerkey, 
	            success: function(data){ 
	                    var data = JSON.parse(data);  
	                    var i;
                        var newrow;
                    
                             
	                    for(i=0;i<data.length;i++){ 
	                            var arrPostValue = []; 
	                            arrPostValue.push({"selector":"hidSalesOrderKey", "value":data[i].pkey});
	                            arrPostValue.push({"selector":"hidSalesOrderHeaderKey", "value":data[i].refheaderkey});
	                            arrPostValue.push({"selector":"salesOrderCode", "value":data[i].value});
	                            arrPostValue.push({"selector":"salesOrderSubtotal", "value":data[i].subtotalcurrency});  
	                            arrPostValue.push({"selector":"doNumberDetail", "value":data[i].hbl});  
	                            //arrPostValue.push({"selector":"salesOrderDate", "value": moment(data[i].trdate).format(_DATE_FORMAT_) }); 
	                            newrow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue)); 
                         
                               
                           thisObj.updatePartialInvoiceOutstanding(newrow); 
                           //thisObj.updateSODetail(newrow);
	                    }

                     
                     thisObj.updateFieldOnChangeInvoiveDownpayment();     
                     thisObj.rebindEl();
                    
	                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
	                 tabObj.find(".inputnumber, .inputdecimal, .inputautodecimal").blur(); 
                     thisObj.calculateTotal();

	                decreaseActiveAjaxConnections(thisObj); 
                     
                    tabObj.find("[name='chkPick-master']").val(1).change();   
                    thisObj.updateDefaultDownpayment();
                    
	            } ,
	             error: function(xhr, errDesc, exception) { 
                            decreaseActiveAjaxConnections(thisObj); 
                     
                }
	        });
	    } 
         
        this.onChangeChk = function onChangeChk(){   
            thisObj.calculateTotal();
        }
        
        this.rebindDownpayment = function rebindDownpayment(){ 
            var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;  
            bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete,'ajax-customer-downpayment.php?action=searchData&customerkey='+customerkey); 
        }
        
        this.updateCustomerInformation = function updateCustomerInformation(obj,event, ui){
           
                var topkey = 0;
                var bankkey = 0;
                var currencykey = varConstant.CURRENCY.idr;
            
				if (tabObj.find("[name=hidCurrentCustomerKey]" ).val() != ''){
					$( "#dialog-message" ).html("Merubah pelanggan akan mereset detail transaksi.");
					$( "#dialog-message" ).dialog({
					  width: 300,
					  modal: true,
					  title:"Konfirmasi Perubahan Data Pelanggan", 
					  open: function() {
						  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
					  },
					  close:function() {
							tabObj.find("[name=hidCustomerKey]" ).val(tabObj.find("[name=hidCurrentCustomerKey]" ).val());
							tabObj.find("[name=customerName]" ).val(tabObj.find("[name=hidCurrentCustomerName]" ).val());
                          
                          // gk boleh, kaerna user bisa edit manual
//                        	tabObj.find("[name=invoiceName]" ).val(tabObj.find("[name=hidCurrentInvoiceName]" ).val());
                            //tabObj.find("[name=customerTaxId]" ).val(tabObj.find("[name=hidCurrentCustomerTax23]").blur();
                          	$(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));  
                          
                            thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah
                            
					  },
					  buttons : {
						  OK : function (){  
						  		 if (ui.item == null) { 
									clearAutoCompleteInput(obj,'hidCustomerKey');	
									tabObj.find("[name=hidCurrentCustomerKey]" ).val(''); 
									tabObj.find("[name=hidCurrentCustomerName]" ).val(''); 
//									tabObj.find("[name=hidCurrentInvoiceName]" ).val(''); 
									//tabObj.find("[name=hidCurrentCustomerTax23]" ).val('');
								 }else{
                                     
									tabObj.find("[name=hidCurrentCustomerKey]" ).val(ui.item.pkey); 
									tabObj.find("[name=hidCurrentCustomerName]" ).val(ui.item.value);
//									tabObj.find("[name=hidCurrentInvoiceName]" ).val(ui.item.taxregistrationname);
									//tabObj.find("[name=hidCurrentCustomerTax23]" ).val(ui.item.taxid); 
                                     
                                    var tax23Percentage = 2; // (ui.item.taxid) ? 2 : 4; 
                                    tabObj.find("[name='tax23Percentage']").val(tax23Percentage).change().blur();
                                    tabObj.find("[name=invoiceName]").val(ui.item.taxregistrationname);
                                     
                                    topkey  = ui.item.termofpaymentkey;
                                    bankkey  = ui.item.companybankkey;
                                    currencykey  = ui.item.currencypreference;
                                     
                                    thisObj.updateCurrencyAndOthers(topkey,bankkey,currencykey);
                                       
								 } 
								
								thisObj.resetDetails(); 
								$( this ).dialog( "close" );
						  },
						  Cancel : function (){  
						  		$( this ).dialog( "close" );
						  }
					  },
					});	 
				}else{ 
					 if (ui.item == null) {
						clearAutoCompleteInput(obj,'hidCustomerKey');	
						tabObj.find("[name=hidCurrentCustomerKey]" ).val(''); 
						tabObj.find("[name=hidCurrentCustomerName]" ).val(''); 
//						tabObj.find("[name=hidCurrentInvoiceName]" ).val(''); 
						//tabObj.find("[name=hidCurrentCustomerTax23]" ).val('');
					 }else{ 
						tabObj.find("[name=hidCurrentCustomerKey]" ).val(ui.item.pkey); 
						tabObj.find("[name=hidCurrentCustomerName]" ).val(ui.item.value);
//						tabObj.find("[name=hidCurrentInvoiceName]" ).val(ui.item.taxregistrationname);
						//tabObj.find("[name=hidCurrentCustomerTax23]" ).val(ui.item.taxid); 

                        var tax23Percentage =  2; //(ui.item.taxid) ? 2 : 4; 
                        tabObj.find("[name='tax23Percentage']").val(tax23Percentage).change().blur();
                        tabObj.find("[name=invoiceName]").val(ui.item.taxregistrationname);
                         
                        topkey  = ui.item.termofpaymentkey;
                        bankkey  = ui.item.companybankkey;
                        currencykey  = ui.item.currencypreference;
                        thisObj.updateCurrencyAndOthers(topkey,bankkey,currencykey);
                         
					 } 	
					  
                    thisObj.rebindEl();
				} 	  
        }
        
        this.updateCurrencyAndOthers = function updateCurrencyAndOthers(topkey,bankkey,currencykey){
            
             // pidnahin kedalam, kalo diluar, kalo gk, async, variable belum sempet berubah
            if (tabObj.find("[name=selTermOfPayment] option[value='" + topkey + "']").length > 0)
                tabObj.find("[name=selTermOfPayment]").val(topkey).change(); 

            if (tabObj.find("[name=selBank] option[value='" + bankkey + "']").length > 0) 
                tabObj.find("[name=selBank]").val(bankkey).change(); 

            tabObj.find("[name=selCurrency]").val(currencykey).change(); 
			
			// update avaiable addres
			var selAddressObj = tabObj.find("[name=selInvoiceAddress]");
				
			$.ajax({
				type: "GET",
				url:  'ajax-customer.php', 
				async : false,
				data: "action=getAddressForInvoice&pkey="+tabObj.find("[name=hidCustomerKey]").val(),   
				success: function(data){  
						if(!data) return; 
						 var data = JSON.parse(data);      
					     var selectOpt = data;
						 reInsertSelectBox(selAddressObj,selectOpt, {"key" : "pkey", "label" : "name", "rel" : {"rel-address" : "value"}} );  
						 
				}  
			}); 
			
			
        }
        
        this.changeInvoiceType = function changeInvoiceType(obj){
            var invoiceType = $(obj).val();
              
            $row = $(obj).closest(".transaction-detail-row"); 
            var detailRows = $row.find(".service-detail-row"); //.not(".service-row-template");
             
            
            $row.find("[class*=type-]").hide(); 
            $row.find(".type-"+invoiceType).show();   
            
            $row.find(".options-row").hide();
             
            if ((invoiceType == 1 || invoiceType == 3) && detailRows.length > 0)
                $row.find(".options-row").show();
            
            thisObj.disabledAmount($row);
            thisObj.calculateTotal();
        }
          
        this.updateFieldOnChangeInvoiveDownpayment = function updateFieldOnChangeInvoiveDownpayment(rowObj){
              
            var transactionRow = ( rowObj ) ? rowObj : tabObj.find(".invoice-detail > .transaction-detail-row");
            //var downpaymentType = thisObj.getDownpaymentType();
 
            //gabungin sekali query saja agar lebih efis
            thisObj.updateSODetail(transactionRow);
             
            transactionRow.each(function(){
                
                var detailInvoiceType = $(this).find("[name=\"selInvoiceType[]\"]");
                var detailAmount = $(this).find("[name=\"amount[]\"]");
                var optionsRow = $(this).find(".options-row");
                var notDownpaymentField = tabObj.find(".not-downpayment-field");
                var salesOrderCode =  $(this).find("[name=\"salesOrderCode[]\"]"); 
                 
                /*if (downpaymentType){   
                    
                    notDownpaymentField.hide(); 
                    
                     tabObj.find("[name=finalDiscount]").val(0);
                     tabObj.find("[name=selFinalDiscountType]").val(1);
                    
                    if (detailInvoiceType.val() != 1){ 
                        $(this).remove();
                    }else{ 
                        detailInvoiceType.prop('disabled', true);
                        detailAmount.prop('readonly', false);
                        optionsRow.hide();
                    }
                }  else{*/  
                    notDownpaymentField.show(); 
                    detailInvoiceType.prop('disabled', false); 

                    if (detailInvoiceType.val() == 1){  
                         
                        //thisObj.updateSODetail($(this));
                        
                        detailAmount.prop('readonly', true); 
                         
                        if ($(this).find(".options-row .transaction-detail-row").length > 0 )
                            optionsRow.show();
                    }else{ 
                        detailAmount.prop('readonly', false);
                    } 
                //}

            });  
            
        }
 
           this.updateHeaderRate = function updateHeaderRate(){ 
                var selCurrencyObj = tabObj.find("[name=selCurrency]");  
                var currencyRateObj =  tabObj.find("[name='currencyRate']");
                
               // utk yang tidak pake rate di header
               if (currencyRateObj.length === 0) return;
                
                var trDateObj =  tabObj.find("[name='trDate']"); 
                if(selCurrencyObj.val() == varConstant.CURRENCY.idr){  
                    currencyRateObj.val(1);
                }else{
                     $.ajax({
                        type: "GET",
                        url:  'ajax-currency-rate.php', 
                        data: "action=getLastRate&currencykey=" + selCurrencyObj.val() +"&trdate=" + trDateObj.val(),
                        beforeSend:function (xhr){ 
                            currencyRateObj.val(1); 
                        },
                        success: function(data){  
                                if(data){
                                     var data = JSON.parse(data);   
                                     currencyRateObj.val(data[0]['rate']).blur(); 
                                }
                        }  
                    });
                } 
               
           }
           
           this.onChangeCurrency = function onChangeCurrency(){ 
               
                var selCurrencyObj = tabObj.find("[name=selCurrency]");  
               
                tabObj.find(".mnv-active-currency").html(selCurrencyObj.find("option:selected").text());

                tabObj.find("[name='detailRate[]']").each(function(){
                  thisObj.calculateServiceDetail($(this));
                }); 
 
               thisObj.updateHeaderRate();
        }
    
        
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal(); ;  
          thisObj.rebindDownpayment();   
        }
            
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
         thisObj.updateDefaultDownpayment();
        } 
        
        this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(){
            //console.log("afterAddNewTemplateRowHandler");
            //thisObj.updateFieldOnChangeInvoiveDownpayment();
        } 
         
        this.rebindEl = function rebindEl(){   
            
             var handling = [];
             handling.onSelectFunction = 'getTabObj().updateDetail';
             handling.onChangeFunction = 'getTabObj().updateOnChange';
            
             var customerkey = tabObj.find("[name=hidCustomerKey]").val();
            
             bindAutoCompleteForTransactionDetail('salesOrderCode[]',objAndValueForDetailAutoComplete,'ajax-emkl-job-order.php?action=searchDataForInvoice&customerkey=' + customerkey,handling); 
             bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailItemAutoComplete,'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');
   			 bindAutoCompleteForTransactionDetail('invoiceCode[]',objAndValueForDetailInvoiceAutoComplete,'ajax-emkl-order-invoice.php?action=searchData&statuskey=2&customerkey=' + customerkey,thisObj.updateDetailForInvoice);
                         
             bindEl(tabObj.find("[name='selInvoiceType[]']"),'change', function() { thisObj.changeInvoiceType(this); });
             bindEl(tabObj.find("[name='amount[]'], [name='chkService[]'], [name='chkIsTax23[]']"),'change', function() { thisObj.calculateTotal(); });  
             bindEl(tabObj.find("[name='qtyDetail[]'], [name='detailRate[]']"),'change', function() { thisObj.calculateServiceDetail(this); });  
             bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });  
             bindEl(tabObj.find("[name='chkIsReimburse[]']"),'change',function(){ thisObj.updateReimburse(thisObj.getRowObj($(this))) }); 
			
            var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");   
            bindEl(tableDownPaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
            bindEl(tableDownPaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
 
 
            thisObj.rebindDownpayment();  
            
        } 
         
        this.updateRecordedCode = function updateRecordedCode(obj){ 
            var selCustomCodeObj = tabObj.find("[name=selCustomCode]");  
            thisObj.recordedCode[selCustomCodeObj.val()] = $(obj).val();
        }
        
        this.updateCode = function updateCode(obj){
 
                var customcodekey = $(obj).val();
                var codeObj = tabObj.find("[name=code]");
                var oldCode = '';

                $.ajax({
                    type: "GET",
                    url:  'ajax-custom-code.php',  
                    asyn: false,
                    data: 'action=getDataRowById&pkey=' + customcodekey, 
                    success: function(data){    

                        var data = JSON.parse(data);   
                        data = data[0];

                        oldCode = (thisObj.recordedCode[customcodekey]) ? thisObj.recordedCode[customcodekey] : '' ;
                        
                        // harus simpan kode sebelumnya... 
                        if(data.useautocode == 1){ 
                            var tempCode = (oldCode) ? oldCode : "[auto code]";
                            codeObj.val(tempCode);
                            codeObj.prop("readonly", true);
                        }else{ 
                            codeObj.val(oldCode);
                            codeObj.prop("readonly",false);
                        }
 
                    }  
                }); 
                
        }
        
		this.updateInvoiceAddress = function updateInvoiceAddress(obj){
            
            // khusus others
            if ($(obj).find("option:selected").val() == -999){
                tabObj.find("[name=invoiceAddress]").removeAttr("readonly");
                return;
            }
            
            tabObj.find("[name=invoiceAddress]").attr("readonly",'readonly');
            
			var selectedAddess = $('option:selected', obj).attr('rel-address') || ''; 
			
			if(selectedAddess == ''){ 
				tabObj.find("[name=invoiceAddress]").text('');
			}else{
				selectedAddess = decodeHTMLEntities(selectedAddess);
				tabObj.find("[name=invoiceAddress]").text(selectedAddess);
			}
			
		}
        
       this.getRowObj = function getRowObj(obj){
            return obj.closest(".div-table-row");
        }
        
           
        this.updateReimburse =  function updateReimburse(rowObj){ 
			var isReimburse = rowObj.find("[name='chkIsReimburse[]']").val() ;   
	  
            if (isReimburse == 1) {
                rowObj.find("[name='taxDetail[]']").val(0).attr("readonly", true).change();   
                rowObj.find("[name='taxDetail[]']").find("option:not(:selected)").attr('disabled', true);  
                
                rowObj.find("[name='dummychkIncludeTaxDetail[]']").prop("checked", false).attr("readonly", true);  
                rowObj.find("[name='dummychkIsTax23[]']").prop("checked", false).attr("readonly", true);
               
            } else {
                rowObj.find("[name='taxDetail[]']").find("option:not(:selected)").removeAttr('disabled');  
                rowObj.find("[name='taxDetail[]']").attr("readonly", false);  
                rowObj.find("[name='dummychkIncludeTaxDetail[]']").attr("readonly", false);  
                rowObj.find("[name='dummychkIsTax23[]']").attr("readonly", false);  
            }
        }

        this.loadOnReady = function loadOnReady(){ 
            tabObj.find("[name=selTermOfPayment]" ).change(function() {
            
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

               tabObj.find(".cashTOP").show();
            });    
        
            tabObj.find("[name=selCustomCode]" ).change(function() {
               tabObj.find("[name='salesOrderCode[]']").each(function() { 
                    detailRow = $(this).closest(".transaction-detail-row"); 
                    thisObj.updatePartialInvoiceOutstanding(detailRow); 
               })

               thisObj.calculateTotal();
            });
  
	         tabObj.find("[name=selTermOfPayment]" ).change();  
             tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); }); 
            
             //tabObj.find("[name=chkDownpayment]").change(function(){thisObj.updateInvoiceType(this)}) 
             tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
             tabObj.find("[name=chkTax23], [name=tax23Percentage]").change(function(){thisObj.calculateTax23()}); 
             tabObj.find("[name=otherCost], [name=beforeTaxTotal], [name=chkIncludeTax], [name=selFinalDiscountType], [name=taxPercentage], [name=finalDiscount]" ).change(function(){thisObj.calculateTotal()}) ;
             tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)});
               

            
             var selCurrencyObj = tabObj.find("[name=selCurrency]");
            
             selCurrencyObj.change(function(){thisObj.onChangeCurrency()})
             
             addNewTemplateRow("downpayment-row-template");  

             tabObj.find("[name='chkPick-master']").val(1).change();   
            // gk boleh selCurrency on change, karena nanti ketika load edit, currency yg kesimpen ke overwrite dengan kurs tengah terbaru
             //tabObj.find("[name=selCurrency]").change();
            
            tabObj.find(".mnv-active-currency").html(selCurrencyObj.find("option:selected").text()); 
            tabObj.find("[name=selInvoiceAddress]").change(function(){thisObj.updateInvoiceAddress(this)}); 

            thisObj.recordedCode[tabObj.find("[name=selCustomCode]").val()] = tabObj.find("[name=code]").val(); 
            tabObj.find("[name=selCustomCode]").change(function(){thisObj.updateCode(this)}); 
            tabObj.find("[name=selCustomCode]").change();
            
            tabObj.find("[name=code]").change(function(){thisObj.updateRecordedCode(this)});  
 	
            // hanya ketika add pertama kali
            if(tabObj.find("[name=hidId]").val() == 0){
               thisObj.updateHeaderRate();
            }
        
			thisObj.disabledAmount();
            thisObj.rebindEl(); 
  
        }
}
