function CashIn(tabID,useMasterRevenue, varConstant, opt){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
      
    	var objAndValue = new Array; 
        if(useMasterRevenue){  
        		objAndValue.push({object:'hidRevenueKey[]', value :'pkey'});
                objAndValue.push({object:'hidCOAKey[]', value :'coakey'}); 
        }else{ 
	 	        objAndValue.push({object:'hidCOAKey[]', value :'pkey'}); 
        }
    

        var objAndValueForDetailAutoComplete = objAndValue;
           
        this.tabID = tabID;    
    	this.tablekey = varConstant.TABLEKEY;  
        this.useMasterRevenue = useMasterRevenue;
    
        var arrCurrency =   opt.arrCurrency;  
    
    
        this.calculateTotal = function calculateTotal(obj){
               
            var amount = 0;   
             
            tabObj.find("[name='amount[]']").each(function(){   
                    amount += parseFloat(unformatCurrency($(this).val())) || 0; 
            })     

           tabObj.find("[name='total']").val(amount).blur();  
              
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
        
        
         
        this.updateRecipientType = function updateRecipientType(){
          /* // kalo LCL gk ad customer dan conginee 
            var selRecipientObj = tabObj.find("[name=selRecipientTypeKey]");  
            var isCustomer = tabObj.find(".iscustomer");
            var isNotCustomer = tabObj.find(".isnotcustomer");
            
            var recipientType = selRecipientObj.val(); 
            if (recipientType == 2 ){ 
                isCustomer.show();
                isNotCustomer.hide();  
            }else{
                isCustomer.hide();
                isNotCustomer.show();
            }  */
        }
          
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
                
        this.rebindEl = function rebindEl(){  
            bindEl(tabObj.find("[name='amount[]'] "),'change', function() { thisObj.calculateTotal(); });    
            
            if(thisObj.useMasterRevenue)
                bindAutoCompleteForTransactionDetail('revenueName[]',objAndValueForDetailAutoComplete,'ajax-revenue-cash-in.php?action=searchData'); 
            else
                bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailAutoComplete,'ajax-coa.php?action=searchData');
        }
         
        this.loadOnReady = function loadOnReady(){  
            //tabObj.find("[name=selRecipientTypeKey]").change(function() { thisObj.updateRecipientType(); });
            //tabObj.find("[name=selRecipientTypeKey]").change();
            
            var hidCurrencyObj = tabObj.find("[name=hidCurrencyKey]");

            hidCurrencyObj.change(function(){thisObj.onChangeCurrency()})   

            if(tabObj.find("[name=hidId]").val() == '')
                hidCurrencyObj.change();

            
            thisObj.rebindEl();
        }
}