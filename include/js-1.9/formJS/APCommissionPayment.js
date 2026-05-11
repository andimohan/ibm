function APCommissionPayment(tabID, rs, varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);       
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.supplierTax = 0;
    this.tablekey = varConstant.tablekey;   
    this.useWarehousePrivilages = varConstant.useWarehousePrivilages; 
    this.apCommissionPaymentRate = varConstant.apCommissionPaymentRate;  
      
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
    objAndValue.push({object:'apAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    objAndValue.push({object:'hidAPRate[]', value :'rate'});
    var objAndValueForDetailAutoComplete = objAndValue;



    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction"));
        clearAllRows(tabObj.find(".mnv-downpayment"));
        
        addNewTemplateRow("downpayment-row-template");  
        thisObj.rebindDownpayment(); 
        
        thisObj.updateVoucher();
        thisObj.calculateTotal(); 
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

            thisObj.updateRateFromAP();
    }
	 
	       
    this.updateVoucher = function updateVoucher(){ 
		// kalo gk pake voucher, gk usah
		if(!varConstant.ADV_FINANCE) return;
		
        var supplierkey = tabObj.find("[name=hidSupplierKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name='selVoucher[]']");
            
        var ajaxData = "action=getAvailableVoucher&supplierkey=" + tabObj.find("[name=hidSupplierKey]").val(); 
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
                        //if(!data) return; 
                        //data = JSON.parse(data); 
                
                        data = parseJSON(data);
                        if(data.length == 0) return;

                        var selectOpt = data;
                        reInsertSelectBox(selVoucherObj,selectOpt, {"key" : "pkey", "label" : "voucherlabel", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 
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
         //
        //console.log("totalPayment " + totalPayment);
        //console.log("total " + total);
        //console.log("totalPayment - total = " + (totalPayment-total));
        // ad case
        //totalPayment 363.65
        //total 363.65000000000003
        //totalPayment - total = -5.684341886080802e-14
        
        var balance = totalPayment - total;  
        if (Math.abs(balance) < EPSILON) balance = 0;
        
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
        
    this.rebindCost = function rebindCost(){  
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForCostDetailAutoComplete,'ajax-cost-cash-out.php?action=searchData'); 
    } 

    this.importData = function importData(){ 

        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 

        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
        
		
		var warehouseCriteria = (this.useWarehousePrivilages) ?  '&warehousekey= ' + tabObj.find("[name=selWarehouseKey]").val() : '';
        var ajaxData = "action=searchData&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&supplierkey=" + tabObj.find("[name=hidSupplierKey]" ).val()+warehouseCriteria+dateParam;
        
        $.ajax({
            type: "GET",
            url:  'ajax-ap-commission.php',
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
                            arrPostValue.push({"selector":"apAmount", "value":data[i].amount}); 
                            arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding}); 
                            arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                            arrPostValue.push({"selector":"hidAPRate", "value":data[i].rate}); 
                          
                            var tax = ( data[i].autotax == 1 && thisObj.supplierTax > 0) ?  data[i].amount *  thisObj.supplierTax / 100 : 0;  
                            arrPostValue.push({"selector":"taxPPH", "value":tax}); 
                         
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
 
            				thisObj.updateTax();
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
                    clearAutoCompleteInput(obj,'hidSupplierKey');	
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value); 
                     
                 } 	

            	 thisObj.updateTax();
                 thisObj.updateVoucher();
                 thisObj.rebindEl(); 
            } 	 

            thisObj.rebindDownpayment(); 
    }

    this.updateTax = function updateTax(){
         $.ajax({
                type: "GET",
                url:  'ajax-supplier.php', 
                async : false,
                data: 'action=getTaxInformation&pkey=' + tabObj.find("[name=hidSupplierKey]").val() , 
                success: function(data){  
                    thisObj.supplierTax = 0;
                    
                    //if (!data) return; 
                    //var data = JSON.parse(data);  
                    
                    data = parseJSON(data);
                    if(data.length == 0) return;

                    thisObj.supplierTax = data.taxpercentage;   
					
                    tabObj.find("[name=taxId]" ).val(data.taxid); 
                }  
            }); 
    }
    
    
    this.updateCurrency = function updateCurrency(){


       $( "#dialog-message" ).html("Apakah Anda ingin mengganti mata uang untuk pemasok ini ?<br>Semua detail transaksi akan dihapus jika Anda mengganti mata uang.");
        $( "#dialog-message" ).dialog({
          width: 300,
          modal: true,
          title:"Konfirmasi Perubahan Data mata uang", 
          close:function() {
                tabObj.find("[name=selCurrency]").val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val()); 
          }, 
          open: function() {
              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
          }, 
          buttons : {
              OK : function (){    

                    tabObj.find("[name=hidCurrentCurrencyKey]" ).val(tabObj.find("[name=selCurrency]" ).val());  

                    thisObj.resetDetails();
                   $( this ).dialog( "close" );
              },
              Cancel : function (){  
                    tabObj.find("[name=selCurrency]").val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val()); 
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
            var useAPCommissionPaymentRate = (varConstant.apCommissionPaymentRate == 2) ? true : false;
              
            var changeFlag = false;
            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){ 
                changeFlag = true;
                currencyRateObj.val(1);
            } else if(useAPCommissionPaymentRate && (selCurrencyObj.val() != varConstant.CURRENCY.idr)){
                changeFlag = true;
            }
             
            currencyRateObj.prop("readonly", changeFlag);
            tabObj.find(".active-currency").html(selCurrencyObj.find("option:selected").text());
            
            // dipisah agar dapat dipanggil ketika onload tanpa pengaruh ke nilai rate dll  
            currencyRateObj.change().blur();
             
            if(thisObj.apCommissionPaymentRate == 2) {
                    thisObj.updateRateFromAP();
            }else{
                    $.ajax({
                        type: "GET",
                        url:  'ajax-currency-rate.php', 
                        data: "action=getLastRate&currencykey=" + selCurrencyObj.val(),  
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
          
    };

    this.updateRateFromAP =  function updateRateFromAP(){
        var row = tabObj.find(".transaction-detail-row");

        var selCurrency = tabObj.find("[name='selCurrency']").val();
        var currencyRateObj =  tabObj.find("[name='currencyRate']");

        if((thisObj.apCommissionPaymentRate == undefined) || (thisObj.apCommissionPaymentRate != 2) || (selCurrency == varConstant.CURRENCY.idr)) return;

        var apRate = tabObj.find("[name='hidAPRate[]']").first().val();
        currencyRateObj.val(apRate).blur();
        
        
        //var currencyRateObj =  tabObj.find("[name='currencyRate']");
        //var apkey = row.find("[name='hidAPKey[]']").first().val();

        //$.ajax({
        //    type: "GET",
        //    url:  'ajax-ap-commission.php', 
        //    data: "action=getDataRowById&pkey="+apkey,  
        //    beforeSend:function (xhr){ 
        //    },
        //    success: function(data){  
        //            //data = parseJSON(data); 
        //                            
        //            data = parseJSON(data);
        //            if(data.length == 0) return;
//
//
        //            if(data.length == 0) {
        //                currencyRateObj.prop("readonly", false);
        //                return;
        //            }
        //            currencyRateObj.val(data[0]['rate']).blur();
//
        //    }  
        //});

    }
    
    
    this.rebindEl = function rebindEl(){ 
        var currencykey = tabObj.find("[name='selCurrency']").val();
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='discount[]'], [name='amount[]'], [name='taxPPH[]']"),'change', function() { thisObj.calculateTotal(); });  
		
		
		var warehouseCriteria = (this.useWarehousePrivilages) ?  '&warehousekey= ' + tabObj.find("[name=selWarehouseKey]").val() : '';
		
        bindAutoCompleteForTransactionDetail('apCode[]',  objAndValueForDetailAutoComplete,'ajax-ap-commission.php?action=searchData&supplierkey=' + tabObj.find("[name=hidSupplierKey]").val()+"&currencykey=" + currencykey+warehouseCriteria,thisObj.updateDetail); 
        
        var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");   
        bindEl(tableDownPaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
        bindEl(tableDownPaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
 
        var tableCostDetail = tabObj.find(".mnv-cost");   
        bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
        bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
  
        thisObj.rebindDownpayment();  
        thisObj.rebindCost();
    }

    this.loadOnReady = function loadOnReady(){ 
 
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
        tabObj.find("[name=selCurrency]").change(function() { thisObj.updateCurrency(); });
        tabObj.find("[name=selWarehouseKey]").change(function() { thisObj.rebindEl(); }); // agar kereset parameter utk ajax
        
        tabObj.find("[name=trStartDate], [name=trEndDate]").bind( "change",function() { 
            var trStartDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val()));
            var trEndDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val()));
               
            if (trStartDate > trEndDate) 
                tabObj.find("[name=trEndDate]").val(tabObj.find("[name=trStartDate]").val()); 
            
		}); 
        
        tabObj.find(".mnv-transaction .remove-button").on('click', function() {
            removeDetailRows(this);  
            thisObj.updateRateFromAP();
        });   


        tabObj.find("[name=selCurrency]").change(function() { thisObj.onChangeCurrency();});
         
        tabObj.find("[name='chkPick-master']").val(1).change(); 
        addNewTemplateRow("downpayment-row-template");  
        addNewTemplateRow("cost-row-template");  
        
        
        //utk overwrite ulang bawaan selCurrency
        var useAPCommissionPaymentRate = (varConstant.apCommissionPaymentRate == 2) ? true : false;
        if(useAPCommissionPaymentRate){
            tabObj.find("[name='currencyRate']").prop("readonly", true);
        }
        
        thisObj.rebindEl(); 
         
    }
}