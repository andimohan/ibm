function CostReconsile(tabID, rs, varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.tablekey = varConstant.TABLEKEY;   

    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidReconsileKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'});
    objAndValue.push({object:'hidServiceKey[]', value :'costkey'});
    objAndValue.push({object:'serviceName[]', value :'servicename'});
    objAndValue.push({object:'reconsileAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    var objAndValueForDetailAutoComplete = objAndValue;

//    var objAndValue = new Array;
//    objAndValue.push({object:'hidServiceKey[]', value :'pkey'});   
//    var objAndValueForDetailServiceAutoComplete = objAndValue;

    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction"));
        thisObj.calculateTotal();  
    }

    
     this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);
 
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
                
            }

            detailRow.find("[name=\"reconsileCode[]\"]").first().val(ui.item['code']);
    }

    this.calculateTotal = function calculateTotal(){

        var amount = 0;      


        tabObj.find("[name='chkPick[]']").not(":disabled").each(function(){    
            if ($(this).val() != 1 )  return; 
            var objAmount = $(this).closest(".div-table-row").find("[name='amount[]']"); 
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0; 
        })
        
        var total = amount; 
        tabObj.find("[name='total']").val(total).blur();  
   
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
        
        var ajaxData = "action=getCostReconsileByInvoice&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&pkey=" + tabObj.find("[name=hidInvoiceKey]" ).val()+'&warehousekey=' + tabObj.find("[name=selWarehouseKey]").val() +dateParam;
        $.ajax({
            type: "GET",
            url:  'ajax-emkl-order-invoice.php',
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
                            arrPostValue.push({"selector":"hidReconsileKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"reconsileCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"refCode", "value":data[i].refcode}); 
                            arrPostValue.push({"selector":"hidServiceKey", "value":data[i].costkey}); 
                            arrPostValue.push({"selector":"serviceName", "value":data[i].servicename}); 
                            arrPostValue.push({"selector":"reconsileAmount", "value":data[i].amount}); 
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
 

    this.updateInvoiceInformation = function updateInvoiceInformation(event, ui){
            var obj = this; 
            if (tabObj.find("[name=hidCurrentInvoiceKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah invoice akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Invoice", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidInvoiceKey]" ).val(tabObj.find("[name=hidCurrentInvoiceKey]" ).val());
                        tabObj.find("[name=invoiceCode]" ).val(tabObj.find("[name=hidCurrentInvoiceCode]" ).val());
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        thisObj.rebindEl();
//                        thisObj.updateDetailInvoiceInformation();

                  },
                  buttons : {
                      OK : function (){  
                             if (ui.item == null) { 
                                clearAutoCompleteInput(obj,'hidInvoiceKey');	
                                tabObj.find("[name=hidCurrentInvoiceKey]" ).val(''); 
                                tabObj.find("[name=hidCurrentInvoiceCode]" ).val('');
                                tabObj.find("[name=customerName]" ).val('');  
                             }else{
                                tabObj.find("[name=hidCurrentInvoiceKey]" ).val(ui.item.pkey); 
                                tabObj.find("[name=hidCurrentInvoiceCode]" ).val(ui.item.value);  
                                tabObj.find("[name=customerName]" ).val(ui.item.customername); 
                             } 
                            thisObj.resetDetails(); 
						   	thisObj.updateDetailInvoiceInformation();
						  
                            $( this ).dialog( "close" );
                      },
                      Cancel : function (){  
                            $( this ).dialog( "close" );
                      }
                  },
                });	 
            }else{ 
                 if (ui.item == null) {
                    clearAutoCompleteInput(obj,'hidInvoiceKey');	
                    tabObj.find("[name=hidCurrentInvoiceKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentInvoiceCode]" ).val(''); 
                    tabObj.find("[name=customername]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentInvoiceKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentInvoiceCode]" ).val(ui.item.value); 
                    tabObj.find("[name=customerName]" ).val(ui.item.customername); 
                     
                 } 	
            
				thisObj.updateDetailInvoiceInformation(); 
                 thisObj.rebindEl(); 

            } 	 

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
             
        thisObj.updateNumberDecimal(); 
   
        
    }
    
    this.updateDetailInvoiceInformation = function updateDetailInvoiceInformation(){
        var invoicekey = tabObj.find("[name='hidInvoiceKey']").val(); 
		$.ajax({
					type: "GET",
					url:  'ajax-emkl-order-invoice.php', 
					data: "action=getItemDetailByHeader&invoicekey=" + invoicekey,  
					beforeSend:function (xhr){  
						// bersihin baris dulu
						tabObj.find(".invoice-row").remove();
                        tabObj.find(".total").text(0);
                        tabObj.find(".tax").text(0);
                        tabObj.find(".grandtotal").text(0);
					},
					success: function(data){  
							if(data){
								 if(!data) return;

								 var data = JSON.parse(data);
								 if (data.length <= 0) return;
								 // clone row

								$template = tabObj.find('.invoice-row-template');
                                var total = 0;
                                var roundDecimal = (data[0].headercurrencykey == 1 ) ? 0 : 2;
                                 
								for(i=0;i<data.length;i++){ 
                                    total += parseFloat(unformatCurrency(data[i].total));

								    $newRow   = $template.clone().addClass('invoice-row').show().insertBefore($template.first());
								    // isi row hasil clone

									$newRow.find(".qty").text(parseFloat(unformatCurrency(data[i].qtyinbaseunit))).formatCurrency({roundToDecimalPlace: 2 });
									$newRow.find(".services").text(data[i].itemname);
									$newRow.find(".currency").text(data[i].headercurrencyname);
									$newRow.find(".amount").text(parseFloat(unformatCurrency(data[i].total))).formatCurrency({roundToDecimalPlace: roundDecimal }); // anti buat otomatis
								}

                                tabObj.find(".total").text(total).formatCurrency({roundToDecimalPlace: roundDecimal }); // anti buat otomatis
							}
					}  
				}); 
    }
    
    this.updateNumberDecimal = function updateNumberDecimal(){    
        var selCurrencyObj = tabObj.find("[name='selCurrency']");
        var isNumber = (selCurrencyObj.val() == varConstant.CURRENCY.idr) ? true : false; 
        changeNumberDecimal(tabObj.find("[name='reconsileAmount[]'],[name='outstanding[]'],[name='amount[]'],[name='total']"),isNumber); 
    }

    
    this.rebindEl = function rebindEl(){ 
        var currencykey = tabObj.find("[name='selCurrency']").val();
        var invoicekey = tabObj.find("[name='hidInvoiceKey']").val();
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='amount[]']"),'change', function() { thisObj.calculateTotal(); });  
        bindAutoCompleteForTransactionDetail('reconsileCode[]',  objAndValueForDetailAutoComplete,'ajax-prepaid-expense.php?action=searchData&invoicekey=' + invoicekey +"&currencykey=" + currencykey+'&warehousekey=' + tabObj.find("[name=selWarehouseKey]").val(),thisObj.updateDetail); 
//        bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForDetailServiceAutoComplete,'ajax-item.php?action=searchData&itemtype=3');     


    }

    this.loadOnReady = function loadOnReady(){ 
        thisObj.updateNumberDecimal(); 


        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
         
  /*      tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]");
            
            dateObj.removeClass("force-readonly");
             
            dateObj.datepicker((checked) ? "enable" : "disable"); 
            
            if(!checked) dateObj.addClass("force-readonly");
         })  */
        
//        tabObj.find("[name=chkDatePeriod]").change();
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

        thisObj.rebindEl(); 
         
    }
}
