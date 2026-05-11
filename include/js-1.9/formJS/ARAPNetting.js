function ARAPNetting(tabID,tablekey, rs,varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     
    this.tablekey = tablekey;  
    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.customerTax = 0;
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});
    objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'}); 
    var objAndValueForDPDetailAutoComplete  = objAndValue; 
    
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
    var objAndValueForCostDetailAutoComplete  = objAndValue; 

    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidARKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'}); 
    objAndValue.push({object:'arOutstanding[]', value :'outstanding'}); 
    objAndValue.push({object:'arAmount[]', value :'outstanding'}); 
    var objAndValueForDetailAutoComplete = objAndValue;
    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidAPKey[]', value :'pkey'});   
    objAndValue.push({object:'refAPCode[]', value :'refcode'}); 
    objAndValue.push({object:'apOutstanding[]', value :'outstanding'}); 
    objAndValue.push({object:'apAmount[]', value :'outstanding'}); 
    var objAndValueForDetailAPAutoComplete = objAndValue;
 
    this.resetDetails = function resetDetails(){  
        thisObj.resetARDetails(); 
        thisObj.resetAPDetails();
    }

    
    this.resetARDetails = function resetARDetails(){  
        clearAllRows(tabObj.find(".mnv-ar"));  
        thisObj.calculateTotalAR();  
    }
    
    this.resetAPDetails = function resetAPDetails(){  
        clearAllRows(tabObj.find(".mnv-ap"));  
        thisObj.calculateTotalAP();  
    }
     
     this.updateDetailAR = function updateDetailAR(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
            }

            detailRow.find("[name=\"arCode[]\"]").first().val(ui.item['code']);
            thisObj.calculateTotalAR();   
    }
     
     this.updateDetailAP = function updateDetailAP(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
            }

            detailRow.find("[name=\"apCode[]\"]").first().val(ui.item['code']);
            thisObj.calculateTotalAP();   
    }
     
  this.calculateTotal = function calculateTotal(){     
        var totalar = 0;
        var totalap = 0;
        var taxap = 0;
        var taxar = 0;   
         
        totalar = parseFloat(unformatCurrency(tabObj.find("[name='totalARAmount']").val())); 
        taxar = parseFloat(unformatCurrency(tabObj.find("[name='totalTaxARAmount']").val())); 
        totalap = parseFloat(unformatCurrency(tabObj.find("[name='totalAPAmount']").val())); 
        taxap = parseFloat(unformatCurrency(tabObj.find("[name='totalTaxAPAmount']").val())); 
        var grandtotalar = totalar - taxar;
        tabObj.find("[name='grandtotalARAmount']").val(grandtotalar).blur();
        var grandtotalap = totalap - taxap;
        tabObj.find("[name='grandtotalAPAmount']").val(grandtotalap).blur(); 
       
    } 
    
    this.calculateTotalTaxAR = function calculateTotalTaxAR(){
        var amount = 0;      

        tabObj.find("[name='hidARKey[]']").each(function(){    
            var objAmount = $(this).closest(".div-table-row").find("[name='arTax23[]']"); 
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })
        
        tabObj.find("[name='totalTaxARAmount']").val(amount).blur(); 
        thisObj.calculateTotal();
    }
    
    this.calculateTotalTaxAP = function calculateTotalTaxAP(){
        var amount = 0;      

        tabObj.find("[name='hidAPKey[]']").each(function(){    
            var objAmount = $(this).closest(".div-table-row").find("[name='apTax23[]']"); 
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })
        
        tabObj.find("[name='totalTaxAPAmount']").val(amount).blur(); 
        thisObj.calculateTotal();
    }
    
    this.calculateTotalAR = function calculateTotalAR(){
        var amount = 0;      

        tabObj.find("[name='hidARKey[]']").each(function(){    
            var objAmount = $(this).closest(".div-table-row").find("[name='arAmount[]']"); 
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })
        
        tabObj.find("[name='totalARAmount']").val(amount).blur(); 
        thisObj.calculateTotal();
    }  
    
    this.calculateTotalAP = function calculateTotalAP(){
        var amount = 0;      

        tabObj.find("[name='hidAPKey[]']").each(function(){    
            var objAmount = $(this).closest(".div-table-row").find("[name='apAmount[]']");
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })
        
        tabObj.find("[name='totalAPAmount']").val(amount).blur(); 
        thisObj.calculateTotal();
    }


    this.rebindAR = function rebindAR(){  
        bindEl(tabObj.find("[name='arTax23[]']"),'change', function() { thisObj.calculateTotalTaxAR(); });
        bindEl(tabObj.find("[name='arAmount[]']"),'change', function() { thisObj.calculateTotalAR(); }); 
        bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete,'ajax-ar.php?action=searchData&customerkey=' + tabObj.find("[name=hidCustomerKey]").val(),thisObj.updateDetailAR); 
    } 
    
    this.rebindAP = function rebindAP(){ 
        bindEl(tabObj.find("[name='apTax23[]']"),'change', function() { thisObj.calculateTotalTaxAP(); });
        bindEl(tabObj.find("[name='apAmount[]']"),'change', function() { thisObj.calculateTotalAP(); }); 
        bindAutoCompleteForTransactionDetail('apCode[]',  objAndValueForDetailAPAutoComplete,'ajax-ap.php?action=searchData&supplierkey=' + tabObj.find("[name=hidSupplierKey]").val(),thisObj.updateDetailAP); 
    } 

    this.importDataAR = function importDataAR(){ 
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
         
        var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();
        if(!customerkey) return;
        
        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
                 
        var ajaxData = "action=searchData&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&customerkey=" + customerkey +" "+dateParam;  
          
        $.ajax({
            type: "GET",
            url:  'ajax-ar.php',
            data: ajaxData,
            }).done(function( data ) { 
                    var data = JSON.parse(data);
                    if(data.length == 0){ 
                        addNewTemplateRow("ar-row-template"); 
                        alert(phpErrorMsg[213])
                        return;
                    }
                    var i;
                    for(i=0;i<data.length;i++){  
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidARKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"arCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"arRefCode", "value":data[i].refcode});   
                            arrPostValue.push({"selector":"arRefCode2", "value":data[i].refcode2});   
                            arrPostValue.push({"selector":"arOutstanding", "value":data[i].outstanding});  
                            arrPostValue.push({"selector":"arAmount", "value":data[i].outstanding});  
                         
                            addNewTemplateRow("ar-row-template",JSON.stringify(arrPostValue));  
                    }
 
                   thisObj.rebindAR(); 
 
                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();
 
                thisObj.calculateTotalAR();
            
        }); 
    }
    
    
    this.importDataAP = function importDataAP(){ 
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
 
        var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val();
        if(!supplierkey) return;
        
        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
          
        var ajaxData = "action=searchData&currencykey=" + tabObj.find("[name=selCurrency]").val() + "&supplierkey=" + supplierkey +" "+dateParam;
            
        $.ajax({
            type: "GET",
            url:  'ajax-ap.php',
            data: ajaxData,
            }).done(function( data ) {  
                    var data = JSON.parse(data); 
                    if(data.length == 0){ 
                        addNewTemplateRow("ap-row-template"); 
                        alert(phpErrorMsg[213])
                        return; 
                    } 
                    var i;
                    for(i=0;i<data.length;i++){  
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidAPKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"apCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"apRefCode", "value":data[i].refcode});  
                            arrPostValue.push({"selector":"apRefCode2", "value":data[i].refcode2});  
                            arrPostValue.push({"selector":"apOutstanding", "value":data[i].outstanding});
                            arrPostValue.push({"selector":"apAmount", "value":data[i].outstanding}); 
                          
                            addNewTemplateRow("ap-row-template",JSON.stringify(arrPostValue));  
                    }
 
                   thisObj.rebindAP(); 

                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();
 
                thisObj.calculateTotalAP(); 
        }); 
    }
        
    this.importData = function importData(){ 
        
        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        thisObj.activeAjaxConnections++; 
        
        thisObj.resetDetails(); 
        thisObj.importDataAR();
        thisObj.importDataAP(); 
                
        decreaseActiveAjaxConnections(thisObj); 
    }
  
	this.updateSupplierLink = function updateSupplierLink(){
		
		var customerkey =  tabObj.find("[name=hidCustomerKey]").val();
		
		 $.ajax({
					type: "GET",
					url:  'ajax-customer.php', 
			 		async: false, // biar gk error pas autofill dr customer
					data: "action=getSupplierLink&pkey=" + customerkey,  
					beforeSend:function (xhr){ 
						// gk boleh reset disini, kalo link supplier ketemu baru direset
//						supplierkey.val(0); 
//						supplierName.val(0); 
					},
					success: function(data){   
						data = parseJSON(data); 
						if(data.length == 0) {
							return;
						}else{  
							var field = 'supplierName';
							tabObj.find("[name=hidSupplierKey]").val(data[0].supplierkey); 
							tabObj.find("[name=supplierName]").val(data[0].suppliername);
							
							thisObj.rebindAP(); 
						 }
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
                        thisObj.rebindAR(); 
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
 
                            thisObj.updateSupplierLink(); 
                            thisObj.resetARDetails(); 
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
                            
					thisObj.updateSupplierLink(); 
                     
                 } 	

                 thisObj.rebindAR(); 
            } 	 
 
    }
    
    this.updateSupplierInformation = function updateSupplierInformation(event, ui){
		
		return;
		
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
                        thisObj.rebindAP(); 
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
 
                            thisObj.resetAPDetails(); 
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

                 thisObj.rebindAP(); 
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
                   $( this ).dialog( "close" );
              },
              Cancel : function (){  
                    tabObj.find("[name=selCurrency]").val(tabObj.find("[name=hidCurrentCurrencyKey]" ).val()); 
                    $( this ).dialog( "close" );
              }
          } 

        });	  
          
    }    
    
    /*this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    }*/
 
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){ 
     thisObj.calculateTotalAR(); 
     thisObj.calculateTotalAP(); 
    } 

    this.rebindEl = function rebindEl(){ } 
    
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
    }

    this.loadOnReady = function loadOnReady(){

        //tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
        //tabObj.find("[name=btnAddPayment]").bind( "click", function(event) {  thisObj.onCLickAddPayment(); }) 
         
        tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]"); 
            dateObj.removeClass("force-readonly"); 
            dateObj.datepicker((checked) ? "enable" : "disable");  
            if(!checked) dateObj.addClass("force-readonly");
         })  
        
        tabObj.find("[name=chkDatePeriod]").change();
        tabObj.find("[name=selCurrency]").change(function() { thisObj.updateCurrency();  thisObj.onChangeCurrency();});
        
        
         tabObj.find(".arap-show-detail").on('click', function() { 
            var $obj = $(this).closest(".arap-col").find(".options-row"); 
              
            $obj.css('display',($obj.is(":visible")) ? 'none' : 'table'); 
            
            var temp = $(this).attr("alt");        
            $(this).attr("alt",$(this).html());
            $(this).html(temp);
             
        });
        
        /*
        // INI SUDAH ADA DI TIPE PERIODE
        tabObj.find("[name=trStartDate], [name=trEndDate]").bind( "change",function() { 
            var trStartDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val()));
            var trEndDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val()));
               
            if (trStartDate > trEndDate) 
                tabObj.find("[name=trEndDate]").val(tabObj.find("[name=trStartDate]").val()); 
            
		}); */
          
          
        tabObj.find(" [name=btnAddARRows]").on('click', function() {
            addNewTemplateRow("ar-row-template"); 
            thisObj.rebindAR(); 
	     });
        
        tabObj.find(" [name=btnAddAPRows]").on('click', function() {
            addNewTemplateRow("ap-row-template"); 
            thisObj.rebindAP(); 
        });
        
        thisObj.calculateTotalAR();
        thisObj.calculateTotalAP();
         
        if(!this.rs){ 
            addNewTemplateRow("ar-row-template");  
            addNewTemplateRow("ap-row-template");  
        }
		 
        
        thisObj.rebindEl(); 
        thisObj.rebindAR(); 
        thisObj.rebindAP();
         
    }
}
