function DebitNote(tabID,varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      

    var objAndValue = new Array; 
    objAndValue.push({object:'hidRefAPKey[]', value :'pkey'});  	    	 
    objAndValue.push({object:'amount[]', value :'outstanding'}); 
    objAndValue.push({object:'refPurchaseCode[]', value :'refcode'}); 
    objAndValue.push({object:'hidRefTableType[]', value :'reftabletype'}); 
    objAndValue.push({object:'refAPDate[]', value :'trdate', type : 'date'});	
    var objAndValueForDetailAutoComplete = objAndValue;	
    
    var objAndValue = new Array;
	objAndValue.push({object:'hidCostKey[]', value :'pkey'});
    var objAndValueForDetailCostAutoComplete = objAndValue;
 
    this.tabID = tabID;    
    
    this.updateDetail = function updateDetail(target,objAndValue,ui){   
         
        var detailRow = $(target).closest(".transaction-detail-row");
        var DNType = parseInt(tabObj.find("[name='selDNType']").val());  
         
        for(var i=0;i<objAndValue.length;i++){     

            if (objAndValue[i].type == "date")
               ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

            detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  

        }

        // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
  	if(DNType == 3) {
            detailRow.find("[name='costName[]']").first().val(ui.item['value']);
        } else {
            detailRow.find("[name='refCode[]']").first().val(ui.item['code']); 
        }
        
    } 
      
    
    this.calculateTotal = function calculateTotal(){
        var subtotal = 0;
        tabObj.find("[name='debitTotal[]']").each(function(){ subtotal += parseFloat(unformatCurrency($(this).val())) || 0;  })
        tabObj.find("[name='grandTotal']").val(subtotal).blur();  
    }

    this.resetDetails = function resetDetails(){   
        clearAllRows(tabObj.find(".mnv-transaction"));
        thisObj.calculateTotal(); 
    }
     
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
     thisObj.calculateTotal(); 
    }
     
    this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    } 
    
    this.updateSupplierInformation = function updateSupplierInformation(obj,event, ui){
       
            var topkey = 0;
        
            if (tabObj.find("[name=hidCurrentSupplierKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah Supplier akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Supplier", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidSupplierKey]" ).val(tabObj.find("[name=hidCurrentSupplierKey]" ).val());
                        tabObj.find("[name=supplierName]" ).val(tabObj.find("[name=hidCurrentSupplierName]" ).val()); 
                          $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));  
                      
                        thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah
                        
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
                    clearAutoCompleteInput(obj,'hidSupplierKey');	
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val('');  
                 }else{ 
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value);  
                 } 	
                  
                thisObj.rebindEl();
            } 	 
         

    }
    
    
    this.updateCurrency = function updateCurrency(){

        // perlu handle manual readonlynya, karena default main.js selalu panggil onChangeCurrency
        var  currencyObj = tabObj.find("[name=selCurrency]");
        var currencyRateObj = tabObj.find("[name=currencyRate]"); 

       $( "#dialog-message" ).html("Apakah Anda ingin mengganti mata uang untuk pelanggan ini ?<br>Semua detail transaksi akan dihapus jika Anda mengganti mata uang.");
        $( "#dialog-message" ).dialog({
          width: 300,
          modal: true,
          title:"Konfirmasi Perubahan Data mata uang", 
          close:function() {

                currencyObj.val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val());  
                currencyRateObj.val(tabObj.find("[name=hidCurrentCurrencyRate]" ).val()).blur(); 
                currencyRateObj.prop("readonly", (currencyObj.val() == 1) ? true:false ); 
          }, 
          open: function() {
              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
          }, 
          buttons : {
              OK : function (){     
                    tabObj.find("[name=hidCurrentCurrencyKey]" ).val(currencyObj.val()); 

                    thisObj.resetDetails();
                    $( this ).dialog( "close" ); 
                    thisObj.onChangeCurrency();
              },
              Cancel : function (){  
                    currencyObj.val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val()); 
                    currencyRateObj.val(tabObj.find("[name=hidCurrentCurrencyRate]" ).val()).blur(); 
                    currencyRateObj.prop("readonly", (currencyObj.val() == 1) ? true:false );
                    $( this ).dialog( "close" );
              }
          } 

        });	  
          
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

                                    tabObj.find("[name=hidCurrentCurrencyRate]" ).val(currencyRateObj.val()).blur();                                     }
                        }  
                    });
       
       thisObj.updateNumberDecimal();
            thisObj.updateTotalOutstandingDownpayment(); 
    }
   
     
       
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
     thisObj.calculateTotal();  
    } 

    
    this.updateOnDNTypeChanged = function updateOnDNTypeChanged(){
        var DNType = parseInt(tabObj.find("[name='selDNType']").val());
        var hideOnAP = tabObj.find(".hide-on-ap");
        var hideWithoutAP = tabObj.find(".hide-without-ap");
        var showWithoutAP = tabObj.find(".show-without-ap");
         
        if(DNType == 1) {
            hideOnAP.hide();
            hideWithoutAP.show();
            showWithoutAP.hide();
        } else if(DNType == 2) {
            hideOnAP.show();
            hideWithoutAP.show();
            showWithoutAP.hide();
        } else if(DNType == 3) {
            hideOnAP.show();
            hideWithoutAP.hide();
            showWithoutAP.show();
        }
    }
    
    this.rebindEl = function rebindEl(){   
         
         var handling = [];
         handling.onSelectFunction = 'getTabObj().updateDetail'; 
         var supplierKey = tabObj.find("[name=hidSupplierKey]").val();
         var currencykey = tabObj.find("[name=selCurrency]").val();
        
         bindAutoCompleteForTransactionDetail('refCode[]',  objAndValueForDetailAutoComplete,'ajax-debit-note.php?action=searchAPForDebitNote&supplierkey=' + supplierKey +'&currencykey='+ currencykey,handling);

        bindAutoCompleteForTransactionDetail('costName[]',  objAndValueForDetailCostAutoComplete,'ajax-item.php?action=searchData&itemtype=3',handling);         bindEl(tabObj.find("[name='debitTotal[]']" ), 'change',  function(){ thisObj.calculateTotal() }); 
         bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });
         bindEl(tabObj.find("[name='selDNType']"),'change', function() { thisObj.updateOnDNTypeChanged(); });
        
    }  
    
    this.loadOnReady = function loadOnReady(){

        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)}); 
        tabObj.find("[name=selCurrency]").change(function() { thisObj.updateCurrency(); });
        tabObj.find("[name='chkPick-master']").val(1).change();     
        thisObj.rebindEl(); 
        
        tabObj.find("[name='selDNType']").change();

    }
    
}
