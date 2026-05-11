function SupplierDownpayment(tabID,varConstant,cashTOP){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	var objAndValue = new Array; 

        var objAndValueForDetailAutoComplete = objAndValue;
           
        this.tabID = tabID;    
    
        this.updateTypeKey = function updateTypeKey(ui){  
            var typekey = (ui.item) ? ui.item.tabletypekey : 0 ;
            tabObj.find("[name='selDPType']").val(typekey).blur();  
        }
        
        this.onCLickAddPayment = function onCLickAddPayment(){
            $newRow = addNewTemplateRow("payment-method-row-template"); 
        }
        
          this.calculateTotal = function calculateTotal(){      
        
            var objTaxPercentage = tabObj.find("[name='taxPercentage']");
       
            var subtotal = parseInt(unformatCurrency(tabObj.find("[name='amount']").val())) || 0 ;
            var includeTax =   (tabObj.find("[name='chkIncludeTax']").val() == 1) ? true : false;
            var taxPercentage =  parseFloat(unformatCurrency(objTaxPercentage.val())) || 0 ;   
            var prepaidTax23Percentage =  parseFloat(unformatCurrency(tabObj.find("[name='prepaidTax23Percentage']").val())) || 0 ;  
            //var prepaidTax23 =  parseFloat(unformatCurrency(tabObj.find("[name='prepaidTax23']").val())) || 0 ;   
       
            //objTaxPercentage.val( (!includeTax) ? 0 : 10  ).blur();   
            //objTaxPercentage.prop("readonly",!includeTax);
            var beforeTaxTotal = subtotal;
            var taxValue = 0;
            if (includeTax == 0) {
                taxValue = subtotal * taxPercentage / 100;
                subtotal += taxValue;
            }else{
                taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
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
                    clearAutoCompleteInput(obj,'hidSupplierKey');	
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value); 
                 } 	  
                
                thisObj.updateVoucher(); 
                
            } 	      
        
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
            //tabObj.find(".active-currency").html(selCurrencyObj.find("option:selected").text());
             
            //currencyRateObj.change().blur();
             
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
            }
        
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         //thisObj.calculateTotal(); 
        }
                
        this.rebindEl = function rebindEl(){  
          //  bindEl(tabObj.find("[name='paymentMethodValue[]']"), 'change',  function(){ thisObj.calculateTotal() }); 
            
        }
        
        
	 this.updateVoucher = function updateVoucher(){ 
		
		// kalo gk pake voucher, gk usah
		if(!varConstant.useCashBankVoucher) return;
		
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
//                console.log(data)
                        // update combobox services 
                        if(!data) return; 
                        data = JSON.parse(data); 
                        var selectOpt = data;
                        reInsertSelectBox(selVoucherObj,selectOpt, {"key" : "pkey", "label" : "voucherlabel", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 
    } 
	 
	 
         
        this.loadOnReady = function loadOnReady(){  
            tabObj.find("[name=btnAddPayment]").on('click', function() { thisObj.onCLickAddPayment(); }); 
           // tabObj.find("[name=amount]" ).change(function(){thisObj.calculateTotal()}) 
            tabObj.find("[name=selCurrency]").change(function() { thisObj.onChangeCurrency(); });
                        tabObj.find("[name=selTermOfPaymentKey]" ).change(function() {
           
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".payment-method-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

               tabObj.find(".cashTOP").show();
            });
            
                    tabObj.find("[name=selTermOfPaymentKey]" ).change();   
            // gk boleh dipanggil, kalo gk nanti rate berubah pas onload
            //tabObj.find("[name=selCurrency]" ).change(); 
            
            thisObj.rebindEl();
        }
}
