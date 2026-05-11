function EMKLPurchaseOrder(tabID,tablekey,cashTOP,varConstant) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        var taxRounded = varConstant.TAX_ROUND_TYPE ?? 4;
        this.tabID = tabID;
        this.tablekey = tablekey; 

        var fileFolder = varConstant.uploadFileFolder;
		var fileUploaderTarget = "item-file-uploader";
		var rsFile = varConstant.rsFile;
        var arrFile = Array(); 

        var id = tabObj.find("[name=hidId]").val();  
    
        var firstOpened = true;
        var objAndValue = new Array;
		objAndValue.push({object:'hidSupplierDetailKey[]', value :'pkey'});  
        var objAndValueSupplierAutoComplete = objAndValue;
            
    	var objAndValue = new Array;
		objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue;
            
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidServiceKey[]', value :'pkey'});   
        var objAndValueForDetailServiceAutoComplete = objAndValue;
    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidSalesDetailKey[]', value :'pkey'});  
        var objAndValueSalesDetailAutoComplete = objAndValue;
              
        this.updateFromJobOrder = function updateFromJobOrder(jobType){   
                var pkey = tabObj.find("[name=hidJobOrderKey]").val();
                 
                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-job-order.php', 
                    data: "action=getDataRowById&jobtype="+jobType+"&pkey=" + pkey ,  
                }).done(function( data ) { 
                      
                    data = JSON.parse(data) ; 
                     
                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];
                     
                    // dibuka dulu biar bisa ketrigger onchange nya
                    //tabObj.find("[name=selContainerType] option").prop('disabled', false);
                    
                    updateComboboxReadonly(tabObj.find("[name=selContainerType]"),false);
                    
                    //tabObj.find("[name=trDate]").val(moment(data.trdate).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=selTypeOfJob]").val(data.jobtypekey);
                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey).change();  
                    tabObj.find("[name=volume]").val(data.volume).blur(); 
                    tabObj.find("[name=selVolumeType]").val(data.volumetype);
                    tabObj.find("[name=selWarehouseKey]").val(data.warehousekey); 
                    tabObj.find("[name=containerName]").val(decodeHTMLEntities(data.containername));
                    tabObj.find("[name=bookingNumber]").val(data.bookingnumber);
                    tabObj.find("[name=shipperName]").val(data.customername);
                    tabObj.find("[name=poNumber]").val(data.ponumber);
                    tabObj.find("[name=mblNumber]").val(data.mblnumber); 
                    tabObj.find("[name=containerNumber]").val(data.containernumber); 
                    tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_));   
                    tabObj.find("[name=pol]").val(data.polname); 
                    tabObj.find("[name=pod]").val(data.podname); 
                    tabObj.find("[name=terminal]").val(data.terminalname);                     
                    tabObj.find("[name=depot]").val(data.depotname);  
                    tabObj.find("[name=ajuNumber]").val(data.aju);                  
                    tabObj.find("[name=pibRegistrationNumber]").val(data.pibregistrationnumber);                  
