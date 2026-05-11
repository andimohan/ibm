function CustomerDownpaymentSettlement(tabID,tablekey, rs,varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.tablekey = tablekey; 
    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.customerTax = 0;

    
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
    var objAndValueForCostDetailAutoComplete  = objAndValue; 

    var  objAndValue = new Array;
    objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});   
    //objAndValue.push({object:'refCode[]', value :'refcode'});
    objAndValue.push({object:'dpCode[]', value :'code'});
    objAndValue.push({object:'dpAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    var objAndValueForDetailAutoComplete = objAndValue;

    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction"));
        clearAllRows(tabObj.find(".mnv-payment-method"));
        
        addNewTemplateRow("payment-method-row-template");  
        
        //thisObj.rebindDownpaymentCode();  
        thisObj.updateVoucher(); 
        thisObj.calculateTotal(); 
    }

    this.updateVoucher = function updateVoucher(){ 
		
		// kalo gk pake voucher, gk usah
		if(!varConstant.ADV_FINANCE) return;
		
        var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name='selVoucher[]']");
            
        var ajaxData = "action=getAvailableVoucher&customerkey=" + tabObj.find("[name=hidCustomerKey]").val();  
      
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
     
    this.updateDPType = function updateDPType(){
            var selType = tabObj.find("[name=selDPSettlementType]");  
            var costObj = tabObj.find(".mnv-cost");
            var paymentObj = tabObj.find(".mnv-payment-method");
            var coaObj = tabObj.find(".mnv-coa");
            var transactionType = selType.val(); 
 
            if (transactionType == 1){ 
                paymentObj.show();
                coaObj.hide();
            } else{ 
                paymentObj.hide(); 
                coaObj.show();
            }
    
        thisObj.updateVoucher(); 
        thisObj.calculateTotal(); 
    }

     this.calculateTotalCOA = function calculateTotalCOA(){
        var totalCOA = 0; 

        var totalCOA = parseFloat(unformatCurrency(tabObj.find("[name='subtotalCOA']").val())) || 0;
        tabObj.find("[name='totalCOA']").val(totalCOA).blur();
         
        return totalCOA;
    } 

     this.calculateTotalCost = function calculateTotalCost(){
        var totalCost = 0; 
        tabObj.find("[name='costAmount[]']").each(function() { totalCost += parseFloat(unformatCurrency($(this).val())) || 0;   })
        tabObj.find("[name='totalCost']").val(totalCost).blur(); 

        return totalCost;
    } 

    this.calculateTotal = function calculateTotal(){

        var amount = 0;      

        var totalCost = 0;
        var selType = tabObj.find("[name=selDPSettlementType]").val();  


        tabObj.find("[name='chkPick[]']").not(":disabled").each(function(){   

            if ($(this).val() != 1 )  return;

            var objAmount = $(this).closest(".div-table-row").find("[name='amount[]']");

            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })     

        var totalReceived = amount ;
        tabObj.find("[name='totalReceived']").val(totalReceived).blur(); 



        var totalCost = thisObj.calculateTotalCost();
         
        var total = amount-totalCost; 
        tabObj.find("[name='total']").val(total).blur();

        var totalPayment = 0; 
        tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseFloat(unformatCurrency($(this).val())) || 0; })   
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();

        var totalCOA = thisObj.calculateTotalCOA();
        var balance = 0;
        
        
        if (selType == 1){ 
            balance = totalPayment - total; 
        }else{
            balance = totalCOA - total; 
        }
        
        balance = Math.round(balance * 100) / 100
        tabObj.find("[name='balance']").val(balance).blur();  
    }

        
    this.rebindDownpaymentCode = function rebindDownpaymentCode(){
        bindAutoCompleteForTransactionDetail('dpCode[]',  objAndValueForDetailAutoComplete,'ajax-customer-downpayment.php?action=searchData&customerkey=' + tabObj.find("[name=hidCustomerKey]").val()+'&currencykey='+ tabObj.find("[name=selCurrency]").val(),thisObj.updateDetail); 
    } 
     
    this.rebindCost = function rebindCost(){   
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForCostDetailAutoComplete,'ajax-cost-cash-out.php?action=searchData'); 
    } 
     
    this.importData = function importData(){ 

        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        //var refkey = tabObj.find("[name=hidRefKey]").val() || 0; 
        
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
 
        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
                 
        var ajaxData = "action=searchData&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&customerkey=" + tabObj.find("[name=hidCustomerKey]").val()+dateParam;  
          
        $.ajax({
            type: "GET",
            url:  'ajax-customer-downpayment.php',
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
                            arrPostValue.push({"selector":"hidDownpaymentKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"dpCode", "value":data[i].value});  
                            arrPostValue.push({"selector":"dpAmount", "value":data[i].amount}); 
                            arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding});  
                            arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                            
                            addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                    }
            
                thisObj.rebindEl(); 
 
                // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

                decreaseActiveAjaxConnections(thisObj); 
                tabObj.find("[name='chkPick-master']").val(1).change(); 
            } ,
             error: function(xhr, errDesc, exception) {
                 decreaseActiveAjaxConnections(thisObj); 
            }
        }); 
    }
 
 
    
    this.updateCustomerInformation = function updateCustomerInformation(event, ui){
            var obj = this; 
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
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        
                        thisObj.rebindEl(); 
                  },
                  buttons : {
                      OK : function (){  
                             if (ui.item == null) { 
                                clearAutoCompleteInput(obj,'hidCustomerKey');	
                                tabObj.find("[name=hidCurrentCustomerKey]" ).val(''); 
                                tabObj.find("[name=hidCurrentCustomerName]" ).val(''); 
                             }else{
                                tabObj.find("[name=hidCurrentCustomerKey]" ).val(ui.item.pkey); 
                                tabObj.find("[name=hidCurrentCustomerName]" ).val(ui.item.value);  
                             } 
 
                            thisObj.resetDetails(); 
                            thisObj.updateVoucher(); 

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
                 }else{ 
                    tabObj.find("[name=hidCurrentCustomerKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentCustomerName]" ).val(ui.item.value); 
                 } 	 

                thisObj.updateVoucher(); 
                thisObj.rebindEl(); 
                //thisObj.updateTax(); // udah ad di rebindEl
            } 	  

    }

    
    this.updateCurrency = function updateCurrency(){ 
       $( "#dialog-message" ).html("Apakah Anda ingin mengganti mata uang untuk pelanggan ini ?<br>Semua detail transaksi akan dihapus jika Anda mengganti mata uang.");
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

    this.onChangeVoucher = function onChangeVoucher(obj){
        var amount = obj.find('option:selected').attr('rel-amount') || 0; 
        obj.closest(".transaction-detail-row").find("[name=\"paymentMethodValue[]\"]").val(amount).change().blur(); 
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});
    }
    
    this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    }

    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
        thisObj.calculateTotal(); 
          
        // gk boleh kereset harusnya, data yang sudah keisi jd kereset jg
        
        thisObj.rebindCost();
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
     thisObj.calculateTotal(); 
    }


    this.rebindEl = function rebindEl(){ 
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find(" [name='amount[]']"),'change', function() { thisObj.calculateTotal(); });  
        
        thisObj.rebindDownpaymentCode();  

        var tableCostDetail = tabObj.find(".mnv-cost");   
        bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
        bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
  
        // gk boleh pake onChangePaymentMethodHandler, jadi error nambah baris payment, karena ini bukan detail
        var tableCOADetail = tabObj.find(".mnv-coa");       
        bindEl(tableCOADetail.find('[name=subtotalCOA]'),'change',function(){ thisObj.calculateTotal(); });

        // utk voucher kepake, biar bisa auto add new row
        var tablePaymentMethodDetail = tabObj.find(".mnv-payment-method");   
        bindEl(tablePaymentMethodDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
        bindEl(tablePaymentMethodDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
  
        thisObj.rebindCost();
        
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
                                }
                        }  
                    });
       
       thisObj.updateNumberDecimal();
    }
   
    this.updateNumberDecimal = function updateNumberDecimal(){    
        var selCurrencyObj = tabObj.find("[name='selCurrency']");
        var isNumber = (selCurrencyObj.val() == varConstant.CURRENCY.idr) ? true : false; 
        changeNumberDecimal(tabObj.find("[name='dpAmount[]'],[name='outstanding[]'],[name='amount[]'],[name='totalReceived'],[name='totalCost'],[name='costAmount[]'],[name='total'],[name='totalPayment'],[name='paymentMethodValue[]'] ,[name='balance']"),isNumber); 
    }

    
    this.loadOnReady = function loadOnReady(){ 
 
       thisObj.updateNumberDecimal(); 
        
	   tabObj.find("[name=selTermOfPayment]" ).change();  

        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
         
        tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]");
             
            dateObj.removeClass("force-readonly");
             
            dateObj.datepicker((checked) ? "enable" : "disable"); 
            
            if(!checked) dateObj.addClass("force-readonly");
         })   
        
        tabObj.find("[name=chkDatePeriod]").change();
        tabObj.find("[name=selCurrency]").change(function() { thisObj.updateCurrency(); });
        //tabObj.find("[name=selWarehouseKey]").change(function() { thisObj.rebindEl(); }); // agar kereset parameter utk ajax
        
        tabObj.find("[name=trStartDate], [name=trEndDate]").bind( "change",function() { 
            var trStartDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val()));
            var trEndDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val()));
               
            if (trStartDate > trEndDate) 
                tabObj.find("[name=trEndDate]").val(tabObj.find("[name=trStartDate]").val()); 
            
		});
        
        tabObj.find("[name=selDPSettlementType]").change(function() { thisObj.updateDPType(); });  
        tabObj.find("[name=selCurrency]").change(function() { thisObj.onChangeCurrency();});
                
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});

       
        tabObj.find("[name='chkPick-master']").val(1).change(); 
        //addNewTemplateRow("cost-row-template");  
        
        thisObj.rebindEl(); 
         
    }
}
