function EMKLCommission(tabID,cashTOP,varConstant) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;
    
        var firstOpened = true;
    	var objAndValue = new Array;
		objAndValue.push({object:'hidSalesDetailKey[]', value :'pkey'});  
        var objAndValueSalesDetailAutoComplete = objAndValue;
                  
        this.updateFromJobOrder = function updateFromJobOrder(){   
                var pkey = tabObj.find("[name=hidJobOrderKey]").val();
                  
                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-job-order.php',  
                    data: "action=getDataRowById&pkey=" + pkey ,  
                }).done(function( data ) { 
                      
                    data = JSON.parse(data) ; 
                     
                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];
                     
                    tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_));
                    tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=selTypeOfJob]").val(data.jobtypekey);
                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey).change();  
                    tabObj.find("[name=volume]").val(data.volume).blur(); 
                    tabObj.find("[name=selVolumeType]").val(data.volumetype);
                    tabObj.find("[name=containerName]").val(decodeHTMLEntities(data.containername));
                    tabObj.find("[name=bookingNumber]").val(data.bookingnumber);
                    tabObj.find("[name=poNumber]").val(data.ponumber);
                    tabObj.find("[name=mblNumber]").val(data.mblnumber); 
                    tabObj.find("[name=containerName]").val(decodeHTMLEntities(data.containername));
                    tabObj.find("[name=shipperName]").val(data.customername); 
                    tabObj.find("[name=pol]").val(data.polname); 
                    tabObj.find("[name=pod]").val(data.podname); 
                    tabObj.find("[name=selWarehouseKey]").val(data.warehousekey); 
                    
                    
                    updateComboboxReadonly(tabObj.find("[name=selTypeOfJob]"));
                    tabObj.find("[name=selTypeOfJob]").change();
                        
                });  
        }
            

        
        this.updateJobType = function updateJobType(){
           // kalo LCL gk ad supplier dan conginee 
            var selContainerObj = tabObj.find("[name=selContainerType]");  
            var fclOnlyObj = tabObj.find(".fcl-only");
            var lclOnlyObj = tabObj.find(".lcl-only");
            var supplierDetailRow = tabObj.find(".supplier-row").not(".row-template");
            
            var containerType = selContainerObj.val();  
            if (containerType == varConstant.EMKL.container.lcl ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                
                $(".fcl-readonly").attr("readonly", false);
            }else{
                lclOnlyObj.hide();
                fclOnlyObj.show();
                
                $(".fcl-readonly").attr("readonly", true);
            }  
        }
        
         
        this.getRowObj = function getRowObj(obj){
            return obj.closest(".div-table-row");
        } 
         
        this.onChangeCurrency = function onChangeCurrency(){
            
            var selCurrencyObj = tabObj.find("[name='selCurrency']")
            var currencyRateObj =  tabObj.find("[name='currencyRate']");
            
            var detailCurrency =  tabObj.find("[name='selCurrencyDetail[]']"); 
              
            detailCurrency.val(selCurrencyObj.val());
            
            var changeFlag = false;
            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){ 
                changeFlag = true;
                currencyRateObj.val(1);
            }
             
            currencyRateObj.prop("readonly", changeFlag);
            tabObj.find(".mnv-active-currency").html(selCurrencyObj.find("option:selected").text());
            
            // dipisah agar dapat dipanggil ketika onload tanpa pengaruh ke nilai rate dll
            thisObj.updateAvailableCurency(); 
            
            currencyRateObj.change().blur();
            thisObj.updateNumberDecimal();
            
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
                                 thisObj.onChangeCurrencyRate();
                            }
                    }  
                });
        }
         
         this.updateAvailableCurency = function updateAvailableCurency(){
            var selCurrencyObj = tabObj.find("[name='selCurrency']")
            var detailCurrency =  tabObj.find("[name='selCurrencyDetail[]']"); 
             
            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){  
                detailCurrency.find("option:not(:selected)").attr('disabled', true);
            }else{   
                detailCurrency.find("option").attr('disabled', false);
            }
                   
        }
         
 	 this.updateTOP = function updateTOP(){
          
                    var selTermOfPaymentKey = tabObj.find("[name=selTermOfPaymentKey]" ).val();   
                    var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val(); 

                       $.ajax({
                            type: "GET",
                            url:  'ajax-supplier.php',
                            data: "action=getDataRowById&pkey=" + supplierkey ,  
                        }).done(function( data ) {

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
         
         this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
         }
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal();  
        }   
         this.onChangeCurrencyRate = function onChangeCurrencyRate(){
               
             tabObj.find("[name='priceInUnit[]']").each(function(){ 
                   thisObj.calculateDetail(this); 
             })
              
        }
           
        
        this.calculateDetail = function calculateDetail(obj){   
     
            var rowObj =  $(obj).closest(".transaction-detail-row");  
            var rate = parseFloat(unformatCurrency(tabObj.find("[name='currencyRate']").val())) ||  1;   
            
            var qty = parseFloat(unformatCurrency(rowObj.find("[name='qty[]']").val())) || 0;
            var priceInUnit = parseFloat(unformatCurrency(rowObj.find("[name='priceInUnit[]']").val())) || 0;   
            var subtotal = qty * priceInUnit; 
            
            rowObj.find("[name='detailRowCurrencySubtotal[]']").val(subtotal).blur();
            
            var selCurrencyDetailObj = rowObj.find("[name='selCurrencyDetail[]']"); 

            var currencyheaderkey = tabObj.find("[name=selCurrency]").val();
 
			if(currencyheaderkey==varConstant.CURRENCY.idr){
                if(selCurrencyDetailObj.val() != varConstant.CURRENCY.idr) 
					subtotal *= rate;   
            }else{ 
                if(selCurrencyDetailObj.val() == varConstant.CURRENCY.idr) 
					subtotal /= rate;    
            }    
            
            rowObj.find("[name='detailSubtotal[]']").val(subtotal).blur();   
            rowObj.find(".mnv-active-currency-detail").html(selCurrencyDetailObj.find("option:selected").text()); 
            
            thisObj.updateDetailNumberDecimal(rowObj);
            thisObj.calculateTotal();
        }
        
        this.updateNumberDecimal = function updateNumberDecimal(){    
            var selCurrencyObj = tabObj.find("[name='selCurrency']");
            var isNumber = (selCurrencyObj.val() == varConstant.CURRENCY.idr) ? true : false;  
            changeNumberDecimal(tabObj.find("[name='detailSubtotal[]'],[name='total'],[name='totalPayment'],[name='paymentMethodValue[]'] ,[name='balance']"),isNumber); 
        }
        
        this.updateDetailNumberDecimal = function updateDetailNumberDecimal(rowObj){    
            var selCurrencyDetailObj = (rowObj) ? rowObj.find("[name='selCurrencyDetail[]']") :  tabObj.find("[name='selCurrencyDetail[]']"); 
            
            selCurrencyDetailObj.each(function(){    
                var isNumber = ($(this).val() == varConstant.CURRENCY.idr) ? true : false;
                var rowObj = $(this).closest(".transaction-detail-row");
                changeNumberDecimal(rowObj.find("[name='priceInUnit[]'],[name='detailRowCurrencySubtotal[]']"),isNumber); 
            })
        }
                 
        this.calculateTotal = function calculateTotal(){    
            var subtotal = 0; 
            tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseFloat(unformatCurrency($(this).val())) || 0;  })
            tabObj.find("[name='subtotal']").val(subtotal).blur();
                       
            var total = subtotal;
            tabObj.find("[name='total']").val(total).blur();
            
            var totalPayment = parseFloat(unformatCurrency(tabObj.find("[name='totalPayment']").val()));
            
            var balance = totalPayment - total;
            tabObj.find("[name='balance']").val(balance).blur();
        }
         
        
        this.rebindEl = function rebindEl(){    
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'],  [name='selCurrencyDetail[]']"),'change',function(){ thisObj.calculateDetail(this) });  
        }
        
        
        this.loadOnReady = function loadOnReady(){  
            
            thisObj.updateNumberDecimal();
            thisObj.updateDetailNumberDecimal();
            
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
            tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 
            tabObj.find("[name=selVolumeType]").change(function() { thisObj.updateVolumeType(); });
            tabObj.find("[name=selCurrency]").change(function() { thisObj.onChangeCurrency(); });
			tabObj.find(".mnv-active-currency").html(tabObj.find("[name=selCurrency]").find("option:selected").text());
            tabObj.find("[name=currencyRate]").change(function() { thisObj.onChangeCurrencyRate(); });
            
            thisObj.updateAvailableCurency(); 
            thisObj.updateJobType(); 
            thisObj.rebindEl();
        }
}