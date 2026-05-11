function EMKLInvoiceReceipt(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        var objAndValue = new Array; 
        objAndValue.push({object:'hidInvoiceKey[]', value :'pkey'});  	    	 
        objAndValue.push({object:'invoiceTotal[]', value :'grandtotal'}); 
        objAndValue.push({object:'invoiceDate[]', value :'trdate', type : 'date'});	
        var objAndValueForDetailAutoComplete = objAndValue;	
 
    
        this.tabID = tabID;    
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row"); 

            thisObj.updateRowInformation(detailRow,objAndValue,ui); 
            thisObj.calculateTotal();   

        } 
        

        this.calculateTotal = function calculateTotal(){    
            var amount = 0; 
            
            $("#" + tabID + " [name='chkPick[]']").not(":disabled").each(function(){   
                if ($(this).val() != 1 )
                    return;

                objAmount = $(this).closest(".div-table-row").find("[name='invoiceTotal[]']"); 
                amount += parseFloat(unformatCurrency(objAmount.val())) || 0;
            })  
            
            tabObj.find("[name='grandTotal']").val(amount).blur();
        } 
        
         this.updateRowInformation  = function updateRowInformation (detailRow,objAndValue,ui){
       
           var i;
           for(i=0;i<objAndValue.length;i++){     

                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
 
            }

            // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
            detailRow.find("[name='invoiceCode[]']").first().val(ui.item['value']); 

       }
  
        this.resetDetails = function resetDetails(){   
            clearAllRows($("#"+tabID)); 
            thisObj.calculateTotal(); 
        }
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        }
        
         
        this.importData = function importData(){  
            
            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;

            var customerkey = tabObj.find("[name=hidCustomerKey]").val(); 
            
	        $.ajax({
	            type: "GET",
	            url:  'ajax-emkl-order-invoice.php',
	            beforeSend:function (xhr){ 
                    clearAllRows($("#defaultForm-"+tabID));
                    thisObj.activeAjaxConnections++; 
	            }, 
	            data: 'action=searchData&statuskey=2&customerkey=' + customerkey, 
	            success: function(data){
                    
	                    var data = JSON.parse(data);  
	                    var i;
                        var newrow;
                        
                             
	                    for(i=0;i<data.length;i++){ 
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidInvoiceKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"invoiceCode", "value":data[i].code});
                            arrPostValue.push({"selector":"invoiceDate", "value": moment(data[i].trdate).format(_DATE_FORMAT_)}); 
                            arrPostValue.push({"selector":"invoiceTotal", "value":data[i].grandtotal});  
                            newrow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
	                    }

                     
                     thisObj.rebindEl();
                    
	                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
	                 tabObj.find(".inputnumber").change().blur();
	                 tabObj.find(".inputdecimal").change().blur();
                     thisObj.calculateTotal();

	                decreaseActiveAjaxConnections(thisObj); 
                     
                    tabObj.find("[name='chkPick-master']").val(1).change();  
                    
	            } ,
	             error: function(xhr, errDesc, exception) { 
                            decreaseActiveAjaxConnections(thisObj); 
                     
                }
	        });
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
                                     
                                    topkey  = ui.item.termofpaymentkey;
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
  
                        topkey  = ui.item.termofpaymentkey;
					 } 	
					  
                    thisObj.rebindEl();
				} 	 
             

        }
          
         
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal(); ;   
        }
            
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal();  
        } 
        
        this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(){
            
        } 
         
        this.rebindEl = function rebindEl(){   
            
             var handling = [];
             handling.onSelectFunction = 'getTabObj().updateDetail'; 
             var customerkey = tabObj.find("[name=hidCustomerKey]").val();
            
             bindAutoCompleteForTransactionDetail('invoiceCode[]',objAndValueForDetailAutoComplete,'ajax-emkl-order-invoice.php?action=searchData&statuskey=2&customerkey=' + customerkey,handling); 
             bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });
            
        } 
        
        this.loadOnReady = function loadOnReady(){
 
            tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); }); 
            tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)});  
 
            tabObj.find("[name='chkPick-master']").val(1).change();    
               
            thisObj.rebindEl(); 

        }
        
}
