function CashOut(tabID,useMasterCost,varConstant,opt){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	this.tablekey = varConstant.TABLEKEY;  
        this.useStorage = varConstant.USE_STORAGE;  
    
        var arrCurrency =   opt.arrCurrency;
	
		var fileFolder = varConstant.uploadFileFolder;
		var fileUploaderTarget = "item-file-uploader";
		var rsFile = varConstant.rsFile;
		var arrFile = Array(); 
 
    	var id = tabObj.find("[name=hidId]").val();
	
    	var objAndValue = new Array; 
        if(useMasterCost){  
        		objAndValue.push({object:'hidCostKey[]', value :'pkey'});
                objAndValue.push({object:'hidCOAKey[]', value :'coakey'}); 
        }else{ 
	 	        objAndValue.push({object:'hidCOAKey[]', value :'pkey'}); 
        }
        var objAndValueForDetailAutoComplete = objAndValue;
           
        this.tabID = tabID;    
        this.useMasterCost = useMasterCost;
      
        this.calculateTotal = function calculateTotal(obj){
            
            // var amount = 0;   
            // tabObj.find("[name='amount[]']").each(function(){   
            //         amount += parseFloat(unformatCurrency($(this).val())) || 0; 
            // })     
            // tabObj.find("[name='total']").val(amount).blur();
            
            var totalCashOutAmount = 0;   
            var totalPPh = 0;
            var totalCost = 0;
            var totalPPN = 0;
            
            tabObj.find("[name='amount[]']").each(function () {
                var row = $(this).closest(".transaction-detail-row");

                var includeTax =  parseInt(unformatCurrency(row.find("[name='chkDetailIncludeTax[]']").val())) || 0;
                var taxPercentage =  parseFloat(unformatCurrency(row.find("[name='detailTaxPercentage[]']").val())) || 0 ; 

                var amount = parseFloat(unformatCurrency($(this).val())) || 0; 
                var PPhValue = parseFloat(unformatCurrency(row.find("[name=\"PPhValue[]\"]").val())) || 0;
                
                var DPP = (amount + PPhValue); 
//                if(includeTax){
//                    DPP = DPP / (1 + taxPercentage/100);
//                }

                // duit keluar sudah pasti termasuk pajak,
                DPP = DPP / (1 + taxPercentage/100);
                
                var taxValue = DPP * taxPercentage/100; 
                var afterTax = DPP + taxValue;
                
                var cashOutAmount = afterTax - PPhValue;
                 var totalAmount = afterTax;  
               // var costAmount = DPP;
 
                row.find("[name=\"detailTotal[]\"]").val(totalAmount).blur();

                totalPPh += PPhValue;
                totalCost += DPP ; 
                totalCashOutAmount += cashOutAmount ;
                totalPPN += taxValue;
            });

            tabObj.find("[name='totalCost']").val(totalCost).blur();  
            tabObj.find("[name='totalPPh']").val(totalPPh).blur();  
            tabObj.find("[name='totalPPN']").val(totalPPN).blur();  
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
        
        
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
                
        this.rebindEl = function rebindEl(){  
            bindEl(tabObj.find("[name='amount[]'], [name='PPhValue[]'], [name=\"detailTaxPercentage[]\"], [name=\"chkDetailIncludeTax[]\"] "),'change', function() { thisObj.calculateTotal(); });      
              
            if(thisObj.useMasterCost)
                bindAutoCompleteForTransactionDetail('CCOName[]',objAndValueForDetailAutoComplete,'ajax-cost-cash-out.php?action=searchData'); 
            else  
                bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailAutoComplete,'ajax-coa.php?action=searchData');
        }
         
        this.loadOnReady = function loadOnReady(){  
			
            var hidCurrencyObj = tabObj.find("[name=hidCurrencyKey]");

            hidCurrencyObj.change(function(){thisObj.onChangeCurrency()})   

            if(tabObj.find("[name=hidId]").val() == '')
                hidCurrencyObj.change();
             
            if(thisObj.useStorage){

            }else{ 
                if(id){    
                    for($i=0;$i<rsFile.length;$i++) 
                        arrFile.push(rsFile[$i].file); 

                    createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  

                }else{  
                     createFileUploader(fileUploaderTarget, fileFolder, "", "", false);
                }
            }
                  
         tabObj.find('[name=btnSubmitFileAjax]').on('click', function () { onSubmitFileAjax(tabObj,
                                                                                            {ajaxFile: 'ajax-cash-out.php'}
                                                                                           );});
            
            thisObj.rebindEl();
        }
}