//                    tabObj.find("[name=location]").val(data.locationname);                    
//                    tabObj.find("[name=hidLocationKey]").val(data.locationkey);                                      
                    tabObj.find("[name=location]").val(data.stuffinglocation);           
                    updateComboboxReadonly(tabObj.find("[name=selTypeOfJob]"));
                    tabObj.find("[name=selTypeOfJob]").change();
                    
                    updateComboboxReadonly(tabObj.find("[name=selContainerType]"));
                    

                    thisObj.updateJobOrderVolumeInformation(data);
                    
                    thisObj.updateJobOrderDetail();
                });  
        }


    this.updateJobOrderDetail = function updateJobOrderDetail() {

        var pkey = tabObj.find("[name=hidJobOrderKey]").val();

        selJODetailObj = tabObj.find("[name='selJobOrderDetailKey[]']"); 
        
	   if (selJODetailObj.length === 0) return;
        
        $.ajax({
            type: "GET",
            url: 'ajax-emkl-job-order.php',
            data: 'action=getDetailById&pkey=' + pkey, 
        }).done(function (data) { 
 
            var data = parseJSON(data);

            var selectOpt = data;
            selectOpt.unshift({   pkey: "0",  code: "-----" });  
            
            reInsertSelectBox(selJODetailObj, selectOpt, { "key": "pkey", "label": "code" });
            
            // Check if second option exists  
            // berlaku utk semua detail
            if (selectOpt.length > 1)  {    
                selJODetailObj.prop('selectedIndex', 1); 
            }

        });

    }


    this.updateJobOrderVolumeDetail = function updateJobOrderVolumeDetail()
    {
   	thisObj.activeAjaxConnections = 0;

        var selJOType = tabObj.find("[name=selJOType]");
        var selTransactionType = selJOType.val();

        var ajaxUrl = (selTransactionType == varConstant.JOBTYPE.Order) ? 'ajax-emkl-job-order.php' : 'ajax-emkl-job-order-header';
        var ajaxData = (selTransactionType == varConstant.JOBTYPE.Order) ? "action=getContainerVolume&pkey=" + tabObj.find("[name=hidJobOrderKey]").val() : "action=getDetailById&pkey=" + tabObj.find("[name=hidJobHeaderKey]").val();
 
        $.ajax({
            type: "GET",
            url:  ajaxUrl,
            beforeSend:function (xhr){
                //clearAllRows(tabObj.find(".mnv-container-volume")); // gk bisa pake ini karena gk ad icon delete
                tabObj.find(".mnv-container-volume .transaction-detail-row").remove();
                thisObj.activeAjaxConnections++; 
            }, 
            data:ajaxData,
            success: function(data){ 
                                    
                var data = JSON.parse(data);
                            
                if(data.length == 0){ 
                    addNewTemplateRow("volume-row-template",'','',thisObj.rebindEl);  
                    tabObj.find(".mnv-container-volume input,.mnv-container-volume select").prop("disabled",true); 
                    return;
                }     

                var i;
                
                for(i=0;i<data.length;i++){ 
                    var arrPostValue = []; 
                    arrPostValue.push({"selector":"qtyVolume", "value":data[i].qty});
                    arrPostValue.push({"selector":"selContainerDetailVolumeKey", "value":data[i].itemkey}); 
                    addNewTemplateRow("volume-row-template",JSON.stringify(arrPostValue));
                } 

                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();
                tabObj.find(".mnv-container-volume input,.mnv-container-volume select").prop("disabled",true);

                decreaseActiveAjaxConnections(thisObj);

            },
            error: function(xhr, errDesc, exception) { 
                decreaseActiveAjaxConnections(thisObj); 
            }
        });

    }
 
    this.updateJobOrderVolumeInformation =  function updateJobOrderVolumeInformation(data)
    {
 
        var selJOType = tabObj.find("[name=selJOType]");
        var selTransactionType = selJOType.val();
 
        var selContainerObj = tabObj.find("[name=selContainerType]");  
        var fclOnlyObj = tabObj.find(".fcl-only");
        var lclOnlyObj = tabObj.find(".lcl-only");

        var containerType = selContainerObj.val(); 
        
         if (containerType == varConstant.EMKL.container.lcl ||
               containerType == varConstant.EMKL.container.lclnc ||
               containerType == varConstant.EMKL.container.freightcustomlcl ||
               containerType == varConstant.EMKL.container.customlcl){ 
             
                //gk perlu, sudah diudpate di jobtype
                //lclOnlyObj.show();
                //fclOnlyObj.hide(); 
                //
                //$(".fcl-readonly").attr("readonly", false);
                
                tabObj.find("[name=weight]").val(data.weight).blur();
                tabObj.find("[name=volume]").val(data.volume).blur();
                tabObj.find("[name=hidContainerKey]").val(data.itemkey).change();
             
            }else{
                thisObj.updateJobOrderVolumeDetail();
            }  

    }
            
 
	 this.updateFromJobHeader = function updateFromJobHeader(jobType){   
                var pkey = tabObj.find("[name=hidJobHeaderKey]").val();
                 
                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-job-order-header.php', 
                    data: "action=getDataRowById&jobtype="+jobType+"&pkey=" + pkey ,  
                }).done(function( data ) { 
                      
                    data = JSON.parse(data) ; 
                     
                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];
                    
                    updateComboboxReadonly(tabObj.find("[name=selContainerType]"),false);
                    
                    //tabObj.find("[name=trDate]").val(moment(data.trdate).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=selTypeOfJob]").val(data.jobtypekey);
                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey).change();  
                    tabObj.find("[name=volume]").val(data.volume).blur(); 
		            tabObj.find("[name=selWarehouseKey]").val(data.warehousekey);
                    tabObj.find("[name=selVolumeType]").val(data.volumetype);
                    tabObj.find("[name=containerName]").val(decodeHTMLEntities(data.containername));
                    tabObj.find("[name=bookingNumber]").val(data.bookingnumber);
                    tabObj.find("[name=shipperName]").val(data.customername);
                    tabObj.find("[name=poNumber]").val(data.invoicenumber);
                    tabObj.find("[name=mblNumber]").val(data.bookingnumber); 
                    tabObj.find("[name=containerNumber]").val(data.containernumber); 
                    tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_));  
                    tabObj.find("[name=customerName]").val(data.customername);  
                    tabObj.find("[name=pol]").val(data.polname); 
                    tabObj.find("[name=pod]").val(data.podname); 
                    //tabObj.find("[name=terminal]").val(data.terminalname);                     
                    //tabObj.find("[name=depot]").val(data.depotname);                  
