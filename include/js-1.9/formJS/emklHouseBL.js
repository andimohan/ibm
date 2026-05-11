function EMKLHouseBL(tabID,data){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID;   
          
        var id = tabObj.find("[name=hidId]").val();
 
     this.updateFromJobOrder = function updateFromJobOrder(){   
                var pkey = tabObj.find("[name=hidJobOrderKey]").val();
                  
                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-job-order.php',
                    async: false,
                    data: "action=getJobOrderByDetailId&pkey=" + pkey ,  
                }).done(function( data ) { 

                    if(!data) return;
                    
                    data = JSON.parse(data) ; 

                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];
                    
                    // kalo field job order nya gk readonly dan kosong (utk CIF)
                    if (tabObj.find("[name=code]").val() == ''){
                        tabObj.find("[name=code]").val(data.code);
                    }

                    tabObj.find("[name=hidShipperKey]").val(data.customerkey); 
                    tabObj.find("[name=shipperName]").val(decodeHTMLEntities(data.customername));
                    tabObj.find("[name=shipperAddress]").val(data.customeraddress);
				    tabObj.find("[name=shipperName1]").val(decodeHTMLEntities(data.customername));
                    tabObj.find("[name=shipperAddress1]").val(data.customeraddress);
					
					
					// harus dicheck karen by default ambilny dr JO
				    tabObj.find("[name=dummychkIsOverwriteConsignee]").prop("checked", true); 
				    tabObj.find("[name=chkIsOverwriteConsignee]").val(1);
                    tabObj.find("[name=chkIsOverwriteConsignee]" ).change();
				    tabObj.find("[name=consigneeName]").val(decodeHTMLEntities(data.consigneename));
				    tabObj.find("[name=consigneeName1]").val(data.consigneename);

                    // tabObj.find("[name=dummychkIsOverwriteAgent]").prop("checked", true); 
				    /* tabObj.find("[name=chkIsOverwriteAgent]").val(1); */
                    tabObj.find("[name=hidAgentKey]").val(data.agentkey); 
				    tabObj.find("[name=agentName]").val(decodeHTMLEntities(data.agentname));
                    tabObj.find("[name=agentAddress]").val(data.agentaddress);
                    tabObj.find("[name=agentName1]").val(data.agentname);
                    tabObj.find("[name=agentAddress1]").val(data.agentaddress);
                    
                    if (data.isoverwritenotifyparty == 1) {
                        tabObj.find("[name=dummychkIsOverwriteCarrier]").prop("checked", true);
                        tabObj.find("[name=chkIsOverwriteCarrier]").val(1);
                        tabObj.find("[name=chkIsOverwriteCarrier]").change();
                    } else {
                        tabObj.find("[name=dummychkIsOverwriteCarrier]").prop("checked", false);
                        tabObj.find("[name=chkIsOverwriteCarrier]").val(0);
                        tabObj.find("[name=chkIsOverwriteCarrier]").change();
                    }

                    tabObj.find("[name=hidCarrierKey]").val(data.notifypartykey); 
				    tabObj.find("[name=carrierName]").val(decodeHTMLEntities(data.notifypartyname));
                    tabObj.find("[name=carrierAddress]").val(data.notifypartyaddress);
				    tabObj.find("[name=carrierName1]").val(data.notifypartyname);
                    tabObj.find("[name=carrierAddress1]").val(data.notifypartyaddress);
					
                    tabObj.find("[name=hidPOLKey]").val(data.polkey);
                    tabObj.find("[name=polName]").val(data.polname);
                    tabObj.find("[name=hidPODKey]").val(data.podkey);
                    tabObj.find("[name=podName]").val(data.podname);
                    tabObj.find("[name=hidPODeliveryKey]").val(data.placeofdeliverykey);
                    tabObj.find("[name=placeOfDeliveryName]").val(data.placeofdeliveryname);
                    tabObj.find("[name=hidPOReceptKey]").val(data.placeofreceiptkey);
                    tabObj.find("[name=placeOfReceiptName]").val(data.placeofreceiptname);
                    tabObj.find("[name=hidAgentKey]").val(data.agentkey);
                    tabObj.find("[name=agentName]").val(decodeHTMLEntities(data.agentname));
                    
					tabObj.find("[name=weight]").val(data.weight);
                    tabObj.find("[name=volume]").val(data.measurement);
 					tabObj.find("[name=package]").val(data.package);
                    tabObj.find("[name=shortDesc]").val(data.itemdescription);
                     
                    tabObj.find("[name=hidFeederKey]").val(data.feederkey);
                    tabObj.find("[name=feederName]").val(data.feedervesselname);
                    tabObj.find("[name=feederNumber]").val(data.feedernumber);

                    tabObj.find("[name=hidVesselKey]").val(data.vesselkey);
                    tabObj.find("[name=vesselName]").val(data.vesselname);
                    tabObj.find("[name=vesselNumber]").val(data.vesselnumber);

                    tabObj.find("[name=selShipmentTermKey]").val(data.shipmenttermkey);
                    tabObj.find("[name=selShipmentTerm2Key]").val(data.shipmentterm2key);
                    tabObj.find("[name=selFreightTermKey]").val(data.freightterm2key);
         
                    tabObj.find("[name=hidConnectingVesselKey]").val(data.connectingvesselkey);
                    tabObj.find("[name=connectingVesselName]").val(data.connectingvesselname);
                    tabObj.find("[name=connectingVesselNumber]").val(data.connectingvesselnumber);
                
                    tabObj.find("[name=hidConnectingVessel2Key]").val(data.connectingvessel2key);
                    tabObj.find("[name=connectingVessel2Name]").val(data.connectingvessel2name);
                    tabObj.find("[name=connectingVessel2Number]").val(data.connectingvessel2number);

                    tabObj.find("[name=shipTo]").val(data.alsonotifyparty);
                    
                    tabObj.find("[name=shippingLineName]").val(data.carriername);
                    tabObj.find("[name=hidShippingLineKey]").val(data.carrierkey);  
  
				    tabObj.find("[name=finalDestinationName]").val(data.finaldestinationname);
                    tabObj.find("[name=hidFinalDestinationKey]").val(data.finaldestinationkey);
                    
                    tabObj.find("[name=mblNumber]").val(data.mblnumber);
             
                    if(data.etdpol != '0000-00-00'){ 
                        tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_)); 
                        tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_));
                    }
                             
					tabObj.find(".inputnumber, .inputdecimal, .inputautodecimal").blur();

                    thisObj.updateWeightVolumeFromJobOrder(data.pkey);
                    thisObj.updateContainerInformation(data.pkey);
                }); 
         
        }
               this.updateWeightVolumeFromJobOrder = function updateWeightVolumeFromJobOrder(pkey) 
        {

            $.ajax({
                type: "GET",
                url: 'ajax-emkl-job-order.php',
                async: false,
                data: "action=getDetailContainer&pkey=" + pkey,
            }).done(function (data) {

                if (!data) return;
                    
                data = JSON.parse(data); 
                
                tabObj.find("[name=sumQty]").val(data[0].qty); 
                tabObj.find("[name=selSumUnit]").val(data[0].unitkey).change(); 
                tabObj.find("[name=sumGrossWeight]").val(data[0].grossweight);  
                tabObj.find("[name=sumNetWeight]").val(data[0].netweight); 
                tabObj.find("[name=sumMeas]").val(data[0].meas); 

                tabObj.find(".inputnumber, .inputdecimal, .inputautodecimal").blur();

            });
        }

        this.updateContainerInformation = function updateContainerInformation(JOKey)
        {

            if (!JOKey) {
                return;
            }

            var selContainerNo = tabObj.find("[name='selContainerNo[]']");

            $.ajax({
                type: "GET",
                url: "ajax-emkl-job-order.php",
                data: "action=getContainerDetailForHBL&jokey="+JOKey,
                async: false,
                beforeSend: function (xhr) {
                    clearAllRows(selContainerNo.closest("div"));
                },
                success: function (data) {
                    if (!data) return;

                    var data = parseJSON(data);

                    var selectOpt = data;

                    selectOpt.unshift({
                        pkey: "0",         
                        containerno: "-----"
                    });


                    reInsertSelectBox(selContainerNo, selectOpt, {
                        "key": "pkey",
                        "label": "containerno"
                    });

                }
            });

        }

        this.onChangeContainerNumber = function onChangeContainerNumber(obj) {
            var JOKey = tabObj.find("[name=hidJobOrderKey]").val();
            var containerkey = obj.val(); 

            var row = obj.closest(".transaction-detail-row");

            $.ajax({
                type: "GET",
                url: "ajax-emkl-job-order.php",
                data: "action=getContainerDetailForHBL&jokey=" + JOKey + "&containerkey=" + containerkey,
                async: false,
                success: function (data) {
                
                    if(!data) {
                        row.find("[name='sealNo[]']").val("");
                        row.find("[name='hidContainerNo[]']").val("");
                        row.find("[name='hidJobOrderDetailKey[]']").val("");

                        return;
                    }

                    var data = parseJSON(data);
                    data = data[0];
                    
                    row.find("[name='sealNo[]']").val(data.sealno);
                    row.find("[name='hidContainerNo[]']").val(data.containerno);
                    row.find("[name='hidJobOrderDetailKey[]']").val(data.refkey);

                }
            });


        }
      
        this.updateConsignee = function updateConsignee()
        {
            var hidConsigneeKey = tabObj.find("[name=hidConsigneeKey]");  
			if(hidConsigneeKey.length == 0) return ; 
			
            var consigneekey = hidConsigneeKey.val();

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
                        tabObj.find("[name=consigneeAddress]").val("");
                    } else {
                        tabObj.find("[name=consigneeAddress]").val(data[0]['address']);
                    }
                    
                }
            });  
        }
        
        this.updateNotifyParty = function updateNotifyParty()
        {
            var hidCarrierKey = tabObj.find("[name=hidCarrierKey]");  
			if(hidCarrierKey.length == 0) return ; 
			
            var carrierkey = hidCarrierKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + carrierkey; 
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

                    if(carrierkey == null || data.length <= 0) {
                        tabObj.find("[name=carrierAddress]").val("");
                    } else {
                        tabObj.find("[name=carrierAddress]").val(data[0]['address']);
                    }
                    
                }
            });  
    }
    
        this.updateShipper = function updateShipper()
        {
            var hidShipperKey = tabObj.find("[name=hidShipperKey]");  
			if(hidShipperKey.length == 0) return ; 
			
            var shipperkey = hidShipperKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + shipperkey; 
            $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async : false,
                beforeSend:function (xhr){
                    
                },
                data: ajaxData,
                success: function (data) {
                    if (!data) return;
                    data = JSON.parse(data);

                    if(shipperkey == null || data.length <= 0) {
                        tabObj.find("[name=shipperAddress]").val("");
                    } else {
                        tabObj.find("[name=shipperAddress]").val(data[0]['address']);
                    }
                    
                }
            });  
    }
    
        this.updateAgent = function updateAgent()
        {
            var hidAgentKey = tabObj.find("[name=hidAgentKey]");  
			if(hidAgentKey.length == 0) return ; 
			
            var agentkey = hidAgentKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + agentkey; 
            $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async : false,
                beforeSend:function (xhr){
                    
                },
                data: ajaxData,
                success: function (data) {
                    if (!data) return;
                    data = JSON.parse(data);

                    if(agentkey == null || data.length <= 0) {
                        tabObj.find("[name=agentAddress]").val("");
                    } else {
                        tabObj.find("[name=agentAddress]").val(data[0]['address']);
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
     
        this.rebindEl = function rebindEl(){
            bindEl(tabObj.find("[name='selContainerNo[]']"),'change',function(){ thisObj.onChangeContainerNumber($(this)) }); 
        }         
        this.loadOnReady = function loadOnReady(){ 
            
            tabObj.find("[name=chkIsOverwriteShipper], [name=chkIsOverwriteConsignee], [name=chkIsOverwriteCarrier], [name=chkIsOverwritePOL], [name=chkIsOverwritePOD], [name=chkIsOverwriteFinalDestination], [name=chkIsOverwriteAgent]").change(function(){thisObj.updateOverwrite($(this))}); 
            tabObj.find("[name=chkIsOverwriteShipper]" ).change();
            tabObj.find("[name=chkIsOverwriteConsignee]" ).change();
            tabObj.find("[name=chkIsOverwriteCarrier]" ).change();
            tabObj.find("[name=chkIsOverwritePOL]" ).change();
            tabObj.find("[name=chkIsOverwritePOD]" ).change();
            tabObj.find("[name=chkIsOverwriteFinalDestination]" ).change();
            tabObj.find("[name=chkIsOverwriteAgent]" ).change();

            // hanya jika kalo ad detail container di form
            
            if (tabObj.find(".container-row-template").length > 0 ){ 
                
                if (!data['containerDetail'] || data['containerDetail'].length == 0) { 
                    addNewTemplateRow("container-row-template", null, null, thisObj.rebindEl);
                }       
            }
     
            
            multiLang(tabObj); 
            thisObj.rebindEl(); 
        }
}
