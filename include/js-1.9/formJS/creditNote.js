function CreditNote(tabID,varConstant){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        var objAndValue = new Array; 
        objAndValue.push({object:'hidARKey[]', value :'pkey'});  	    	 
        objAndValue.push({object:'amount[]', value :'outstanding'}); 
        objAndValue.push({object:'refCode[]', value :'refcode'}); 
        objAndValue.push({object:'arDate[]', value :'trdate', type : 'date'});	
        var objAndValueForDetailAutoComplete = objAndValue;	
        
     
        this.tabID = tabID;    
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
             
            var detailRow = $(target).closest(".transaction-detail-row");  
             
            for(var i=0;i<objAndValue.length;i++){     

                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
 
            }

            // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
            detailRow.find("[name='arCode[]']").first().val(ui.item['value']); 
            
        } 
          
        
        this.calculateTotal = function calculateTotal(){
            var subtotal = 0;
            tabObj.find("[name='creditTotal[]']").each(function(){ subtotal += parseFloat(unformatCurrency($(this).val())) || 0;  })
            tabObj.find("[name='grandTotal']").val(subtotal).blur();  
        }
 
        this.resetDetails = function resetDetails(){   
            clearAllRows($("#"+tabID)); 
            thisObj.calculateTotal(); 
        }
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        }
         
        this.onChangeChk = function onChangeChk(){   
            thisObj.calculateTotal();
        }
        
        this.updateCustomerInformation = function updateCustomerInformation(obj,event, ui){
           
                var topkey = 0;
            
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
                          
                            thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah
                            
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

                        var selCurrencyObj = tabObj.find("[name='selCurrency']"); 
                        var isNumber = (selCurrencyObj.val() == varConstant.CURRENCY.idr) ? true : false; 
                        changeNumberDecimal(tabObj.find("[name='amount[]'],[name='creditTotal[]'],[name='grandTotal']"),isNumber);

					   $( this ).dialog( "close" );
				  },
				  Cancel : function (){  
						tabObj.find("[name=selCurrency]").val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val()); 
						$( this ).dialog( "close" );
				  }
			  } 

			});	  

		} 
		  
		 this.onChangeCurrency = function onChangeCurrency(){
			thisObj.rebindEl();  
            thisObj.updateCurrency(); 
		}
           
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal();  
        } 
 
        this.rebindEl = function rebindEl(){   
            
             var handling = [];
             handling.onSelectFunction = 'getTabObj().updateDetail'; 
             var customerkey = tabObj.find("[name=hidCustomerKey]").val();
             var currencykey = tabObj.find("[name=selCurrency]").val();
            
			 bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete,'ajax-ar.php?action=searchData&customerkey=' + customerkey +'&currencykey='+ currencykey,handling);
             bindEl(tabObj.find("[name='creditTotal[]']" ), 'change',  function(){ thisObj.calculateTotal() }); 
             bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });
            
        }  
        
        this.loadOnReady = function loadOnReady(){
  
            tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)}); 
			tabObj.find("[name=selCurrency]").change(function() { thisObj.onChangeCurrency(); });
            tabObj.find("[name='chkPick-master']").val(1).change();     
            thisObj.rebindEl(); 

        }
        
}