//                    tabObj.find("[name=location]").val(data.locationname);                    
//                    tabObj.find("[name=hidLocationKey]").val(data.locationkey);                                      
                    tabObj.find("[name=location]").val(data.stuffing);           
                    updateComboboxReadonly(tabObj.find("[name=selTypeOfJob]"));
                    tabObj.find("[name=selTypeOfJob]").change();
                     
                    
                    updateComboboxReadonly(tabObj.find("[name=selContainerType]"));
                    
                    thisObj.updateJobOrderVolumeInformation(data);
                });  
        }
        
        this.updateJobType = function updateJobType(){
           // kalo LCL gk ad supplier dan conginee 
            var selContainerObj = tabObj.find("[name=selContainerType]");  
            var fclOnlyObj = tabObj.find(".fcl-only");
            var lclOnlyObj = tabObj.find(".lcl-only");
            var supplierDetailRow = tabObj.find(".supplier-row").not(".row-template");
            
            var containerType = selContainerObj.val();  
            
            if (containerType == varConstant.EMKL.container.lcl ||
               containerType == varConstant.EMKL.container.lclnc ||
               containerType == varConstant.EMKL.container.freightcustomlcl ||
               containerType == varConstant.EMKL.container.customlcl){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                
                $(".fcl-readonly").attr("readonly", false);
            }else{
                lclOnlyObj.hide();
                fclOnlyObj.show();
                
                $(".fcl-readonly").attr("readonly", true);
            }  
        }
        
        this.updateTransactionType = function updateTransactionType(){ 
            var selJOType = tabObj.find("[name=selJOType]");  
            var headerObj = tabObj.find(".isheader");
            var orderObj = tabObj.find(".isorder");
            
            var transactionType = selJOType.val(); 
             
            if (transactionType == varConstant.JOBTYPE.Order){ 
                headerObj.hide();
                orderObj.show(); 
            }else{
                headerObj.show();
                orderObj.hide(); 
            }  
        }

        this.updateVolumeType = function updateVolumeType(){
            var volumeTypeObj = tabObj.find(".volume-type").html( tabObj.find("[name=selVolumeType]").find("option:selected").text() );
        }
         
        this.getRowObj = function getRowObj(obj){
            return obj.closest(".div-table-row");
        } 
         
        /*this.updateNumberDecimal = function updateNumberDecimal(){    
            var selCurrencyObj = tabObj.find("[name='selCurrency']");
            var isNumber = (selCurrencyObj.val() == varConstant.CURRENCY.idr) ? true : false; 
            changeNumberDecimal(tabObj.find("[name='detailSubtotal[]'],[name='total'],[name='totalPayment'],[name='paymentMethodValue[]'] ,[name='balance'], [name='subtotal'], [name='beforeTaxTotal'], [name='taxValue']"),isNumber); 
        }*/
        
        /*this.updateRate = function updateRate(){
            
            var selCurrencyObj = tabObj.find("[name='selCurrency']")
            var currencyRateObj =  tabObj.find("[name='currencyRate']");
            
            $.ajax({
                        type: "GET",
                        url:  'ajax-currency-rate.php', 
                        data: "action=getLastRate&currencykey=" + selCurrencyObj.val(),  
                        beforeSend:function (xhr){ 
                            //currencyRateObj.val(1); 
                        },
                        success: function(data){  
                                if(data){
                                     var data = JSON.parse(data);   
                                     currencyRateObj.val(data[0]['rate']).blur();
                                     thisObj.onChangeCurrencyRate();
                                }
                        }  
                    });
        
        
        } */
        
        this.onChangeCurrency = function onChangeCurrency(){
            // ganti currency tdk mengganti rate, karena didetail bisa pilih USD
            // kecuali nanti kalo lebih dr 2 currency
            
            var selCurrencyObj = tabObj.find("[name='selCurrency']")
            var currencyRateObj =  tabObj.find("[name='currencyRate']");
 		    var detailCurrency =  tabObj.find("[name='selCurrencyDetail[]']"); 
              
            detailCurrency.val(selCurrencyObj.val());
            
            // gk bisa pake, karena ada tagihan, header pilih IDR, tp detailnya diisi USD (tp mau dibayarkan sebagai IDR), jd ratenya gk boleh 1
            // apakah boleh diakfitfkan, tp jgn readonly. jg kalo IDR bisa diisi jg
            
//  =====          
//            var changeFlag = false;
//            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){ 
//                changeFlag = true;
//                currencyRateObj.val(1);
//            }
//             
//            // karena detailnya bisa pilih usd jg, jd harus ad rate
//            currencyRateObj.prop("readonly", changeFlag); 
// ======			
            
            
            tabObj.find(".mnv-active-currency").html(selCurrencyObj.find("option:selected").text());
            
            // dipisah agar dapat dipanggil ketika onload tanpa pengaruh ke nilai rate dll
            //thisObj.updateAvailableCurency(); 
            
            currencyRateObj.change().blur();
            //thisObj.updateNumberDecimal();
            
// =======                       
            
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
                                 thisObj.onChangeCurrencyRate();
                            }
                    }  
                });
