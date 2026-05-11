function CashBankOut(tabID, varConstant, opt){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID;    

		var arrCurrency =   opt.arrCurrency;  
		var CURRENCY = varConstant.CURRENCY; 

		var  objAndValue = new Array;
		objAndValue.push({object:'hidSupplierKey[]', value :'pkey'}); 
		var objAndValueForDetailAutoComplete  = objAndValue; 

		var id = tabObj.find("[name=hidId]").val();  

		this.tablekey = varConstant.tablekey;     

        this.calculateTotal = function calculateTotal(){
               
            var totalCashOutAmount = 0;   
            var totalPPh = 0;
            var totalCost = 0;
             
            tabObj.find("[name='amount[]']").each(function(){   
                    var row = $(this).closest(".transaction-detail-row");
                    var includeTax =  parseFloat(unformatCurrency(row.find("[name='chkDetailIncludeTax[]']").val())) || 0;
                    var taxPercentage =  parseFloat(unformatCurrency(row.find("[name='detailTaxPercentage[]']").val())) || 0 ; 

                    var amount = parseFloat(unformatCurrency($(this).val())) || 0; 
                    var PPhValue = parseFloat(unformatCurrency(row.find("[name=\"PPhValue[]\"]").val())) || 0;
                 
                    var DPP = (amount + PPhValue) / (1 + taxPercentage/100);
                    var taxValue = DPP * taxPercentage/100;
                
                    var afterTax = DPP + taxValue;
                    var cashOutAmount = afterTax - PPhValue;
                    var totalAmount = afterTax; // ==> total cost
                 
                    row.find("[name=\"detailTotal[]\"]").val(totalAmount).blur();
                 
                    totalPPh += PPhValue;
                    totalCost += totalAmount ; 
                    totalCashOutAmount += cashOutAmount ;  
            })     

           tabObj.find("[name='totalCost']").val(totalCost).blur();  
           tabObj.find("[name='totalPPh']").val(totalPPh).blur();  
           tabObj.find("[name='total']").val(totalCashOutAmount).blur();  
              
        }
        
            this.updateCurrency = function updateCurrency() {

            var pkey = tabObj.find("[name=hidCOAHeaderKey]").val();
            
                  
                $.ajax({
                    type: "GET",
                    url:  'ajax-coa.php',
                    async: false,
                    data: "action=searchData&pkey=" + pkey ,  
                }).done(function(data) { 

                    if(!data) return;
                    
                    data = JSON.parse(data) ; 

                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];

                    if(data.currencykey == 0 || data.currencykey == null )
                    {
                        //currency val == 1 or idd
                        tabObj.find("[name=currencyName]").val(arrCurrency[CURRENCY]['name']);
                        tabObj.find("[name=hidCurrencyKey]").val(CURRENCY).change();
                    } else {
                        tabObj.find("[name=currencyName]").val(arrCurrency[data.currencykey]['name']);                   
                        tabObj.find("[name=hidCurrencyKey]").val(data.currencykey).change();
                    }

                    
    
                }); 
            
//            thisObj.onChangeCurrency();

        }
        
        
        this.onChangeCurrency = function onChangeCurrency(){ 
                var selCurrencyObj = tabObj.find("[name=hidCurrencyKey]");  
                    var currencyRateObj =  tabObj.find("[name='currencyRate']");


                    // gk bisa pake, karena ada tagihan, header pilih IDR, tp detailnya diisi USD (tp mau dibayarkan sebagai IDR), jd ratenya gk boleh 1
                    // apakah boleh diakfitfkan, tp jgn readonly. jg kalo IDR bisa diisi jg

                    var changeFlag = false;
                    if(selCurrencyObj.val() == varConstant.CURRENCY.idr){ 
                        changeFlag = true;
                        currencyRateObj.val(1);
                    }


                    currencyRateObj.change().blur();
                    //thisObj.updateNumberDecimal();

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
    
         this.onChangeRecepientType = function onChangeRecepientType(){ 
                var selRecipientTypeObj = tabObj.find("[name=selRecipientType]");  

                    if(selRecipientTypeObj.val() == varConstant.RECIPIENTYPE.customer){ 
                        tabObj.find(".type-1").show();
                        tabObj.find(".type-2").hide();
                        tabObj.find(".type-3").hide();
                        tabObj.find("[name=employeeName]").val('');
                        tabObj.find("[name=hidEmployeeKey]").val('');
                        tabObj.find("[name=supplierName]").val('');
                        tabObj.find("[name=hidSupplierKey]").val('');
                    }else if(selRecipientTypeObj.val() == varConstant.RECIPIENTYPE.supplier){ 
                        tabObj.find(".type-1").hide();
                        tabObj.find(".type-2").show();
                        tabObj.find(".type-3").hide();
                        tabObj.find("[name=customerName]").val('');
                        tabObj.find("[name=hidCustomerKey]").val('');
                        tabObj.find("[name=employeeName]").val('');
                        tabObj.find("[name=hidEmployeeKey]").val('');
                        
                    }else{
                         tabObj.find(".type-1").hide();
                        tabObj.find(".type-2").hide();
                        tabObj.find(".type-3").show();
                        tabObj.find("[name=supplierName]").val('');
                        tabObj.find("[name=hidSupplierKey]").val('');
                        tabObj.find("[name=customerName]").val('');
                        tabObj.find("[name=hidCustomerKey]").val('');

                    }
	 
        }
         
        this.reupdatePPhField = function reupdatePPhField(obj){
            var row = obj.closest(".transaction-detail-row");
            var costkey = parseFloat(obj.val());
            
            var readonly = false;
            
            if(costkey == 0){
                row.find("[name=\"PPhValue[]\"],[name=\"detailTaxPercentage[]\"]").val(0).change().blur();
                readonly = true;
            }else{
                readonly = false; 
            } 

            row.find("[name=\"PPhValue[]\"], [name=\"dummychkDetailIncludeTax[]\"]").attr("readonly",readonly); 
        }
        
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        }
        
        this.rebindEl = function rebindEl(){ 
            bindEl(tabObj.find("[name='amount[]'], [name='PPhValue[]'], [name=\"detailTaxPercentage[]\"], [name=\"chkDetailIncludeTax[]\"]"),'change', function() { thisObj.calculateTotal(); });
            bindEl(tabObj.find("[name='hidCostKey[]']"),'change', function() { thisObj.reupdatePPhField($(this)); });
            bindAutoCompleteForTransactionDetail('supplierName[]',objAndValueForDetailAutoComplete,'ajax-supplier.php?action=searchData'); 
        }
        
        
        this.loadOnReady = function loadOnReady(){   

              var hidCurrencyObj = tabObj.find("[name=hidCurrencyKey]");

              hidCurrencyObj.change(function(){thisObj.onChangeCurrency()})   

            if(tabObj.find("[name=hidId]").val() == '')            
                hidCurrencyObj.change();


            tabObj.find("[name=selRecipientType]").change(function(){thisObj.onChangeRecepientType()})   
            tabObj.find("[name=selRecipientType]").change();

            if (!opt['fileDetail'] || opt['fileDetail'].length == 0)
                addNewTemplateRow("file-row-template",null,null,thisObj.rebindEl);    
        
            
            thisObj.rebindEl(); 
        }
        
}
