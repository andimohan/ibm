function CustomerDownpayment(tabID, varConstant, cashTOP) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    var objAndValue = new Array;

    var objAndValueForDetailAutoComplete = objAndValue;

    this.tabID = tabID;

    this.updateTypeKey = function updateTypeKey(ui) {
        var typekey = (ui.item) ? ui.item.tabletypekey : 0;
        tabObj.find("[name='selDPType']").val(typekey).blur();
    }

    this.onCLickAddPayment = function onCLickAddPayment() {
        $newRow = addNewTemplateRow("payment-method-row-template");
    }

    this.calculateTotal = function calculateTotal() {

        var objTaxPercentage = tabObj.find("[name='taxPercentage']");

        var subtotal = parseInt(unformatCurrency(tabObj.find("[name='amount']").val())) || 0;
        var includeTax = (tabObj.find("[name='chkIncludeTax']").val() == 1) ? true : false;
        var taxPercentage = parseFloat(unformatCurrency(objTaxPercentage.val())) || 0;
        var prepaidTax23Percentage = parseFloat(unformatCurrency(tabObj.find("[name='prepaidTax23Percentage']").val())) || 0;
        //var prepaidTax23 =  parseFloat(unformatCurrency(tabObj.find("[name='prepaidTax23']").val())) || 0 ;   

        //objTaxPercentage.val( (!includeTax) ? 0 : 10  ).blur();   
        //objTaxPercentage.prop("readonly",!includeTax);
        var beforeTaxTotal = subtotal;
        var taxValue = 0;
        if (includeTax == 0) {
            taxValue = subtotal * taxPercentage / 100;
            subtotal += taxValue;
        } else {
            taxValue = (taxPercentage / (100 + taxPercentage)) * subtotal;
            beforeTaxTotal = subtotal - taxValue;
        }

        var prepaidTax23 = beforeTaxTotal * prepaidTax23Percentage / 100;
        tabObj.find("[name='prepaidTax23']").val(prepaidTax23).blur();

        var totalPayment = beforeTaxTotal + taxValue - prepaidTax23;
        tabObj.find("[name='taxValue']").val(taxValue).blur();
        tabObj.find("[name='beforeTaxTotal']").val(beforeTaxTotal).blur();
        tabObj.find("[name='subtotal']").val(subtotal).blur();
        tabObj.find("[name='payment']").val(totalPayment).blur();

        /*    
            var totalPayment = 0; 
             tabObj.find("[name='paymentMethodValue[]']").each(function() {   
                    totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
            })  
             */

    }

    this.onChangeCurrency = function onChangeCurrency() {

        var selCurrencyObj = tabObj.find("[name='selCurrency']")
        var currencyRateObj = tabObj.find("[name='currencyRate']");

        var changeFlag = false;
        if (selCurrencyObj.val() == varConstant.CURRENCY.idr) {
            changeFlag = true;
            currencyRateObj.val(1);
        }

        currencyRateObj.prop("readonly", changeFlag);
        //tabObj.find(".active-currency").html(selCurrencyObj.find("option:selected").text());

        //currencyRateObj.change().blur();

        var trDateObj =  tabObj.find("[name='trDate']"); 
        $.ajax({
            type: "GET",
            url: 'ajax-currency-rate.php',
            data: "action=getLastRate&currencykey=" + selCurrencyObj.val()+"&trdate=" + trDateObj.val(), 
            beforeSend: function (xhr) {
                currencyRateObj.val(1);
            },
            success: function (data) {
                if (data) {
                    var data = JSON.parse(data);
                    currencyRateObj.val(data[0]['rate']).blur();
                }
            }
        });
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
   
                            $( this ).dialog( "close" );
                            thisObj.updateVoucher(); 
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
				
            } 	  
        
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        //thisObj.calculateTotal(); 
    }

    this.rebindEl = function rebindEl() {
        //  bindEl(tabObj.find("[name='paymentMethodValue[]']"), 'change',  function(){ thisObj.calculateTotal() }); 
    }

    this.loadOnReady = function loadOnReady() { 
        tabObj.find("[name=selTermOfPaymentKey]").change(function () {

            for (i = 0; i < cashTOP.length; i++) { 
                if ($(this).val() == cashTOP[i]) {
                    tabObj.find(".transaction-detail-row.payment-method-row-template clone-detail").find(".remove-button").each(function () {
                        $(this).click()
                    });
                    tabObj.find(".cashTOP").hide();
                    return;
                }
            }

            tabObj.find(".cashTOP").show();
        });
        tabObj.find("[name=btnAddPayment]").on('click', function () {
            thisObj.onCLickAddPayment();
        });
        tabObj.find("[name=selTermOfPaymentKey]").change();
        // tabObj.find("[name=amount]" ).change(function(){thisObj.calculateTotal()}) 
        tabObj.find("[name=selCurrency]").change(function () {
            thisObj.onChangeCurrency();
        });

        thisObj.rebindEl();
    }
}