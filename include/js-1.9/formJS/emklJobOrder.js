function EMKLJobOrder(tabID,data,varConstant) {   
     
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;
        this.tablekey = varConstant.TABLEKEY || 0;
     
        this.autoUpdateEMKLJobOrderContainer = varConstant.autoUpdateEMKLJobOrderContainer || false;
    
        var LCLTYPE = varConstant.LCLTYPE || 0;
		var updateTaxAtJobOrder = varConstant.updateTaxAtJobOrder || 0;
	
        var objAndValue = new Array;
		objAndValue.push({object:'hidCustomerDetailKey[]', value :'pkey'});  
        var objAndValueCustomerAutoComplete = objAndValue;
            
    	var objAndValue = new Array;
		objAndValue.push({object:'hidDetailPODKey[]', value :'pkey'});  
        var objAndValuePODAutoComplete = objAndValue;
            
        var objAndValue = new Array;
		objAndValue.push({object:'hidCommissionRecipientKey[]', value :'pkey'});   
        var objAndValueCommissionRecipientAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidDestinationDetailKey[]', value :'pkey'});  
        var objAndValueDestinationAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidWarehouseDetailKey[]', value :'pkey'});  
        var objAndValueWarehouseAutoComplete = objAndValue;
            
    	var objAndValue = new Array;
		objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue;
        
        var objAndValue = new Array;
        objAndValue.push({object:'hidCommodityKey[]', value :'pkey'});   
        var objAndValueForDetailCommodityAutoComplete = objAndValue;
            
    	var objAndValue = new Array;
		objAndValue.push({object:'hidServiceKey[]', value :'pkey'});
	
		if(updateTaxAtJobOrder == 1){
			objAndValue.push({object:'dummychkIsReimburse[]', value :'reimburse', type:'checkbox'});   
			objAndValue.push({object:'chkIsReimburse[]', value :'reimburse'});      
		}
	
        var objAndValueForDetailServiceAutoComplete = objAndValue;
    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidSalesDetailKey[]', value :'pkey'});  
        var objAndValueSalesDetailAutoComplete = objAndValue;
                
        var objAndValue = new Array;
		objAndValue.push({object:'hidChargeToDetailKey[]', value :'pkey'});  
        var objAndValueChargeToAutoComplete = objAndValue;
            

        var objAndValue = new Array;
		objAndValue.push({object:'hidUnitDetailKey[]', value :'pkey'});  
        var objAndValueUnitDetailAutoComplete = objAndValue;
        this.updateCommissionDetail = function updateCommissionDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
             
            var selRecipientType = detailRow.find("[name=\"selCommissionRecipientType[]\"]").first();

            for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
  
            selRecipientType.val(ui.item['suppliertype']);
            updateComboboxReadonly(selRecipientType);
            
            
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"commissionRecipientDetailName[]\"]").first().val(ui.item['value']); 
        }
        
        this.updateAirOrSea = function updateAirOrSea(){  
            var selAirSeaObj = tabObj.find("[name=selAirSea]");
            var selContainerObj = tabObj.find("[name=selContainerType]");
            var selVolumeTypeObj = tabObj.find("[name=selVolumeType]");

            var seaOnlyObj = tabObj.find(".sea-only");
            var airOnlyObj = tabObj.find(".air-only");
             
            if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea){
                //laut
                //selContainerObj.prop("disabled", false);
                selVolumeTypeObj.val(varConstant.EMKL.volume.cbm); 
                airOnlyObj.hide();
                seaOnlyObj.show();
            }else{ 
                //udara
                //selContainerObj.prop("disabled", true); 
                seaOnlyObj.hide();
                airOnlyObj.show();
                selVolumeTypeObj.val(varConstant.EMKL.volume.kg);
            }
            
            selContainerObj.change();
            selVolumeTypeObj.change();
         
        }
        
		this.onChangeQuotationOrder = function onChangeQuotationOrder(){ 
            thisObj.updateFromQuotationOrder();
        }

        this.updateFromQuotationOrder = function updateFromQuotationOrder(){   
            var pkey = tabObj.find("[name=hidQuotationKey]").val();

			// harusnay gk perlu, karean 1 table
			//+ "&jobtypekey="+jobType 
			
                $.ajax({
                    type: "GET",
                    async: false,
                    url:  'ajax-emkl-quotation-order.php', 
                    data: "action=getQuotationInformation&pkey=" + pkey  
                }).done(function( data ) {
					
					data = parseJSON(data);
                    if(data.length == 0)return;
                    data = data[0];
						
                    //tabObj.find("[name=trDate]").val(moment(data.trdate).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=selTypeOfJob]").val(data.jobtypekey);
                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey).change();  
                    tabObj.find("[name=selWarehouseKey]").val(data.warehousekey);  
                    tabObj.find("[name=shipperName]").val(data.customername);
                    tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
                    tabObj.find("[name=salesName]").val(data.salesname);
                    tabObj.find("[name=hidSalesKey]").val(data.saleskey);
                    tabObj.find("[name=itemDescription]").val(data.commoditydesc);
                        
        			tabObj.find("[name='hidCustomerDetailKey[]']").first().val(data.customerkey);
        			tabObj.find("[name='customerDetailName[]']").first().val(data.customername);
					
                    updateComboboxReadonly(tabObj.find("[name=selTypeOfJob]"));
                    tabObj.find("[name=selTypeOfJob]").change();
                    tabObj.find("select[readonly]").find("option:selected").attr('disabled', false);
                    tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
                    
					 
					if(data.freightdetail.length > 0){
						var freightDetail = data.freightdetail[0];
						
						tabObj.find("[name=hidPOLKey]").val(freightDetail.polkey);
						tabObj.find("[name=hidPODKey]").val(freightDetail.podkey);
						tabObj.find("[name=polName]").val(freightDetail.polname);
						tabObj.find("[name=podName]").val(freightDetail.podname);
						tabObj.find("[name=hidCarrierKey]").val(freightDetail.carrierkey);
						tabObj.find("[name=carrierName]").val(freightDetail.carriername); 
					}
					
					if(data.containerfreightdetail.length>0){
						var freightDetail = data.containerfreightdetail[0]; 
						tabObj.find("[name='selContainerDetailVolumeKey[]']").val(freightDetail.containerkey);  
					}

                    thisObj.updatePersonInCharge();
                });  
        }  
		
        this.updateJobType = function updateJobType(){
           // kalo LCL gk ad customer dan conginee 
            var selContainerObj = tabObj.find("[name=selContainerType]");  
            var selAirSeaObj = tabObj.find("[name=selAirSea]");
            var fclOnlyObj = tabObj.find(".fcl-only");
            var lclOnlyObj = tabObj.find(".lcl-only");
            var customerDetailRow = tabObj.find(".customer-row").not(".row-template");
      
            var containerType = selContainerObj.val(); 
            if(containerType == varConstant.EMKL.container.lcl ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                tabObj.find(".truckingfcl").hide();
                tabObj.find(".lcl-only").show();

                // kayanya udah gk kepake
               $(".fcl-readonly").attr("readonly", false); 
                
            }else if(containerType == varConstant.EMKL.container.lclnc ||
                     containerType == varConstant.EMKL.container.freightcustomlcl  ||
                     containerType == varConstant.EMKL.container.customlcl
                    ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                tabObj.find(".truckingfcl, .lcl-only").hide(); 
                tabObj.find(".lclnc").show(); 
                
            }else if(containerType == varConstant.EMKL.container.fcl || 
                     containerType == varConstant.EMKL.container.freightcustomfcl || 
					 containerType == varConstant.EMKL.container.trucking || 
					 containerType == varConstant.EMKL.container.customfcl){
                 
				lclOnlyObj.hide();
                fclOnlyObj.show();
                
                      
                $(".fcl-readonly").attr("readonly", true); 
                tabObj.find("[name=chkIsMaster]").val(0);
                tabObj.find(".truckingfcl").show();
                //tabObj.find(".lcl").hide();
				tabObj.find(".lclonly").hide();


                 // kalo jenisnya air, gk ad container
                 
                if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea)
                 tabObj.find(".sea-only").show(); 
                else
                 tabObj.find(".sea-only").hide(); 
            }else{
                lclOnlyObj.hide();
                fclOnlyObj.show();
                
                $(".fcl-readonly").attr("readonly", true); 
                tabObj.find("[name=chkIsMaster]").val(0);
                tabObj.find(".truckingfcl").hide();
                tabObj.find(".lcl").hide();
   
                
                 // kalo jenisnya air, gk ad container
                 
                if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea)
                 tabObj.find(".sea-only").show(); 
                else
                 tabObj.find(".sea-only").hide(); 
                
                
            }  
            
            thisObj.updateJobTypeLabel();
            tabObj.find("[name=chkIsMaster]").change();
        }
        
        this.updateJobTypeLabel = function updateJobTypeLabel(){
            
            var containerType = tabObj.find("[name=selContainerType] option:selected").html();
            var airSeaType = tabObj.find("[name=selAirSea] option:selected").html();
            
            tabObj.find(".label-container-type").html(containerType);
            tabObj.find(".label-air-sea").html(airSeaType);
        }
        
        
        this.updateFieldfForLCLDetail = function updateFieldfForLCLDetail(){
           
            // kalo statusnya sudah bkn menunggu, langsugn return saja
            
            var status = tabObj.find("[name=selStatus]").val();
            if (status > 2) return; // sementara
            
            
            var readonlyField = false;
            var containerType = tabObj.find("[name=selContainerType]").val(); 
            var isMaster = tabObj.find("[name=chkIsMaster]").val();
              
            var headerPanel = tabObj.find(".header-panel");
            
            if (containerType == varConstant.EMKL.container.lcl && isMaster == 0){ 
				
				// tgl kalo dipaksa readonly jadnya bisa dihapus
				// lagian LCL mungkin saja tglnya boleh berbeda
				
                readonlyField = true;
                headerPanel.find(".auto-complete .add-button").hide(); 
                headerPanel.find("[name=selAirSea],[name=selWarehouseKey]").not("[attr-header=\"true\"]").addClass("force-readonly"); 
            }else{ 
                headerPanel.find(".auto-complete .add-button").show(); 
                headerPanel.find("[name=selAirSea],[name=selWarehouseKey]").not("[attr-header=\"true\"]").removeClass("force-readonly");  
            }
            
            // code readonly sementara saja. harusnya tergantung settingan db
            headerPanel.find("input").not("[name=jobOrderCode], [name=trDate],[name=salesName],[name=code],[name=podName],[attr-header=\"true\"]").prop("readonly",readonlyField);    
//            headerPanel.find(".input-date").prop("disabled",readonlyField);
            headerPanel.find("[name=containerNumber]").prop("disabled",readonlyField);
            
            headerPanel.find("[name=selAirSea],[name=selWarehouseKey]").not("[attr-header=\"true\"]").attr('disabled', readonlyField);
             
            
            // select jobtype gk boleh disabled agar bisa divalidasi ulang di class
            //headerPanel.find("select").prop("disabled",readonlyField);
            
        }
        
        this.updateMasterDetailField = function updateMasterDetailField(obj){
			 // kalo master dan tipenya lcl-nc, return
			  
            var containerType = tabObj.find("[name=selContainerType]").val(); 
			if(containerType == varConstant.EMKL.container.lclnc) return;
            
            if (obj.val() == 1){
                tabObj.find(".doc-detail-only").hide();
                tabObj.find("[name=jobOrderCode]").prop("readonly",true);     
                tabObj.find("[name=hidJobOrderKey]").val(0);
                tabObj.find("[name=jobOrderCode]").val("");  
            }else{
                tabObj.find(".doc-detail-only").show();
                tabObj.find("[name=jobOrderCode]").prop("readonly",false);
            }
            
            thisObj.updateFieldfForLCLDetail();
            
        }
     
        this.updateHBLManually = function updateHBLManually(obj){
			
			// refhbl lempar ke panel aj, karena kalo chkbox repot ada dummynya
            var panelObj =  thisObj.getGroupObj(obj);
			
			detailHBLObj = panelObj.find("[name='detailHBL[]']");
			detailHBLKeyObj = panelObj.find("[name='hidDetailHBLKey[]']");
 
			
            if (obj.val() == 1){
				
				panelObj.attr("refhblcode",detailHBLObj.val());
				panelObj.attr("refhblkey",detailHBLKeyObj.val());
				
                panelObj.find(".add-hbl").hide();
                detailHBLObj.val('');
                detailHBLKeyObj.val(0);
                panelObj.find("[name='detailHBL[]']").prop("readonly",false);
				

            }else{
                panelObj.find("[name='detailHBL[]']").prop("readonly",true);
                
				// hanya jika sebelumnya bkn auto
				var hblLock = panelObj.find("[name=\"hblLock[]\"]").val();
				
				if (hblLock == 0){ 
					detailHBLObj.val('');
					detailHBLKeyObj.val(0);
					panelObj.find(".add-hbl").show(); 
				}else{
					detailHBLObj.val(panelObj.attr("refhblcode") || '');
					detailHBLKeyObj.val(panelObj.attr("refhblkey") || 0);
				}

            } 
            
        }
		
         this.updateNumberMasterHBL = function updateNumberMasterHBL(btnObj){
            
			 btnObj.hide();
             var panelObj =  thisObj.getGroupObj(btnObj);
            	//var consignename = tabObj.find("[name=consigneeName]").val(); 
             var jobType = tabObj.find("[name=selTypeOfJob]").val(); 
 
              $.ajax({
                    type: "POST",
                    url:  'ajax-emkl-hbl.php',
                    data: "action=addHBL",  
                    success: function(data){ 
                        data = JSON.parse(data) ; 
                        data  = data[0];
                        
                        panelObj.find("[name='detailHBL[]']").val(data['data']['code']);
                        panelObj.find("[name='hidDetailHBLKey[]']").val(data['data']['pkey']);
						panelObj.find("[name=\"hblLock[]\"]").val(1);
                    }
                });
            
        }
        
                this.updateFromJobOrder = function updateFromJobOrder(jobType){   
                var pkey = tabObj.find("[name=hidJobOrderKey]").val();
                  
                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-job-order.php',
                    async: false,
                    data: "action=getDataRowById&jobtype="+jobType+"&pkey=" + pkey ,  
                }).done(function( data ) { 
                      
                    if(!data) return;
                    
                    data = JSON.parse(data) ; 
                     
                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];
                    
                    tabObj.find("[name=selWarehouseKey]").val(data.warehousekey); 
                    tabObj.find("[name=trDate]").val(moment(data.trdate).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=poNumber]").val(data.ponumber);
                    tabObj.find("[name=bookingNumber]").val(data.bookingnumber); 
                    //tabObj.find("[name=selTypeOfJob]").val(data.jobtypekey);
                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey).change();   
                    tabObj.find("[name=containerName]").val(decodeHTMLEntities(data.containername));
                    //tabObj.find("[name=volume]").val(data.volume).blur(); 
                    tabObj.find("[name=selVolumeType]").val(data.volumetype);
                    tabObj.find("[name=trDesc]").val(data.trdesc); 
                    tabObj.find("[name=mblNumber]").val(data.mblnumber); 
                    tabObj.find("[name=polName]").val(data.polname); 
                    tabObj.find("[name=hidPOLKey]").val(data.polkey); 
                    tabObj.find("[name=podName]").val(data.podname); 
                    tabObj.find("[name=hidPODKey]").val(data.podkey); 
                    tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_));
                    tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_));
                    tabObj.find("[name=carrierName]").val(data.carriername); 
                    tabObj.find("[name=hidCarrierKey]").val(data.carrierkey); 
                    tabObj.find("[name=vesselNumber]").val(data.vesselnumber); 
                    tabObj.find("[name=vesselName]").val(data.vesselname); 
                    tabObj.find("[name=hidVesselKey]").val(data.vesselkey);
                    tabObj.find("[name=depotName]").val(data.depotname); 
                    tabObj.find("[name=hidDepotKey]").val(data.depotkey); 
                    tabObj.find("[name=containerNumber]").val(data.containernumber); 
                    tabObj.find("[name=agentName]").val(data.agentname); 
                    tabObj.find("[name=hidAgentKey]").val(data.agentkey);
                    tabObj.find("[name=shipperName]").val(data.customername);
                    tabObj.find("[name=hidShipperKey]").val(data.shipperkey); 
                    tabObj.find("[name=salesName]").val(data.salesname); 
                    tabObj.find("[name=hidSalesKey]").val(data.employeekey); 
                    tabObj.find("[name=hidCargoType]").val(data.containertypekey); 
                        
                    tabObj.find("select[readonly]").find("option:selected").attr('disabled', false);
                    tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
                    
                });  
        }
		
        this.updateVolumeType = function updateVolumeType(){
            var volumeTypeObj = tabObj.find(".volume-type").html( tabObj.find("[name=selVolumeType]").find("option:selected").text() );
        }
        
        this.updateCustomerInformation = function updateCustomerInformation(){ 
            
            // update salesman
            
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php', 
                    async : false,
                    data: "action=getSalesman&pkey=" + tabObj.find("[name=hidCustomerKey]").val(),  
                    beforeSend:function (xhr){ 
                        tabObj.find("[name=hidSalesKey]").val(0);
                        tabObj.find("[name=salesName]").val("");
                    },
                success: function (data) {  
                        data = parseJSON(data); 
                        if(data.length == 0)return;  
                        
                       tabObj.find("[name=hidSalesKey]").val(data.pkey);
                       tabObj.find("[name=salesName]").val(data.name);
                         
                    }  
                });
            
            
            // gk tau siapa yg pake
            
