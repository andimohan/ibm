function ARPayment(tabID, rs,varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.customerTax = 0;
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});
    objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'}); 
    var objAndValueForDPDetailAutoComplete  = objAndValue; 
    
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
    var objAndValueForCostDetailAutoComplete  = objAndValue; 

    var  objAndValue = new Array;
    objAndValue.push({object:'hidARKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'});
    objAndValue.push({object:'refJOCode[]', value :'refcode2'});  
    objAndValue.push({object:'arAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    var objAndValueForDetailAutoComplete = objAndValue;



    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction"));
        clearAllRows(tabObj.find(".mnv-downpayment"));
        
        addNewTemplateRow("downpayment-row-template");  
        thisObj.rebindDownpayment(); 
        
        thisObj.calculateTotal(); 
    }

    
     this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
            }

            detailRow.find("[name=\"arCode[]\"]").first().val(ui.item['code']);
            //ARPayment.updateDefaultDownpayment();
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
              
             var arkey = new Array();
             tabObj.find("[name='hidARKey[]']").each(function(){ if($(this).val()) arkey.push($(this).val()); })
            
              $.ajax({
                type: "GET",
                url:  'ajax-customer-downpayment.php', 
                data: 'action=getDownpaymentForAR&arkey=' + JSON.stringify(arkey), 
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
                    bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete,'ajax-supplier-downpayment.php?action=searchData'); 
                    tabObj.find("[name=\"downpaymentAmount[]\"]").change().blur();
                    thisObj.calculateTotalDownpayment();                         
                }  
            }); 
            
    }
     
    this.calculateTotalDownpayment = function calculateTotalDownpayment(){
        var totalDP = 0; 
        tabObj.find("[name='downpaymentAmount[]']").each(function() { totalDP += parseInt(unformatCurrency($(this).val())) || 0;   })
        tabObj.find("[name='totalDownpayment']").val(totalDP).blur(); 

        return totalDP;
     }

     this.calculateTotalCost = function calculateTotalCost(){
        var totalCost = 0; 
        tabObj.find("[name='costAmount[]']").each(function() { totalCost += parseInt(unformatCurrency($(this).val())) || 0;   })
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

            amount += parseInt(unformatCurrency(objAmount.val())) || 0;
            totalDiscount += parseInt(unformatCurrency(objDiscount.val())) || 0;
            totalPPH += parseInt(unformatCurrency(objPph.val())) || 0;

        })     

        var totalReceived = amount + totalDiscount;
        tabObj.find("[name='totalReceived']").val(totalReceived).blur(); 


        tabObj.find("[name='totalDiscount']").val(totalDiscount).blur(); 
        tabObj.find("[name='pph23']").val(totalPPH).blur(); 

        var totalDP = thisObj.calculateTotalDownpayment();
        var totalCost = thisObj.calculateTotalCost();
         
        var total = amount-totalPPH-totalDP-totalCost; 
        tabObj.find("[name='total']").val(total).blur();

        var totalPayment = 0; 
        tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseInt(unformatCurrency($(this).val())) || 0; })   
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();
         
        var balance = totalPayment - total;  
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
        var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;  
        bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete,'ajax-customer-downpayment.php?action=searchData&customerkey='+customerkey); 
    } 
    
        
    this.rebindVoucher = function rebindVoucher(){ 
        var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name=selVoucher]");
            
        var ajaxData = "action=searchData&customerkey=" + tabObj.find("[name=hidCustomerKey]").val()+"&warehousekey=" + tabObj.find("[name=selWarehouseKey]").val();  
      
         $.ajax({
            type: "GET",
            url:  'ajax-cash-bank.php',
            async : false,
            beforeSend:function (xhr){
                clearAllRows(tabObj.find(".mnv-payment-method"));
                $('option', selVoucherObj).remove(); 
            },
            data: ajaxData,
            success: function(data){ 
                   // update combobox services
                        var newOptions = {};
                        for(i=0;i<data.length;i++)  
                            newOptions[data[i].conversionunitkey] =  data[i].unitname;       
 
                        var options = (selVoucherObj.prop) ? selVoucherObj.prop('options') : selVoucherObj.attr('options');  

                        $('option', selVoucherObj).remove();

                        $.each(newOptions, function(val, text) {
                            options[options.length] = new Option(text, val);
                        });
                    
                        // add conversion 
                        /*selVoucherObj.find("option").each(function(i){ 
                                $(this).attr("relconversionmultiplier",data[i].conversionmultiplier);
                            } 
                        ) */

                        //selUnitObj.find('option:eq(0)').prop('selected', true).change();
                        selVoucherObj.val(data[0]['pkey']);
            }  
        }); 
    } 
        
    this.rebindCost = function rebindCost(){  
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForCostDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=2&serviceCost=1&moduleCost=trucking'); 
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
                 
        var ajaxData = "action=searchData&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&customerkey=" + tabObj.find("[name=hidCustomerKey]").val()+"&warehousekey=" + tabObj.find("[name=selWarehouseKey]").val()+dateParam;  
          
        $.ajax({
            type: "GET",
            url:  'ajax-ar.php',
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
                            arrPostValue.push({"selector":"hidARKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"arCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"refCode", "value":data[i].refcode}); 
                            arrPostValue.push({"selector":"doNumber", "value":data[i].refcode2}); 
                            arrPostValue.push({"selector":"arAmount", "value":data[i].amount}); 
                            arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding}); 
                            arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                           
                            var tax = ( data[i].autotax == 1 && thisObj.customerTax > 0) ?  data[i].amount *  thisObj.customerTax / 100 : 0;  
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

                 thisObj.rebindEl(); 
            } 	 

            thisObj.updateTax();
            thisObj.rebindDownpayment(); 
    }

    this.updateTax = function updateTax(){
         $.ajax({
                type: "GET",
                url:  'ajax-customer.php', 
                async : false,
                data: 'action=getTaxInformation&pkey=' + tabObj.find("[name=hidCustomerKey]").val() , 
                success: function(data){  
                    thisObj.customerTax = 0;
                    
                    if (!data) return;
                    
                    var data = JSON.parse(data);  
                    thisObj.customerTax = data.taxpercentage;      
                }  
            }); 
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


    this.rebindEl = function rebindEl(){ 
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='discount[]'], [name='amount[]'], [name='taxPPH[]']"),'change', function() { thisObj.calculateTotal(); });  
        bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete,'ajax-ar.php?action=searchData&customerkey=' + tabObj.find("[name=hidCustomerKey]").val()+'&warehousekey=' + tabObj.find("[name=selWarehouseKey]").val(),thisObj.updateDetail); 
         
        var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");   
        bindEl(tableDownPaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
        bindEl(tableDownPaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
 
        var tableCostDetail = tabObj.find(".mnv-cost");   
        bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
        bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
  
        thisObj.rebindDownpayment();  
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

    this.loadOnReady = function loadOnReady(){ 
 
	   tabObj.find("[name=selTermOfPayment]" ).change();  

        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
        //tabObj.find("[name=btnAddPayment]").bind( "click", function(event) {  thisObj.onCLickAddPayment(); }) 
         
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
        
        tabObj.find("[name=selCurrency]").change(function() { thisObj.onChangeCurrency();});
         
        tabObj.find("[name='chkPick-master']").val(1).change(); 
        addNewTemplateRow("downpayment-row-template");  
        addNewTemplateRow("cost-row-template");  
        thisObj.rebindEl(); 
         
    }
}