// ======	
        }
             
         this.updateAvailableCurency = function updateAvailableCurency(){ 
            var selCurrencyObj = tabObj.find("[name='selCurrency']")
            var currencyRateObj =  tabObj.find("[name='currencyRate']");
            //var detailCurrency =  tabObj.find("[name='selCurrencyDetail[]']"); 
             
			var changeFlag = false;
			 
            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){  
               // detailCurrency.find("option:not(:selected)").attr('disabled', true);
				 
				changeFlag = true;
				currencyRateObj.val(1); 
				
            }else{   
                //detailCurrency.find("option").attr('disabled', false);
            }
                  
			  currencyRateObj.prop("readonly", changeFlag); 
			 
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
            var rate = parseFloat(unformatCurrency(tabObj.find("[name='currencyRate']").val())) || 0;   
            
            var qty = parseFloat(unformatCurrency(rowObj.find("[name='qty[]']").val())) || 0;
            var priceInUnit = parseFloat(unformatCurrency(rowObj.find("[name='priceInUnit[]']").val())) || 0;   
            var subtotal = qty * priceInUnit; 
            
            rowObj.find("[name='detailRowCurrencySubtotal[]']").val(subtotal).blur();
            
            var selCurrencyDetailObj = rowObj.find("[name='selCurrencyDetail[]']"); 
            var isNumber = (selCurrencyDetailObj.val() == varConstant.CURRENCY.idr) ? true : false;

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
             
            //changeNumberDecimal(rowObj.find("[name='priceInUnit[]'],[name='detailRowCurrencySubtotal[]']"),isNumber);

            thisObj.calculateTotal();
        }
        
        this.calculateTotal = function calculateTotal(){    
            var subtotal = 0; 
            var totalPPH = 0;
            var selCurrencyObj = tabObj.find("[name='selCurrency']");
            
            tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseFloat(unformatCurrency($(this).val())) || 0;  })
            tabObj.find("[name='detailPPHAmount[]']").each(function(){ totalPPH += parseFloat(unformatCurrency($(this).val())) || 0;  })
            
            tabObj.find("[name='subtotal']").val(subtotal).blur();
            tabObj.find("[name='totalPPH']").val(totalPPH).blur();
            
            var includeTax =    tabObj.find("[name='chkIncludeTax']").val();
            var taxPercentage =  parseFloat(unformatCurrency( tabObj.find("[name='taxPercentage']").val())) || 0 ;  
            
            tabObj.find("[name='beforeTaxTotal']").val(subtotal).blur();
             
            var taxValue = 0;
            if (includeTax == 0) {
                taxValue = subtotal * taxPercentage / 100;
                if (selCurrencyObj.val() == varConstant.CURRENCY.idr)
                    taxValue = getInvoiceRoundedTax(taxValue,taxRounded);
                subtotal += taxValue;
            }else{
                taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                if (selCurrencyObj.val() == varConstant.CURRENCY.idr)
                    taxValue = getInvoiceRoundedTax(taxValue,taxRounded);
                tabObj.find("[name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
            }

	    // khusus IDR           
           

            tabObj.find("[name='taxValue']").val(taxValue).blur(); 
                       
            var total = subtotal;
            tabObj.find("[name='total']").val(total).blur();
            
            var totalPayment = parseFloat(unformatCurrency(tabObj.find("[name='totalPayment']").val()));
            
            var balance = totalPayment - total;
            
            
            // cek ad pph tdk, dan hanya jika tunai baru dakui
            var isCash = false;
            tabObj.find("[name=selTermOfPaymentKey]" ).each(function(i, obj) {
                
				for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i])    
                        return; 
                }
                 
                 isCash = true;
 
            }); 
            
            if(isCash) 
                balance += totalPPH;
            
            
            tabObj.find("[name='balance']").val(balance).blur();
            
        }
         
 
        this.updateSupplierInformation =  function updateSupplierInformation (topkey){
            if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + topkey + "']").length > 0)
                tabObj.find("[name=selTermOfPaymentKey]").val(topkey).change();  
        }
        
        this.updateDetailSupplierMasterPrice = function updateDetailSupplierMasterPrice(){
            tabObj.find("[name='serviceName[]']:not(:disabled)").each(function() {thisObj.updateSupplierMasterPrice($(this))}); 
        }
        
        
		this.updateSupplierMasterPrice = function updateSupplierMasterPrice(obj){
			var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val();
			var locationkey = tabObj.find("[name=hidLocationKey]" ).val();
            
			var detailRow = $(obj).closest(".transaction-detail-row");
			var servicekey = detailRow.find("[name=\"hidServiceKey[]\"]").val();
			var containerkey = detailRow.find("[name=\"hidContainerDetailKey[]\"]").val(); 
			var currencykey =  detailRow.find("[name=\"selCurrencyDetail[]\"]" ).val();
            
			detailRow.find("[name=\"priceInUnit[]\"]").val(0);
            
			$.ajax({
                type: "GET",
                url:  'ajax-supplier.php',
                async : false,
                data: "action=getSupplierPrice&pkey=" + supplierkey +'&locationkey='+locationkey+'&servicekey=' + servicekey +'&containerkey=' + containerkey +'&currencykey=' + currencykey ,  
            }).done(function( data ) {
                
                if(!data) return; 
                data = JSON.parse(data);
                
                if(!data[0]) return;
                
                 data = data[0]; 
                
                 detailRow.find("[name=\"priceInUnit[]\"]").val(data.price).change().blur(); 
            }); 
			
			  
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
                           
                                if(!data[0]) return;
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
		
		this.importData = function importData(obj){  
               
            var templatecostkey = $(obj).attr("relkey");
    
	        $.ajax({
	            type: "GET",
	            url:  'ajax-template-purchase-item.php',
	            beforeSend:function (xhr){ 
                    clearAllRows(tabObj.find(".mnv-transaction"));
	            }, 
	            //data: 'action=searchDataForInvoice&statustype='+statustype+'&customerkey=' + customerkey+'&currencykey='+currencykey, 
	            data: 'action=getDetailById&pkey=' + templatecostkey, 
	            success: function(data){ 
	                    var data = JSON.parse(data);  
	                    var i;
                        var newrow;
                     
	                    for(i=0;i<data.length;i++){ 
	                            var arrPostValue = []; 
	                            arrPostValue.push({"selector":"hidServiceKey", "value":data[i].itemkey}); 
	                            arrPostValue.push({"selector":"serviceName", "value":data[i].costname});  
	                            //arrPostValue.push({"selector":"salesOrderDate", "value": moment(data[i].trdate).format(_DATE_FORMAT_) }); 
	                            newrow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
	                    } 
                    
                     thisObj.rebindEl();
                     thisObj.updateDetailSupplierMasterPrice();
   
	            } ,
	             error: function(xhr, errDesc, exception) { 
                         
                }
	        });
	    } 
        
        
        this.onChangeSupplier = function onChangeSupplier(){ 
            thisObj.updateTOP();
            thisObj.updateDetailSupplierMasterPrice();
        }
        
                
        this.onChangeJobOrder = function onChangeJobOrder(jobType){ 
            thisObj.updateFromJobOrder(jobType);
            thisObj.updateDetailSupplierMasterPrice();
        }
        
        this.onChangeJobHeader = function onChangeJobHeader(jobType){ 
            thisObj.updateFromJobHeader(jobType);
            //thisObj.updateDetailSupplierMasterPrice();
        }
        
        this.rebindEl = function rebindEl(){   
            bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailAutoComplete,'ajax-container.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForDetailServiceAutoComplete,'ajax-item.php?action=searchData&itemtype=3');     
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'],  [name='selCurrencyDetail[]'], [name='detailPPHAmount[]']"),'change',function(){ thisObj.calculateDetail(this) });  
		    bindEl(tabObj.find("[name='containerDetailName[]'],[name='serviceName[]'],[name='selCurrencyDetail[]']"),'change',function(){ thisObj.updateSupplierMasterPrice($(this)) }); 
        }
        
        
        this.loadOnReady = function loadOnReady(){  

            // cek ad gk elementnya, karena beberapa di personalized
           
            if(thisObj.useStorage){

            }else{ 
                 if ($(".item-file-uploader").length > 0){ 
                    if(id){    
                        for($i=0;$i<rsFile.length;$i++) 
                            arrFile.push(rsFile[$i].file); 

                        createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  

                    }else{  
                         createFileUploader(fileUploaderTarget, fileFolder, "", "", false);
                    }
                }
           }
            
		    //tabObj.find(".costTemplate").hide()  
            tabObj.find("[name=selTermOfPaymentKey]" ).change(function() {
                
				for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".pph-field").hide();
                        tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                }

               tabObj.find(".pph-field").show();
               tabObj.find(".cashTOP").show();
            }); 
			
			tabObj.find(".cost-template").click(  function() { thisObj.importData(this); });  
       
            tabObj.find("[name=selTermOfPaymentKey]" ).change();   
               
            tabObj.find("[name=beforeTaxTotal], [name=chkIncludeTax], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal(this)}) 
               
            //tabObj.find("[name=selAirSea]").change(function() { thisObj.updateAirOrSea(); });
            tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 
            tabObj.find("[name=selJOType]").change(function() { thisObj.updateTransactionType(); }); 
            tabObj.find("[name=selVolumeType]").change(function() { thisObj.updateVolumeType(); });
            tabObj.find("[name=selCurrency]").change(function() {  thisObj.onChangeCurrency(); thisObj.updateDetailSupplierMasterPrice(); });
            tabObj.find(".mnv-active-currency").html(tabObj.find("[name=selCurrency]").find("option:selected").text());
            tabObj.find("[name=currencyRate]").change(function() { thisObj.onChangeCurrencyRate(); });
             
            tabObj.find("[name=selJOType]" ).change();  
            thisObj.updateJobType();
            //thisObj.updateAirOrSea();
             
			// gk bisa pake, karena ada tagihan, header pilih IDR, tp detailnya diis USD (tp mau dibayarkan sebagai IDR), jd ratenya gk boleh 1
            //thisObj.updateAvailableCurency(); 
			
            if(tabObj.find('.mnv-container-volume .transaction-detail-row').length == 0){ 
                addNewTemplateRow("volume-row-template",null,null,thisObj.rebindEl);
                tabObj.find(".mnv-container-volume input,.mnv-container-volume select").prop("disabled",true); 
            }
            
            thisObj.rebindEl();
        }
}