//            var selContainerObj = tabObj.find("[name=selContainerType]");    
//            if (selContainerObj.val() == varConstant.EMKL.container.fcl )  
//                thisObj.updateDetailName(tabObj.find(".customer-row").first());
        }
        
        this.updateDetailName = function updateDetailName(detailRow){  
            detailRow.find("[name='customerDetailName[]']").val( tabObj.find("[name=customerName]").val() );
            detailRow.find("[name='hidCustomerDetailKey[]']").val( tabObj.find("[name=hidCustomerKey]").val() );
        }
        
        this.removeContainerType = function afterAddNewTemplateRowHandler(){
            
        }

        this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(newRow){
            //get previous row
            prevRow = newRow.prev();
            var containerName = prevRow.find("[name='containerDetailName[]']").val();
            var containerKey = prevRow.find("[name='hidContainerDetailKey[]']").val();
            var currencyKey = prevRow.find("[name='selCurrencyDetail[]']").val();
            var containerTypeDetailkey =  prevRow.find("[name='selContainerTypeDetail[]']").val();
            
            newRow.find("[name='containerDetailName[]']").val(containerName);
            newRow.find("[name='hidContainerDetailKey[]']").val(containerKey);
            newRow.find("[name='selCurrencyDetail[]']").val(currencyKey);
            newRow.find("[name='selContainerTypeDetail[]']").val(containerTypeDetailkey);
              
            thisObj.bindAutoUpdateContainer();
        }
        
        this.getGroupObj = function getGroupObj(obj){
            return obj.closest(".customer-row");
        }
         
        this.getRowObj = function getRowObj(obj){
            return obj.closest(".div-table-row");
        }
        
          this.calculateDetailGroupSubtotal =  function calculateDetailGroupSubtotal(panelObj){ 
                 
            var subtotalCurrency = 0; 
            var subtotalCurrencyInIdr = 0; 
            var subtotalOtherCurrency = 0;  
            var subtotal = 0;  
            var hasOtherCurrency = false;
              
            var headerCurrency = panelObj.find("[name='selSellingCurrency[]']").val();
            var rate = panelObj.find("[name='sellingCurrencyRate[]']").val();
            
            panelObj.find(".detail-item .transaction-detail-row").each(function() {
            
                var amount = parseFloat(unformatCurrency($(this).find("[name='detailRowCurrencySubtotal[]']").val()));
                var currencykey = $(this).find("[name='selCurrencyDetail[]']").val();
                if(currencykey != headerCurrency){ 
                    hasOtherCurrency = true;
                    subtotalOtherCurrency += amount;
                }else{
                    subtotalCurrency += amount;
                }
                
                var total = parseFloat(unformatCurrency($(this).find("[name='detailRowSubtotal[]']").val()));
                subtotal += total;
                 
            })  
              
            panelObj.find("[name='detailCurrencyTotal[]']").val(subtotalCurrency).blur();
            panelObj.find("[name='detailOtherCurrencyTotal[]']").val(subtotalOtherCurrency).blur(); 
               
            panelObj.find("[name='detailTotal[]']").val(subtotal).blur(); 
              
            if(hasOtherCurrency)
                panelObj.find(".idr-only").show();
            else
                panelObj.find(".idr-only").hide();
              
            thisObj.calculateTotal();
        }
          
          
        this.calculateTotal = function calculateTotal(){
            var amount = 0; 
            var total = 0; 
             
            tabObj.find("[name='detailTotal[]']").each(function(){ amount += parseFloat(unformatCurrency($(this).val())) || 0;  })    
 
            // HITUNG TOTAL
            var subtotal = amount;
            
            tabObj.find("[name='totalSelling']").val(subtotal).blur();
            var totalBuying = tabObj.find("[name='totalBuying']").val();
            var totalCommission = tabObj.find("[name='totalCommission']").val();
            total = subtotal - totalBuying - totalCommission;
            tabObj.find("[name='grandtotal']").val(total).blur();
        }
                
        this.updateAvailableCurency = function updateAvailableCurency(panelObj){
            
            if(!panelObj)
                panelObj = tabObj.find(".customer-row");
            
            panelObj.each(function(){     
                var detailCurrency =  $(this).find("[name='selCurrencyDetail[]']");  
                var selCurrencyObj = $(this).find("[name='selSellingCurrency[]']");  
  
                if(selCurrencyObj.val() == varConstant.CURRENCY.idr){   
                    // update semua detail currency harus idr, disabled selain idr
                    detailCurrency.find("option:not(:selected)").attr('disabled', true);

                }else{   
                    // buka semua select box yg disabled
                    detailCurrency.find("option").attr('disabled', false);
                }
            })
                   
        }
        
        this.onChangeCurrency = function onChangeCurrency(selCurrencyObj){
              
            var panelObj =  thisObj.getGroupObj(selCurrencyObj);
            var currencyRateObj =  panelObj.find("[name='sellingCurrencyRate[]']");
            var detailCurrency =  panelObj.find("[name='selCurrencyDetail[]']"); 
              
            detailCurrency.val(selCurrencyObj.val());
            
            var changeFlag = false;
            if(selCurrencyObj.val() == varConstant.CURRENCY.idr){ 
                changeFlag = true;
                currencyRateObj.val(1);
            }
             
            currencyRateObj.prop("readonly", changeFlag);
            panelObj.find(".subheader-active-currency").html(selCurrencyObj.find("option:selected").text());
            
            // dipisah agar dapat dipanggil ketika onload tanpa pengaruh ke nilai rate dll
            thisObj.updateAvailableCurency(panelObj); 
            
            currencyRateObj.change().blur();
             
            $.ajax({
                    type: "GET",
                    url:  'ajax-currency-rate.php', 
                    data: "action=getLastRate&currencykey=" + selCurrencyObj.val(),  
                    beforeSend:function (xhr){ 
                        currencyRateObj.val(1);
                    },
                success: function (data) {  
                        if (!data) return;
                        var data = JSON.parse(data);

                        if(data.length == 0){ 
                            return;
                        }   
                        if(data){
                            // var data = JSON.parse(data);   
                            currencyRateObj.val(data[0]['rate']).blur();
                            thisObj.onChangeCurrencyRate(currencyRateObj);
                        }
                    }  
                });
        }
            
        
        this.onChangeCurrencyRate = function onChangeCurrencyRate(currencyRateObj){
              
            var panelObj =  thisObj.getGroupObj(currencyRateObj);  
            
            panelObj.find("[name='detailRowSubtotal[]']").each(function() {    
                thisObj.calculateDetailRowSubtotal( thisObj.getRowObj($(this)) ); // gk bisa karena di row template gk ad transction detail row
            }) 
              
        }
           
        this.updateReimburse =  function updateReimburse(rowObj){ 
			var isReimburse = rowObj.find("[name='chkIsReimburse[]']").val() ;   
	  
            if (isReimburse == 1) {
                rowObj.find("[name='taxDetail[]']").val(0).attr("readonly", true).change();  
                rowObj.find("[name='taxDetail[]']").find("option:not(:selected)").attr('disabled', true);  
                
                rowObj.find("[name='dummychkIncludeTaxDetail[]']").prop("checked", false).attr("readonly", true);  
            } else {
                rowObj.find("[name='taxDetail[]']").find("option:not(:selected)").removeAttr('disabled');  
                rowObj.find("[name='taxDetail[]']").attr("readonly", false);  
                rowObj.find("[name='dummychkIncludeTaxDetail[]']").attr("readonly", false);  
            }
        }

        this.calculateDetailRowSubtotal =  function calculateDetailRowSubtotal(rowObj){
            
            var rate = parseFloat(unformatCurrency(thisObj.getGroupObj(rowObj).find("[name='sellingCurrencyRate[]']").val())) || 0;   
            
            var currency = rowObj.find("[name='currencyDetail[]']").val();
            var qty = parseFloat(unformatCurrency(rowObj.find("[name='qty[]']").val())) || 0;
            var priceInUnit = parseFloat(unformatCurrency(rowObj.find("[name='priceInUnit[]']").val())) || 0;   
            var subtotal = qty * priceInUnit;  
            
            var selCurrencyDetailObj = rowObj.find("[name='selCurrencyDetail[]']"); 

            var taxValue = 0;
            var beforeTaxDetail = subtotal;
			
			if(updateTaxAtJobOrder == 1){
                
                var taxValueDetail = parseFloat(unformatCurrency(rowObj.find("[name='taxDetail[]']").val())) || 0;
            	var isInc = rowObj.find("[name='chkIncludeTaxDetail[]']").val() || 0;
                
				if(taxValueDetail > 0){
					if (isInc == 0) {
						taxValue = subtotal * taxValueDetail / 100;
                        subtotal+=taxValue;
					}else{
//						taxValue = (taxValueDetail/(100 + taxValueDetail)) * subtotal;  
//						beforeTaxDetail -= taxValue;
					} 	
				}
            
            } 

            rowObj.find("[name='detailRowCurrencySubtotal[]']").val(subtotal).blur();
             
            if(selCurrencyDetailObj.val() != varConstant.CURRENCY.idr){ 
               subtotal = subtotal * rate; 
            }
            

            rowObj.find("[name='detailRowSubtotal[]']").val(subtotal).change().blur();  
            rowObj.find(".active-currency").html(selCurrencyDetailObj.find("option:selected").text());
             
            //changeNumberDecimal(rowObj.find("[name='priceInUnit[]']"),changeFlag);  
        }
        
 	
		this.importData = function importData(obj){ 
          
                thisObj.activeAjaxConnections = 0;
                
                thisObj.updateJobType();
            
                var panelObj =  thisObj.getGroupObj(obj);

                var objVolume = tabObj.find('.transaction-detail-row');                
                
                var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0;  
                var transportationkey  = tabObj.find("[name=selAirSea]").val() || 0; 
                var containertypekey  = tabObj.find("[name=selContainerType]").val() || 0; 
			
                var polkey = tabObj.find("[name=hidPOLKey]").val(); 
                var podkey = tabObj.find("[name=hidPODKey]").val(); 
			 
                var containerkey  = objVolume.find("[name='selContainerDetailVolumeKey[]']"); 
			    var hidContainerKey = tabObj.find("[name=selContainerKey]").val();
           
				var arrContainerKey = [];
                containerkey.each(function() { 
                    arrContainerKey.push($(this).val());
                }) 
     
                containerkey = ( LCLTYPE.includes(parseInt(containertypekey))) ? hidContainerKey : arrContainerKey;
 

                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-quotation-order.php',
                    beforeSend:function (xhr){ 
                        // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                        clearAllRows(panelObj.find(".detail-item"));
                        thisObj.activeAjaxConnections++; 
                    },
                    data:  "action=getQuotationPriceAndCost&pkey=" + pkey + '&polkey=' + polkey + '&podkey=' + podkey + '&transportationkey=' + transportationkey + '&containertypekey=' + containertypekey + '&containerkey=' + containerkey,
                    success: function(data){ 
                        if(!data) return;
                        
                        var data = JSON.parse(data);
                                
                        if(data.length == 0){  
                            addNewTemplateRow("item-row-template",null,panelObj,thisObj.rebindEl);  
                            return;
                        }                  
						
						//cek currency lain selain IDR
						var currOther = varConstant.CURRENCY.idr;
						for(var i=0;i<data.length;i++){   
							if (data[i].currencykey != varConstant.CURRENCY.idr) currOther = data[i].currencykey;
						}
						
						
						panelObj.find("[name='selSellingCurrency[]']").first().val(currOther).change();
						
						for(var i=0;i<data.length;i++){   

								var arrPostValue = [];  

								arrPostValue.push({"selector":"hidContainerDetailKey", "value":data[i].containerkey});
								arrPostValue.push({"selector":"containerDetailName", "value":data[i].containername}); 
								arrPostValue.push({"selector":"qty", "value":1}); 
								arrPostValue.push({"selector":"alias", "value":data[i].alias}); 
								arrPostValue.push({"selector":"hidServiceKey", "value":data[i].servicekey}); 
								arrPostValue.push({"selector":"serviceName", "value":data[i].servicename}); 
								arrPostValue.push({"selector":"selCurrencyDetail", "value":data[i].currencykey}); 
								arrPostValue.push({"selector":"unitNameDetail", "value":data[i].unitname}); 
								arrPostValue.push({"selector":"hidUnitDetailKey", "value":data[i].unitkey});  
								arrPostValue.push({"selector":"priceInUnit", "value":data[i].price});  
								arrPostValue.push({"selector":"remarks", "value":data[i].remarks}); 
								arrPostValue.push({"selector":"taxDetail", "value":data[i].taxpercentage}); 

								addNewTemplateRow("item-row-template",JSON.stringify(arrPostValue),panelObj,thisObj.rebindEl);  

//                                newrow.find("[name='chkIncludeTaxDetail[]']").val(data[i].ispriceincludetax).change(); 
//                                newrow.find("[name='chkIsReimburse[]']").val(data[i].isperreciept).change(); 

						}

                        // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                        tabObj.find(".inputnumber").change().blur();
                        tabObj.find(".inputdecimal").change().blur();

                        decreaseActiveAjaxConnections(thisObj); 
                    } ,
                    error: function(xhr, errDesc, exception) {
                        decreaseActiveAjaxConnections(thisObj); 
                    }
                }); 
        }

        this.updatePersonInCharge = function updatePersonInCharge() {
			 
			
			// kalo form standart gk ad personincharge, retur nsja langsung
			
            var selSupplierObj = tabObj.find("[name=selSupplier]");  
			if(selSupplierObj.length == 0) return ; 
			 
            var supplierkey = tabObj.find("[name=selSupplier]").val();
   
            var ajaxData = "action=getPersonInCharge&pkey=" + tabObj.find("[name=hidCustomerKey]").val(); 
            $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async : false,
                beforeSend:function (xhr){
                    selSupplierObj .each(function(){  $('option', $(this)).remove();  }) 
                },
                data: ajaxData,
                success: function (data) {
                    // update combobox supplier
                    if (!data) return;
                    data = JSON.parse(data);
                    var arrData = [];
                    for(var i=0; i < data.length; i++) {
                        var obj = {};
                        obj['pkey'] = data[i]['pkey'];
                        obj['value'] = decodeHTMLEntities(data[i]['value']);
                        arrData.push(obj);
                    }
                    var selectOpt = arrData;
                    reInsertSelectBox(selSupplierObj, selectOpt, { "key": "pkey", "label": "value" });
                    if (supplierkey !== null) {
                        selSupplierObj.val(supplierkey).change();
                    }
                    
                }
            });  
        }

        this.updateFlag = function updateFlag() {
			
			var hidVesselKey = tabObj.find("[name=hidVesselKey]");  
			if(hidVesselKey.length == 0) return ; 
			
            var vesselkey = hidVesselKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + vesselkey; 
            $.ajax({
                type: "GET",
                url:  'ajax-vessel.php',
                async : false,
                beforeSend:function (xhr){
                    
                },
                data: ajaxData,
                success: function (data) {
                    if (!data) return;
                    data = JSON.parse(data);

                    if(vesselkey == null || data.length <= 0) {
                        tabObj.find("[name=flag]").val("");
                    } else {
                        tabObj.find("[name=flag]").val(data[0]['flag']);
                    }
                    
                }
            });  
        }

        this.calculateMeas = function calculateMeas(rowObj) {
            var meas = 0;

            var lenght = parseFloat(unformatCurrency(rowObj.find("[name='detailLength[]']").val()));
            var width = parseFloat(unformatCurrency(rowObj.find("[name='detailWidth[]']").val()));
            var height = parseFloat(unformatCurrency(rowObj.find("[name='detailHeight[]']").val()));

            var selAirSea = tabObj.find("[name='selAirSea']").val();

            var air = varConstant.EMKL.shipping.air;
            var sea = varConstant.EMKL.shipping.sea;

            var calculateDimension = lenght * width * height;

            if (selAirSea == sea) {
                meas = calculateDimension / 1000000;
            } else {
                meas = calculateDimension / 6000;
            }

            rowObj.find("[name='detailMeas[]']").val(meas).blur()

        }
