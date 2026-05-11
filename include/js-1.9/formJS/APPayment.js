function APPayment(tabID, rs, varConstant, uploadFolder, rsFile){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.supplierTax = 0;
    this.tablekey = varConstant.TABLEKEY;  
    this.useStorage = varConstant.USE_STORAGE;   

    
     var fileFolder = uploadFolder;
     var fileUploaderTarget = "item-file-uploader"; 
     var arrFile = Array();  
    
     var id = tabObj.find("[name=hidId]").val();
    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});
    objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'}); 
    var objAndValueForDPDetailAutoComplete  = objAndValue; 
    
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
    var objAndValueForCostDetailAutoComplete  = objAndValue; 

    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidAPKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'});
    objAndValue.push({object:'refJOCode[]', value :'refcode2'});  
    objAndValue.push({object:'refInvoiceCode[]', value :'refinvoicecode'});  
    objAndValue.push({object:'apAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    
    var objAndValueForDetailAutoComplete = objAndValue;



    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction"));
        clearAllRows(tabObj.find(".mnv-downpayment"));
        clearAllRows(tabObj.find(".mnv-payment-method"));
        addNewTemplateRow("downpayment-row-template");  
        addNewTemplateRow("payment-method-row-template");
        thisObj.rebindDownpayment(); 
        thisObj.updateVoucher();
        thisObj.calculateTotal(); 
        thisObj.rebindAPCode();  

    }

    
     this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);
 
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
                
            }

            detailRow.find("[name=\"apCode[]\"]").first().val(ui.item['code']);
            //APPayment.updateDefaultDownpayment();
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
              
             var apkey = new Array();
             tabObj.find("[name='hidAPKey[]']").each(function(){ if($(this).val()) apkey.push($(this).val()); })
             var currencykey = tabObj.find("[name='selCurrency']").val();
              $.ajax({
                type: "GET",
                url:  'ajax-supplier-downpayment.php', 
                data: 'action=getDownpaymentForAP&apkey=' + JSON.stringify(apkey) +"&currencykey=" + currencykey, 
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
                    
                    
                    thisObj.rebindDownpayment();
                    tabObj.find("[name=\"downpaymentAmount[]\"]").change().blur();
                    thisObj.calculateTotalDownpayment();                         
                }  
            }); 
            
    } 

    this.updateTotalOutstandingDownpayment = function updateTotalOutstandingDownpayment(obj){
        var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val();
        var currencykey = tabObj.find("[name='selCurrency']").val();
        
        $.ajax({
            type: "GET",
            url:  'ajax-supplier-downpayment.php',
            async : false,
            beforeSend:function (xhr){  
                tabObj.find(".outstanding-currency").text($("[name=selCurrency] option:selected").first().text()); 
                tabObj.find(".outstanding-downpayment").text(0).blur();
            },
            data: "action=getTotalOutstanding&supplierkey=" + supplierkey +'&currencykey='+ currencykey,  
        }).done(function( data ) {
            
			data = parseJSON(data);
			if(data.length == 0) return;
            
            data = data[0];
//            tabObj.find(".outstanding-currency").text(data.currencyname); 
            tabObj.find(".outstanding-downpayment").text(data.totaldownpayment).blur();
        }); 
        
    }   
    
    this.calculateTotalDownpayment = function calculateTotalDownpayment(){
        var totalDP = 0; 
        tabObj.find("[name='downpaymentAmount[]']").each(function() { totalDP += parseFloat(unformatCurrency($(this).val())) || 0;   })
        tabObj.find("[name='totalDownpayment']").val(totalDP).blur(); 

        return totalDP;
     }

     this.calculateTotalCost = function calculateTotalCost(){
        var totalCost = 0; 
        tabObj.find("[name='costAmount[]']").each(function() { totalCost += parseFloat(unformatCurrency($(this).val())) || 0;   })
        tabObj.find("[name='totalCost']").val(totalCost).blur(); 

        return totalCost;
    } 

    this.calculateTotal = function calculateTotal(){

        var amount = 0;      
        var totalPPH = 0; 
        var totalDiscount = 0;
        var totalCost = 0;


        tabObj.find("[name='chkPick[]']").not(":disabled").each(function(){   

            if ($(this).val() != 1 )  return;

            var objAmount = $(this).closest(".div-table-row").find("[name='amount[]']");
            var objDiscount = $(this).closest(".div-table-row").find("[name='discount[]']");
            var objPph = $(this).closest(".div-table-row").find("[name='taxPPH[]']"); 

            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;
            totalDiscount += parseFloat(unformatCurrency(objDiscount.val())) || 0;
            totalPPH += parseFloat(unformatCurrency(objPph.val())) || 0;

        })     

        var totalPaid = amount + totalDiscount;
        tabObj.find("[name='totalPaid']").val(totalPaid).blur(); 


        tabObj.find("[name='totalDiscount']").val(totalDiscount).blur(); 
        tabObj.find("[name='pph23']").val(totalPPH).blur(); 

        var totalDP = thisObj.calculateTotalDownpayment();
        var totalCost = thisObj.calculateTotalCost(); 
        var total = amount-totalPPH-totalDP+totalCost; 
        tabObj.find("[name='total']").val(total).blur(); 



        var totalPayment = 0; 
        tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseFloat(unformatCurrency($(this).val())) || 0; })   
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();
         
        var balance = totalPayment - total ;    
		balance = Math.round((balance + Number.EPSILON) * 100) / 100 
        tabObj.find("[name='balance']").val(balance).blur(); 
        
        /*  if (thisObj.rs && thisObj.rs.statuskey > 1){ 
                autoAddNewRowTemplate(tabObj.find("[name='paymentMethodValue[]']"),"payment-method-row-template");
                autoAddNewRowTemplate(tabObj.find("[name='costAmount[]']"),"cost-row-template");
                thisObj.rebindCost();
                autoAddNewRowTemplate(tabObj.find("[name='downpaymentAmount[]']"),"downpayment-row-template");
                thisObj.rebindDownpayment();
          }*/
    }
    
    this.rebindDownpayment = function rebindDownpayment(){ 
        var supplierkey = tabObj.find("[name=hidSupplierKey]").val() || 0;  
        bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete,'ajax-supplier-downpayment.php?action=searchData&supplierkey='+supplierkey+'&currencykey='+ tabObj.find("[name=selCurrency]").val());  
    } 
    
    this.rebindAPCode = function rebindAPCode(){
        bindAutoCompleteForTransactionDetail('apCode[]',  objAndValueForDetailAutoComplete,'ajax-ap.php?action=searchData&supplierkey=' + tabObj.find("[name=hidSupplierKey]").val()+'&currencykey='+ tabObj.find("[name=selCurrency]").val(),thisObj.updateDetail); 
    } 
    
        
    this.updateVoucher = function updateVoucher(){ 
		// kalo gk pake voucher, gk usah
		if(!varConstant.ADV_FINANCE) return;
		
        var supplierkey = tabObj.find("[name=hidSupplierKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name='selVoucher[]']");
            
        var ajaxData = "action=getAvailableVoucher&creditType=-1&supplierkey=" + tabObj.find("[name=hidSupplierKey]").val(); 
         $.ajax({
            type: "GET",
            url:  'ajax-cash-bank.php',
            async : false,
            beforeSend:function (xhr){
                  selVoucherObj.each(function(){  $('option', $(this)).remove();  }) 
            },
            data: ajaxData,
            success: function(data){ 
                        // update combobox services 
                        if(!data) return; 
                        data = JSON.parse(data); 
                        var selectOpt = data;
                        reInsertSelectBox(selVoucherObj,selectOpt, {"key" : "pkey", "label" : "voucherlabel", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 
    }

    this.rebindCost = function rebindCost(){  
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForCostDetailAutoComplete,'ajax-cost-cash-out.php?action=searchData'); 
    } 

    this.importData = function importData(){ 

        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        var refkey = tabObj.find("[name=hidRefKey]").val() || 0; 
        
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 

        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
        
        var ajaxData = "action=searchData&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&supplierkey=" + tabObj.find("[name=hidSupplierKey]" ).val()+'&&warehousekey=' + tabObj.find("[name=selWarehouseKey]").val()+"&refkey2="+refkey+dateParam;
        
        $.ajax({
            type: "GET",
            url:  'ajax-ap.php',
            beforeSend:function (xhr){ 
                // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++; 
            },
            data: ajaxData,
            success: function(data){ 
                    var data = JSON.parse(data);  
                    var i;
                    for(i=0;i<data.length;i++){  
                        
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidAPKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"apCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"refCode", "value":data[i].refcode}); 
                            arrPostValue.push({"selector":"refJOCode", "value":data[i].refcode2});  
                            arrPostValue.push({"selector":"refInvoiceCode", "value":data[i].refinvoicecode});  
                            arrPostValue.push({"selector":"apAmount", "value":data[i].amount}); 
                            arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding}); 
                            arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                          
                            // var tax = ( data[i].autotax == 1 && thisObj.supplierTax > 0) ?  data[i].amount *  thisObj.supplierTax / 100 : 0;  
                            // arrPostValue.push({"selector":"taxPPH", "value":tax}); 
                            //arrPostValue.push({"selector":"taxPPH", "value":data[i].tax23value}); // gk ad dari query sql nya
                            addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                    }

                   thisObj.rebindEl(); 

                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

                decreaseActiveAjaxConnections(thisObj); 
                tabObj.find("[name='chkPick-master']").val(1).change(); 
                thisObj.updateDefaultDownpayment();
            } ,
             error: function(xhr, errDesc, exception) {
                 decreaseActiveAjaxConnections(thisObj); 
            }
        }); 
    }
 

    this.updateSupplierInformation = function updateSupplierInformation(event, ui){
            var obj = this; 
            if (tabObj.find("[name=hidCurrentSupplierKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah pemasok akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Pemasok", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidSupplierKey]" ).val(tabObj.find("[name=hidCurrentSupplierKey]" ).val());
                        tabObj.find("[name=supplierName]" ).val(tabObj.find("[name=hidCurrentSupplierName]" ).val());
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        thisObj.rebindEl(); 
                  },
                  buttons : {
                      OK : function (){  
                             if (ui.item == null) { 
                                clearAutoCompleteInput(obj,'hidSupplierKey');	
                                tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                                tabObj.find("[name=hidCurrentSupplierName]" ).val(''); 
                             }else{
                                tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                                tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value);  
                             } 
 
                            thisObj.resetDetails(); 
                            thisObj.updateTax(true); 
                            thisObj.updateTotalOutstandingDownpayment();  
                          
                            $( this ).dialog( "close" );
                      },
                      Cancel : function (){  
                            $( this ).dialog( "close" );
                      }
                  },
                });	 
            }else{ 
                 if (ui.item == null) {
                    clearAutoCompleteInput(obj,'hidSupplierKey');	
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value); 
                     
                 } 	
                
                 thisObj.updateVoucher();
                 thisObj.rebindEl(); 
                 thisObj.updateTotalOutstandingDownpayment();
                          
            } 	 

            thisObj.updateTax(true);
            thisObj.rebindDownpayment();
    }

    this.updateTax = function updateTax(updateWithholdingTax){
         if (typeof updateWithholdingTax === 'undefined') updateWithholdingTax = false;
        
         $.ajax({
                type: "GET",
                url:  'ajax-supplier.php', 
                async : false,
                data: 'action=getTaxInformation&pkey=' + tabObj.find("[name=hidSupplierKey]").val() , 
                success: function(data){  
                    thisObj.supplierTax = 0; 
                         
                    data = parseJSON(data);
                    if(data.length == 0) return;
                    
                    //if (!data) return;
                    //data = JSON.parse(data);  
                    
                    thisObj.supplierTax = data.taxpercentage;      
                     
                    if(updateWithholdingTax){ 
                        //console.log(data.pphtype)
                        // harusnya yg kupdateSelectBoxSelectedValeupdate cuma yg di template-row,. karena sebelumyna sudah dihapus dulu semua baris AP
                        var selPPHObj = tabObj.find("[name='selPPhType[]']"); 
                        selPPHObj.each(function(){  
                                updateSelectBoxSelectedVal($(this), data.pphtype); 
                       });
                        
                    }
                }  
            }); 
    }
    
    
    this.updateCurrency = function updateCurrency(){

        // perlu handle manual readonlynya, karena default main.js selalu panggil onChangeCurrency
        var  currencyObj = tabObj.find("[name=selCurrency]");
        var currencyRateObj = tabObj.find("[name=currencyRate]"); 
     
       $( "#dialog-message" ).html("Apakah Anda ingin mengganti mata uang untuk pemasok ini ?<br>Semua detail transaksi akan dihapus jika Anda mengganti mata uang.");
        $( "#dialog-message" ).dialog({
          width: 300,
          modal: true,
          title:"Konfirmasi Perubahan Data mata uang", 
          close:function() {
                currencyObj.val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val());  
                currencyRateObj.val(tabObj.find("[name=hidCurrentCurrencyRate]" ).val()).blur(); 
                currencyRateObj.prop("readonly", (currencyObj.val() == 1) ? true:false ); 
          }, 
          open: function() {
              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
          }, 
          buttons : {
              OK : function (){    

                    tabObj.find("[name=hidCurrentCurrencyKey]" ).val(currencyObj.val()); 

                    thisObj.resetDetails();
                    $( this ).dialog( "close" ); 
                    thisObj.onChangeCurrency();
                    
                  
              },
              Cancel : function (){  
                    currencyObj.val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val()); 
                    currencyRateObj.val(tabObj.find("[name=hidCurrentCurrencyRate]" ).val()).blur(); 
                    currencyRateObj.prop("readonly", (currencyObj.val() == 1) ? true:false );
                    $( this ).dialog( "close" );  
              }
          } 

        });	  
          
    }
        
    this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    }

    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
        thisObj.calculateTotal(); 
                
        thisObj.rebindDownpayment();  
        thisObj.rebindCost();
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
     thisObj.calculateTotal(); 
    }

    this.onChangeCurrency = function onChangeCurrency(){
            
            var selCurrencyObj = tabObj.find("[name='selCurrency']")
            var currencyRateObj =  tabObj.find("[name='currencyRate']");
              
            var changeFlag = false;
            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){ 
                changeFlag = true;
                currencyRateObj.val(1);
            }
             
        
            currencyRateObj.prop("readonly", changeFlag);
            tabObj.find(".active-currency").html(selCurrencyObj.find("option:selected").text());
            
            // dipisah agar dapat dipanggil ketika onload tanpa pengaruh ke nilai rate dll  
            currencyRateObj.change().blur();
             
        
            var trDateObj =  tabObj.find("[name='trDate']"); 
            $.ajax({
                        type: "GET",
                        url:  'ajax-currency-rate.php', 
                        data: "action=getLastRate&currencykey=" + selCurrencyObj.val()+"&trdate=" + trDateObj.val(), 
                        beforeSend:function (xhr){ 
                            currencyRateObj.val(1); 
                        },
                        success: function(data){  
                                if(data){
                                     var data = JSON.parse(data);   
                                     currencyRateObj.val(data[0]['rate']).blur();
                                    
                                    tabObj.find("[name=hidCurrentCurrencyRate]" ).val(currencyRateObj.val()).blur();       
                                }
                        }  
            });
            thisObj.updateNumberDecimal();
            thisObj.updateTotalOutstandingDownpayment(); 
    }
    
    this.updateNumberDecimal = function updateNumberDecimal(){    
        var selCurrencyObj = tabObj.find("[name='selCurrency']");
        var isNumber = (selCurrencyObj.val() == varConstant.CURRENCY.idr) ? true : false; 
        changeNumberDecimal(tabObj.find("[name='arAmount[]'],[name='outstanding[]'],[name='discount[]'],[name='amount[]'],[name='taxPPH[]'],[name='totalReceived'],[name='totalDiscount'],[name='pph23'],[name='totalDownpayment'],[name='downpaymentAmount[]'],[name='totalCost'],[name='costAmount[]'],[name='total'],[name='totalPayment'],[name='paymentMethodValue[]'] ,[name='balance']"),isNumber); 
    }

    this.onChangeVoucher = function onChangeVoucher(obj){
        var amount = obj.find('option:selected').attr('rel-amount') || 0; 
        obj.closest(".transaction-detail-row").find("[name=\"paymentMethodValue[]\"]").val(amount).change().blur(); 
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});
    }

    this.showRetailPurchase = function showRetailPurchase(obj){
        var actionPanel = $(obj).closest(".row-action-panel");
        var transactionDetailRow = $(obj).closest(".transaction-detail-row");
        
        var apkey = transactionDetailRow.find('[name=\'hidAPKey[]\']').val(); //actionPanel.attr("rel-key"); 
        var loaded =  actionPanel.attr("rel-load"); 
        
        var panel = transactionDetailRow.find(".transaction-reference-panel");
        panel.show();
        
        if(loaded == 1) return;
    
         $.ajax({
                type: "GET",
                url:  'ajax-purchase-order.php', 
                data: 'action=getDetailByAP&apkey=' + apkey, 
                beforeSend: function (){
                  panel.append('<div class="loading-'+apkey+'">'+_LOADING_ICON_+'</div>');
                },
                success: function(data){  
                    
                    if(!data) return;
                    
                    var data = JSON.parse(data);  
                    var template = panel.find(".reference-row-template");
                    var i; 
                     
                    for(i=0;i<data.length;i++){  
                        $newRow = template.clone().removeClass("reference-row-template").insertBefore(template.first());
                        $newRow.find('.purchase-number-row').html((i+1)+'.');
                        $newRow.find('.purchase-item').html(data[i]['itemname']);
                        $newRow.find('.purchase-qty').html(data[i]['qty']);
                        $newRow.find('.purchase-unit').html(data[i]['unitname']);
                        $newRow.find('.purchase-price').html(data[i]['priceinunit']);
                        $newRow.find('.purchase-subtotal').html(data[i]['total']); 
                    }   
                    
                    panel.find('.is-number').formatCurrency();
                    panel.find('.loading-'+apkey).remove();
                    actionPanel.attr("rel-load",1);
                }  
            }); 
        
    }
    
    this.showRefDetail = function showRefDetail(obj){ 
        
        // kalo dari retail, sementara asumsi retail semua 
        if($(obj).attr('rel-action') == 'show'){ 
            thisObj.showRetailPurchase(obj);
        }else{ 
            $(obj).closest(".transaction-detail-row").find(".transaction-reference-panel").hide();
        }
        $(obj).closest(".row-action-panel").find('.row-show-detail').toggleClass('hide'); 
    }
    
    // ruben
    // this.rebindEl = function rebindEl(){
    //     var currencykey = tabObj.find("[name='selCurrency']").val();
    //     bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });
    //     bindEl(tabObj.find("[name='discount[]'], [name='amount[]'], [name='taxPPH[]']"),'change', function() { thisObj.calculateTotal(); });
    //     bindAutoCompleteForTransactionDetail('apCode[]',  objAndValueForDetailAutoComplete,'ajax-ap.php?action=searchData&supplierkey=' + tabObj.find("[name=hidSupplierKey]").val()+"&currencykey=" + currencykey+'&warehousekey=' + tabObj.find("[name=selWarehouseKey]").val(),thisObj.updateDetail);
        
    //     var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");
    //     bindEl(tableDownPaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
    //     bindEl(tableDownPaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
 
    //     var tableCostDetail = tabObj.find(".mnv-cost");
    //     bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
    //     bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
  
    //     bindEl($('.row-show-detail'),'click',function(){ thisObj.showRefDetail(this) });
  
        
    //     thisObj.rebindDownpayment();
    //     thisObj.rebindCost();
    //     thisObj.updateTax();
    // }

    this.updateDocumentFiles = function updateDocumentFiles(objButton){
			 
			  var pkey = tabObj.find('[name=hidId]').val();
			  var token = tabObj.find('[name=token-item-file-uploader]').val();
			  var fileName = tabObj.find('[name=item-file-uploader]').val();
			
			  if (parseInt(pkey) == 0 || pkey == '' || parseInt(token) == 0|| token == '') 
					return;
			 
			  $.ajax({
                        type: "POST",
                        url:  'ajax-ap-payment.php', 
                        async : false,
                        data: 'action=updateDocumentFiles&pkey=' + pkey +'&token-item-file-uploader=' + token + '&item-file-uploader=' + fileName, 
                        success: function(data){    
                            data = parseJSON(data);
							
							if(data.length == 0) return; 
							
							alert(data[0].message);
							
							if(data[0].valid == false){
//								alert(data[0].message)
							}else{
								objButton.hide();
								objButton.closest("div").find(".file-uploader").hide();
							}
                        }  
                    }); 
		}

    this.rebindEl = function rebindEl(){ 
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='discount[]'], [name='amount[]'], [name='taxPPH[]']"),'change', function() { thisObj.calculateTotal(); });  
        
        thisObj.rebindAPCode();  
        
        var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");   
        bindEl(tableDownPaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
        bindEl(tableDownPaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
 
        var tableCostDetail = tabObj.find(".mnv-cost");   
        bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
        bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
  
        // utk voucher kepake, biar bisa auto add new row
        var tablePaymentMethodDetail = tabObj.find(".mnv-payment-method");   
        bindEl(tablePaymentMethodDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
        bindEl(tablePaymentMethodDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
  
        thisObj.rebindDownpayment();  
        thisObj.rebindCost();
        
        thisObj.updateTax(); 
    }

    

    this.loadOnReady = function loadOnReady(){ 

        if(thisObj.useStorage){
            
        }else{ 
            if(id){   
                for($i=0;$i<rsFile.length;$i++) 
                arrFile.push(rsFile[$i].file); 

                createFileUploader(fileUploaderTarget, fileFolder, id, arrFile, true); 
            }else{ 
                createFileUploader(fileUploaderTarget,fileFolder, "" , "",true); 
            }

        }
        
	   tabObj.find("[name=selTermOfPayment]" ).change();  

        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
        tabObj.find("[name=btnAddPayment]").bind( "click", function(event) {  thisObj.onCLickAddPayment(); }) 
         
        tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]");
            
            dateObj.removeClass("force-readonly");
             
            dateObj.datepicker((checked) ? "enable" : "disable"); 
            
            if(!checked) dateObj.addClass("force-readonly");
         })  
        
        tabObj.find("[name=chkDatePeriod]").change();
        tabObj.find("[name=selCurrency]").change(function() { thisObj.updateCurrency();  });
        tabObj.find("[name=selWarehouseKey]").change(function() { thisObj.rebindEl(); }); // agar kereset parameter utk ajax
        
        tabObj.find("[name=trStartDate], [name=trEndDate]").bind( "change",function() { 
            var trStartDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val()));
            var trEndDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val()));
               
            if (trStartDate > trEndDate) 
                tabObj.find("[name=trEndDate]").val(tabObj.find("[name=trStartDate]").val()); 
            
		});

        tabObj.find("[name=btnUpdateFile]").click(function() { thisObj.updateDocumentFiles($(this)); });
         
        thisObj.updateTotalOutstandingDownpayment();
        $(".outstanding-downpayment").blur(); // utk pertama kali, agar ad decimal atau tdk
        
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});
        tabObj.find("[name='chkPick-master']").val(1).change(); 
        addNewTemplateRow("downpayment-row-template");  
        addNewTemplateRow("cost-row-template");  
        
         
         tabObj.find('[name=btnSubmitFileAjax]').on('click', function () { onSubmitFileAjax(tabObj,
                                                                                            {ajaxFile: 'ajax-ap-payment.php'}
                                                                                           );});
        
        thisObj.rebindEl(); 
         
    }
}