//        this.rebindContainer = function rebindContainer(){
//                    
//            
//            var selObj = tabObj.find("[name=selAirSea]").val();
//
//            var criteria = '';
//
//            
//            if (selObj == varConstant.EMKL.shipping.sea){
//                
//                criteria = "&issea=1";
//
//            }else if(selObj == varConstant.EMKL.shipping.air){ 
//                criteria = "&isair=1";
//
//            }else if(selObj == varConstant.EMKL.shipping.land){
//                criteria = "&island=1";
//
//            }
//            
//            // bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailAutoComplete,'ajax-container.php?action=searchData'+criteria);  
//  		    // bindAutoCompleteForTransactionDetail('unitNameDetail[]',objAndValueUnitDetailAutoComplete,'ajax-item-unit.php?action=searchData'+criteria); 
//            bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailAutoComplete,'ajax-container.php?action=searchData');  
//  		    bindAutoCompleteForTransactionDetail('unitNameDetail[]',objAndValueUnitDetailAutoComplete,'ajax-item-unit.php?action=searchData'); 
//  
//        }

        this.updateNotifyParty = function updateNotifyParty()
        {
            var hidNotifyPartyKey = tabObj.find("[name=hidNotifyPartyKey]");  
			if( hidNotifyPartyKey.length == 0) return ; 
			
            var consigneekey = hidNotifyPartyKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + consigneekey; 
            $.ajax({
                type: "GET",
                url:  'ajax-consignee.php',
                async : false,
                beforeSend:function (xhr){
                    
                },
                data: ajaxData,
                success: function (data) {
                    if (!data) return;
                    data = JSON.parse(data);

                    if(consigneekey == null || data.length <= 0) {
                        tabObj.find("[name=notifyPartyAddress]").val("");
                    } else {
                        tabObj.find("[name=notifyPartyAddress]").val(data[0]['address']);
                    }
                    
                }
            });  
        }

        this.updateOverwrite  = function updateOverwrite(obj){ 
			var chkVal = obj.val();
			var attrName = obj.closest("div").attr("rel");
		   
			if(chkVal == 1){
				tabObj.find(".overwrite-" + attrName).show();      
				tabObj.find(".non-overwrite-" + attrName).hide();      	
			}else{
				tabObj.find(".overwrite-" + attrName).hide();      
				tabObj.find(".non-overwrite-" + attrName).show();       
			}
        }   
        
        this.updateQtyDetail = function updateQtyDetaul()
        {
            var qty = tabObj.find("[name=qtyHeader]");
            var selUnit = tabObj.find("[name=selUnitKey]");
            var weigthQty = tabObj.find("[name=weightQty]");
            var measurement = tabObj.find("[name=measurement]");

            if(typeof qty.get(0)  !== 'undefined') {
                var qtyValue = qty.val();
                tabObj.find("[name='detailQty[]']").val(qtyValue);
            }

            if(typeof selUnit.get(0)  !== 'undefined') {
                var selUnitValue = selUnit.val();
                tabObj.find("[name='detailSelUnit[]']").val(selUnitValue).change();
            }

            if(typeof weigthQty.get(0)  !== 'undefined') {
                var weigthQtyValue = weigthQty.val();
                tabObj.find("[name='detailWeight[]']").val(weigthQtyValue);
            }

            if(typeof measurement.get(0)  !== 'undefined') {
                var measurementValue = measurement.val();
                tabObj.find("[name='detailMeasurement[]']").val(measurementValue);
            }
        } 

    this.formReset = function formReset() {

            tabObj.find("[name=selWarehouseKey]").val("").prop('selectedIndex', 0).trigger('change');
            tabObj.find("[name=shipperName]").val("");
            tabObj.find("[name=hidCustomerKey]").val("");
            tabObj.find("[name=salesName]").val("");
            tabObj.find("[name=hidSalesKey]").val("");
            tabObj.find("[name=selShipmentTerm], [name=selShipmentTerm2], [name=selFreightTerm], [name=selFreightTerm2]").prop('selectedIndex', 0).trigger('change');
            tabObj.find("[name=selShipmentType]").val("").prop('selectedIndex', 0).trigger('change');
            tabObj.find("[name=serviceContract]").val("");
            tabObj.find("[name=consigneeName]").val("");
                        
            tabObj.find("[name=selAirSea]").val('').prop('selectedIndex', 0).trigger('change');
            tabObj.find("[name=selContainerType]").val("").prop('selectedIndex', 0).trigger('change');
            tabObj.find("[name=hidCargoType]").val("").prop('selectedIndex', 0).trigger('change');
            tabObj.find("[name=weight], [name=volume], [name=qtyHeader], [name=weightQty], [name=measurement]").val("");
            tabObj.find("[name=hidContainerKey]").val("").prop('selectedIndex', 0).trigger('change');

            tabObj.find("[name=aju]").val("");
            tabObj.find("[name=hidAgentKey]").val("");
            tabObj.find("[name=agentName]").val("");

            tabObj.find("[name=chkIsOverwriteNotifyParty]").prop("checked", false).change();
            tabObj.find("[name=notifyPartyName1], [name=notifyPartyAddress1], [name=notifyPartyName], [name=notifyPartyAddress]").val("");
            tabObj.find("[name=alsoNotifyParty]").val("");

            tabObj.find("[name=mblNumber]").val("");

            tabObj.find("[name=hidFeederKey], [name=feederName], [name=feederNumber]").val("");
            tabObj.find("[name=hidVesselKey], [name=vesselName], [name=vesselNumber]").val("");
            tabObj.find("[name=hidConnectingVesselKey], [name=connectingVesselName], [name=connectingVesselNumber]").val("");
            tabObj.find("[name=hidConnectingVessel2Key], [name=connectingVessel2Name], [name=connectingVessel2Number]").val("");

            tabObj.find("[name=hidFinalDestinationKey], [name=finalDestinationName]").val("");
            tabObj.find("[name=etdPol], [name=etaPod]").val("");
            tabObj.find("[name=hidPOLKey], [name=polName], [name=hidPODKey], [name=podName]").val("");
            tabObj.find("[name=hidPlaceOfDeliveryKey], [name=placeOfDeliveryName], [name=hidPlaceOfReceiptKey], [name=placeOfReceiptName]").val("");
            tabObj.find("[name=hidCarrierKey], [name=carrierName]").val("");


            clearAllRows(tabObj.find(".mnv-container-volume"));
            clearAllRows(tabObj.find(".mnv-commodity"));

            addNewTemplateRow("volume-row-template",null,null,thisObj.rebindEl);
            addNewTemplateRow("commodity-row-template",null,null,thisObj.rebindEl);

        }



        this.importTemplate = function importTemplate()
        {

            var pkey = tabObj.find("[name=hidTemplateKey]").val();

            if(!pkey) {
                thisObj.formReset();
                return;
            }

            $.ajax({
                type: "GET",
                async: false,
                url:  'ajax-template-emkl-job-order.php', 
                data: "action=getTemplateInformation&pkey=" + pkey,  
            }).done(function( data ) { 
					
                    data = parseJSON(data);
                    
                   if(data.length == 0)return;

                   data = data[0];

                    tabObj.find("[name=selWarehouseKey]").val(data.warehousekey);  
                    tabObj.find("[name=selWarehouseKey]").change();  
                    tabObj.find("[name=shipperName]").val(data.customername);
                    tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
  
                    tabObj.find("[name=salesName]").val(data.salesname);
                    tabObj.find("[name=hidSalesKey]").val(data.saleskey);
                    tabObj.find("[name=selShipmentTerm]").val(data.shipmenttermkey);
                    tabObj.find("[name=selShipmentTerm2]").val(data.shipmentterm2key);
                    tabObj.find("[name=selFreightTerm]").val(data.freighttermkey);
                    tabObj.find("[name=selFreightTerm2]").val(data.freightterm2key);
                
                    tabObj.find("[name=selShipmentType]").val(data.shipmenttypekey);

                    tabObj.find("[name=selShipmentTerm]").change();
                    tabObj.find("[name=selShipmentTerm2]").change();
                    tabObj.find("[name=selFreightTerm]").change();
                    tabObj.find("[name=selFreightTerm2]").change();
                    tabObj.find("[name=selShipmentType]").change();

                    tabObj.find("[name=serviceContract]").val(data.servicecontract);

                    tabObj.find("[name=consigneeName]").val(data.consigneename);

                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey);
                    tabObj.find("[name=hidCargoType]").val(data.containertypekey);
                    
                    if (data.loadcontainertypekey == varConstant.EMKL.container.lcl) {
                            tabObj.find("[name=weight]").val(data.weight).blur();
                            tabObj.find("[name=volume]").val(data.volume).blur();
                            tabObj.find("[name=hidContainerKey]").val(data.itemkey).change();
                    } else if (data.loadcontainertypekey == varConstant.EMKL.container.lclnc ||
                        data.loadcontainertypekey == varConstant.EMKL.container.freightcustomlcl ||
                        data.loadcontainertypekey == varConstant.EMKL.container.customlcl
                    ) {

                        tabObj.find("[name=weight]").val(data.weight).blur();
                        tabObj.find("[name=volume]").val(data.volume).blur();
                        tabObj.find("[name=hidContainerKey]").val(data.itemkey).change();

                    } else if (data.loadcontainertypekey == varConstant.EMKL.container.fcl ||
                        data.loadcontainertypekey == varConstant.EMKL.container.freightcustomfcl ||
                        data.loadcontainertypekey == varConstant.EMKL.container.trucking ||
                        data.loadcontainertypekey == varConstant.EMKL.container.customfcl) {

                        thisObj.updateVolumeInformation();

                    } else {

                    }


                    tabObj.find("[name=selAirSea]").change();
                    tabObj.find("[name=selContainerType]").change();
                    tabObj.find("[name=hidCargoType]").change();

                    tabObj.find("[name=qtyHeader]").val(data.qty).blur();
                    tabObj.find("[name=selUnitKey]").val(data.unitkey);
                    tabObj.find("[name=weightQty]").val(data.weightqty).blur();
                    tabObj.find("[name=measurement]").val(data.measurement).blur();

                    tabObj.find("[name=aju]").val(data.aju);

                    tabObj.find("[name=hidAgentKey]").val(data.agentkey);
                    tabObj.find("[name=agentName]").val(data.agentname);

                    tabObj.find("[name=chkIsOverwriteNotifyParty]").val(data.isoverwritenotifyparty).change();

                    if(data.isoverwritenotifyparty == 1) {
                        tabObj.find("[name=notifyPartyName1]").val(data.notifypartyname);
                        tabObj.find("[name=notifyPartyAddress1]").val(data.notifypartyaddress);
                    } else {
                        tabObj.find("[name=hidNotifyPartyKey]").val(data.notifypartykey);
                        tabObj.find("[name=notifyPartyName]").val(data.notifypartyname);
                        tabObj.find("[name=notifyPartyAddress]").val(data.notifypartyaddress);
                    }
                    
                    tabObj.find("[name=alsoNotifyParty]").val(data.alsonotifyparty);

                    tabObj.find("[name=mblNumber]").val(data.mblnumber);

                    tabObj.find("[name=hidFeederKey]").val(data.feederkey);
                    tabObj.find("[name=feederName]").val(data.feedervesselname);
                    tabObj.find("[name=feederNumber]").val(data.feedernumber);

                    tabObj.find("[name=hidVesselKey]").val(data.vesselkey);
                    tabObj.find("[name=vesselName]").val(data.vesselname);
                    tabObj.find("[name=vesselNumber]").val(data.vesselnumber);

                    tabObj.find("[name=hidConnectingVesselKey]").val(data.connectingvesselkey);
                    tabObj.find("[name=connectingVesselName]").val(data.connectingvesselname);
                    tabObj.find("[name=connectingVesselNumber]").val(data.connectingvesselnumber);

                    tabObj.find("[name=hidConnectingVessel2Key]").val(data.connectingvessel2key);
                    tabObj.find("[name=connectingVessel2Name]").val(data.connectingvessel2name);
                    tabObj.find("[name=connectingVessel2Number]").val(data.connectingvessel2number);

                    tabObj.find("[name=hidFinalDestinationKey]").val(data.finaldestinationkey);
                    tabObj.find("[name=finalDestinationName]").val(data.finaldestinationname);

                    tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_)); 

                    tabObj.find("[name=hidPOLKey]").val(data.polkey);
                    tabObj.find("[name=polName]").val(data.polname);
                    tabObj.find("[name=hidPODKey]").val(data.podkey);
                    tabObj.find("[name=podName]").val(data.podname);
                    
                    tabObj.find("[name=hidPlaceOfDeliveryKey]").val(data.placeofdeliverykey);
                    tabObj.find("[name=placeOfDeliveryName]").val(data.placeofdeliveryname);
                    tabObj.find("[name=hidPlaceOfReceiptKey]").val(data.placeofreceiptkey);
                    tabObj.find("[name=placeOfReceiptName]").val(data.placeofreceiptname);

                    tabObj.find("[name=hidCarrierKey]").val(data.carrierkey);
                    tabObj.find("[name=carrierName]").val(data.carriername);

                    //get commodity detail
                    thisObj.updateCommodityInformation(); 

            });  
        }

        this.updateVolumeInformation = function updateVolumeInformation(){   
                
                var pkey = tabObj.find("[name=hidTemplateKey]").val();
            
                $.ajax({
                    type: "GET",
                    url:  'ajax-template-emkl-job-order.php',
                    beforeSend:function (xhr){ 
                        clearAllRows(tabObj.find(".mnv-container-volume"));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getContainerVolume&pkey=" + pkey ,  
                    success: function(data){ 
                        if(!data) return;
                        var data = JSON.parse(data);
                                  
                        if(data.length == 0){ 
                            addNewTemplateRow("volume-row-template",'','',thisObj.rebindEl);  
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

                        var obj = tabObj.find("[name='selContainerDetailVolumeKey[]']");
                        if (obj.length > 0) {
                            thisObj.updateAllContainerType(obj);
                        }
                        decreaseActiveAjaxConnections(thisObj); 
                    } ,
                    error: function(xhr, errDesc, exception) {
                        decreaseActiveAjaxConnections(thisObj); 
                    }
            }); 
        }

        this.updateCommodityInformation = function updateCommodityInformation() 
        {
            var pkey = tabObj.find("[name=hidTemplateKey]").val();

            $.ajax({
                type: "GET",
                url:  'ajax-template-emkl-job-order.php',
                beforeSend:function (xhr){ 
                    clearAllRows(tabObj.find(".mnv-commodity"));
                    thisObj.activeAjaxConnections++; 
                },
                data: "action=getCommodityDetail&pkey=" + pkey ,  
                success: function(data){ 
                    if(!data) return;
                    var data = JSON.parse(data);
                              
                    if(data.length == 0){ 
                        addNewTemplateRow("commodity-row-template",'','',thisObj.rebindEl);  
                        return;
                    }                             
                    
                    var i;
                    for(i=0;i<data.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"hidCommodityKey", "value":data[i].commoditykey});
                        arrPostValue.push({"selector":"commodityName", "value":data[i].commodityname}); 
                        addNewTemplateRow("commodity-row-template",JSON.stringify(arrPostValue));  
                    }

                    decreaseActiveAjaxConnections(thisObj); 
                } ,
                error: function(xhr, errDesc, exception) {
                    decreaseActiveAjaxConnections(thisObj); 
                }
            }); 

        }

        
        this.rebindEl = function rebindEl(){   
            
            bindAutoCompleteForTransactionDetail('customerDetailName[]',objAndValueCustomerAutoComplete,'ajax-customer.php?action=searchData');     
            bindAutoCompleteForTransactionDetail('detailPODName[]',objAndValuePODAutoComplete,'ajax-port.php?action=searchData');     
            bindAutoCompleteForTransactionDetail('commissionRecipientDetailName[]',objAndValueCommissionRecipientAutoComplete,'ajax-supplier.php?action=searchData&suppliertype=1', thisObj.updateCommissionDetail);     
            bindAutoCompleteForTransactionDetail('destinationDetailName[]',objAndValueDestinationAutoComplete,'ajax-port.php?action=searchData');    
            bindAutoCompleteForTransactionDetail('warehouseDetailName[]',objAndValueWarehouseAutoComplete,'ajax-depot.php?action=searchData');    
            bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailAutoComplete,'ajax-container.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForDetailServiceAutoComplete,'ajax-item.php?action=searchData&itemtype=3');    
            bindAutoCompleteForTransactionDetail('salesDetailName[]',objAndValueSalesDetailAutoComplete,'ajax-employee.php?action=searchData&issales=1');
  		    bindAutoCompleteForTransactionDetail('chargeToName[]',objAndValueChargeToAutoComplete,'ajax-customer.php?action=searchData'); 
            bindAutoCompleteForTransactionDetail('commodityName[]', objAndValueForDetailCommodityAutoComplete, 'ajax-commodity.php?action=searchData');  

            var btnDeleteCustomer = tabObj.find("[name=btnDeleteCustomerRows]");
            var btnAddHBL = tabObj.find(".add-hbl");
            var btnImport = tabObj.find("[name='btnImport[]']");
            bindEl(btnDeleteCustomer,'mouseover',function(){ $(this).closest(".row-panel").addClass("border-red-cardinal") });
            bindEl(btnDeleteCustomer,'mouseout',function(){ $(this).closest(".row-panel").removeClass("border-red-cardinal") });
     	    bindEl(btnAddHBL,'click',function(){ thisObj.updateNumberMasterHBL($(this)) });
                    
            bindEl(tabObj.find("[name='selSellingCurrency[]']"),'change',function(){ thisObj.onChangeCurrency($(this)) }); 
            bindEl(tabObj.find("[name='sellingCurrencyRate[]']"),'change',function(){ thisObj.onChangeCurrencyRate($(this)) }); 
            bindEl(tabObj.find("[name='detailRowSubtotal[]']"),'change',function(){ thisObj.calculateDetailGroupSubtotal(thisObj.getGroupObj($(this))) }); 
            bindEl(tabObj.find("[name='detailLength[]'],[name='detailWidth[]'], [name='detailHeight[]']"),'change',function(){ thisObj.calculateMeas(thisObj.getGroupObj($(this))) }); 
            bindEl(tabObj.find("[name='detailTotal[]']"),'change',function(){ thisObj.calculateTotal() }); 
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='selCurrencyDetail[]'],[name='taxDetail[]'],[name='chkIncludeTaxDetail[]']"),'change',function(){ thisObj.calculateDetailRowSubtotal(thisObj.getRowObj($(this))) });
            bindEl(tabObj.find("[name='chkIsReimburse[]']"),'change',function(){ thisObj.updateReimburse(thisObj.getRowObj($(this))) }); 
			bindEl(tabObj.find("[name='chkIsManual[]']"),'change',function(){ thisObj.updateHBLManually($(this)) }); 
            //bindEl(tabObj.find("[name='selCommissionCurrency[]']"),'change',function(){ updateDecimal($(this), 'commission[]') }); 
  
            bindEl(btnImport, 'click', function () { thisObj.importData($(this)) });
            
			// container kita blm ad pemisah sea / air
           // thisObj.rebindContainer();
        }
        
        this.bindAutoUpdateContainer = function bindAutoUpdateContainer() { 
            bindEl(tabObj.find("[name='selContainerDetailVolumeKey[]']").first(),'change',function(){    thisObj.updateAllContainerType($(this));  });  
            bindEl(tabObj.find("[name=hidContainerKey]"),'change', function () { thisObj.updateAllContainerType($(this)); });  
        } 
        
        this.updateAllContainerType = function updateAllContainerType(obj){
            if(!thisObj.autoUpdateEMKLJobOrderContainer) return;
            
            tabObj.find("[name='selContainerTypeDetail[]']").val(obj.first().val());
            tabObj.find("[name='hidContainerDetailKey[]']").val(obj.first().val());
            tabObj.find("[name='containerDetailName[]']").val(obj.first().find("option:selected").text());
            
        }
        
        this.loadOnReady = function loadOnReady(){  

            tabObj.find("[name=chkIsOverwriteNotifyParty]").change(function(){thisObj.updateOverwrite($(this))}); 
            tabObj.find("[name=chkIsOverwriteNotifyParty]" ).change();
         
           customerRows = tabObj.find(".customer-row");
           customerRows.each(function() {  
                //alert('tes');
                  var row = $(this).find(".detail-commission .transaction-detail-row");  
                  var itemrow = $(this).find(".detail-item .transaction-detail-row");  
                  if(row.length == 0)  addNewTemplateRow('commission-row-template',null,$(this),thisObj.rebindEl);  
                  if(itemrow.length == 0)  addNewTemplateRow('item-row-template',null,$(this),thisObj.rebindEl);  
            }) 

			// perlu dicek lg, bisa sama tdk dengan GPI
//    		tabObj.find("[name=selAirSea]").change(function() { thisObj.updateAirOrSea();  thisObj.updateContainerType($(this).val());  });
        
            tabObj.find("[name=selAirSea]").change(function() { thisObj.updateAirOrSea(); });
            tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 
            tabObj.find("[name=selVolumeType]").change(function() { thisObj.updateVolumeType(); });
            tabObj.find("[name=chkIsMaster]" ).change(function(){thisObj.updateMasterDetailField($(this))});
            tabObj.find("[name=selAirSea]" ).change(function(){thisObj.calculateMeas(thisObj.getGroupObj($(this)))});
            tabObj.find("[name=qtyHeader], [name=selUnitKey], [name=weightQty], [name=measurement]").change(function(){thisObj.updateQtyDetail()});
     
          
            tabObj.find("[name=hidCustomerKey]").change(function(){thisObj.updatePersonInCharge()});
            tabObj.find("[name=hidVesselKey]").change(function(){thisObj.updateFlag()});
            thisObj.updateAirOrSea();
            tabObj.find( " .section-panel .title" ).click(function() {  
                $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
            });
            // utk memastikan jika pilih IDR diawal, option lain disabled.   
            //if(tabObj.find("[name=hidId]").val() == 0)
            
            if (!data['volumeDetail'] || data['volumeDetail'].length == 0)
                addNewTemplateRow("volume-row-template",null,null,thisObj.rebindEl);
            
            if (!data['containerNumberDetail'] || data['containerNumberDetail'].length == 0)
                addNewTemplateRow("container-row-template",null,null,thisObj.rebindEl);

            if (!data['commodityDetail'] || data['commodityDetail'].length == 0)
                addNewTemplateRow("commodity-row-template",null,null,thisObj.rebindEl);
            
            thisObj.updateAvailableCurency();
            thisObj.updateFieldfForLCLDetail();
            thisObj.updatePersonInCharge();
            thisObj.updateFlag();
            
            // set readonly di JS, karena tergantung attributenya, attribute nanti tergantung load setting
            tabObj.find("[attr-header=\"true\"]").attr('readonly',true).addClass("force-readonly"); // utk autocomplete, harus pake attr readonly
            //tabObj.find("[name=selWarehouseKey]").find("option:not(:selected)").attr('disabled', true);
         
            tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
    
            tabObj.find("[name=btnAddRows]").on('click', function() { 
                thisObj.updateQtyDetail();
                //addNewTemplateRow("monthly-row-template",null,null,thisObj.rebindEl); 
            });
            
            // gk bisa dari removeHandler karena kena ke semua remove-button , jd harus handle manual
            tabObj.find(".mnv-container-volume .remove-button").click(function() {
                removeDetailRows(this);  
                thisObj.bindAutoUpdateContainer();
                tabObj.find("[name='selContainerDetailVolumeKey[]']").first().change(); 
            });   

            
           thisObj.bindAutoUpdateContainer();
        
           thisObj.rebindEl();
        }
}
