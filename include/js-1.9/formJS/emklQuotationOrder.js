function EMKLQuotationOrder(tabID,data,varConstant,tablekey,opt) {   
     
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
		this.tabID = tabID;
		this.tablekey = tablekey;  

        var objAndValue = new Array;
		objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue;
    
     	var objAndValue = new Array;
		objAndValue.push({object:'hidDetailPODKey[]', value :'pkey'});  
        var objAndValuePODAutoComplete = objAndValue;
    
       	var objAndValue = new Array;
		objAndValue.push({object:'hidDetailPOLKey[]', value :'pkey'});  
        var objAndValuePOLAutoComplete = objAndValue;
    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidServiceDestinationKey[]', value :'pkey'});   
//		objAndValue.push({object:'chkIsReimburse[]', value :'reimburse'});   
//		objAndValue.push({object:'dummychkIsReimburse[]', value :'reimburse', type:'checkbox'});  
        var objAndValueForDetailServiceAutoComplete = objAndValue;
        
    	var objAndValue = new Array;
		objAndValue.push({object:'hidServiceFreightKey[]', value :'pkey'});   
//	    objAndValue.push({object:'chkIsReimburseFreight[]', value :'reimburse'});     
//		objAndValue.push({object:'dummychkIsReimburseFreight[]', value :'reimburse', type:'checkbox'});  
        var objAndValueForDetailFreightAutoComplete = objAndValue;
    
  	     var objAndValue = new Array;
		objAndValue.push({object:'hidPickupDetailKey[]', value :'pkey'});   
        var objAndValuePickupAutoComplete = objAndValue;
    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidPickupZoneDetailKey[]', value :'pkey'});   
        var objAndValuePickupZoneAutoComplete = objAndValue;

        var objAndValue = new Array;
		objAndValue.push({object:'hidUnitFreightDetailKey[]', value :'pkey'});   
        var objAndValueForDetailUnitCarrierAutoComplete = objAndValue;
 
        var objAndValue = new Array;
		objAndValue.push({object:'hidUnitItemDetailKey[]', value :'pkey'});   
        var objAndValueForDetailUnitItemAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidUnitOriginDetailKey[]', value :'pkey'});   
        var objAndValueForDetailUnitOriginAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidLocationPickupDetailKey[]', value :'pkey'});   
        var objAndValueLocationPickupAutoComplete = objAndValue;
    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidLocationZoneDetailKey[]', value :'pkey'});   
        var objAndValueLocationZoneAutoComplete = objAndValue;
     
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidServiceOriginKey[]', value :'pkey'});   
//		objAndValue.push({object:'chkIsReimburseOrigin[]', value :'reimburse'}); 
//		objAndValue.push({object:'dummychkIsReimburseOrigin[]', value :'reimburse', type:'checkbox'});  
        var objAndValueForDetailOriginAutoComplete = objAndValue; 
        var objAndValue = new Array;
    
		objAndValue.push({object:'hidCommodityKey[]', value :'pkey'});   
        var objAndValueForDetailCommodityAutoComplete = objAndValue;
    
     	var objAndValue = new Array;
		objAndValue.push({object:'hidCarrierDetailKey[]', value :'pkey'});  
        var objAndValueCarrierAutoComplete = objAndValue;
    
     	var objAndValue = new Array;
		objAndValue.push({object:'hidDetailPickupOriginKey[]', value :'pkey'});  
        var objAndValueForDetailPickupAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidDetailZoneOriginKey[]', value :'pkey'});  
        var objAndValueForDetailZoneAutoComplete = objAndValue;
    
		var id = tabObj.find("[name=hidId]").val(); 
	
		var fileFolder = (opt.uploadFileFolder) ? opt.uploadFileFolder : ''; 
        var fileUploaderTarget = (opt.fileUploaderTarget) ? opt.fileUploaderTarget : 'item-file-uploader';   
        var arrFile =  (opt.arrFile) ? opt.arrFile : Array();  
    
        var noLocation = opt.noLocation;
        this.updateQuotation = function updateQuotation(){ 
          
            thisObj.activeAjaxConnections = 0;
            var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0; 
            if(pkey == 0) return;
            
            var ajaxData;
            ajaxData = "action=searchData&pkey=" + pkey;//+ "&customerkey=" + carrierkey 

            $.ajax({
                type: "GET",
                async: false,
                url:  'ajax-emkl-quotation-order.php',
                beforeSend:function (xhr){ 
                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                
                    thisObj.activeAjaxConnections++; 
                },
                data: ajaxData,
                success: function(data){ 


                    data = parseJSON(data);
                    if(data.length == 0)return;
                    data = data[0];
                    tabObj.find('[name=hidCustomerKey]').val(data.customerkey).change();
                    tabObj.find('[name=customerName]').val(decodeHTMLEntities(data.customername));
                    tabObj.find('[name=hidSalesKey]').val(data.saleskey);
                    tabObj.find('[name=saleName]').val(data.salesname);
                    tabObj.find('[name=selAirSea]').val(data.transportationtypekey).change();
                    tabObj.find('[name=shipmentTermKey]').val(data.shipmenttermkey).change();
                    tabObj.find('[name=shipmentTerm2Key]').val(data.shipmentterm2key).change();
                    tabObj.find('[name=selTermOfShipment]').val(data.termofshipmentkey).change();
                    tabObj.find('[name=chkIsShowCurrency]').val(data.isshowcurrency).change();
                    tabObj.find('[name=selCurrency]').val(data.currencykey).change();
                    tabObj.find('[name=trDesc]').val(decodeHTMLEntities(data.trdesc));
                    

                    decreaseActiveAjaxConnections(thisObj); 
                } ,
                 error: function(xhr, errDesc, exception) {
                     decreaseActiveAjaxConnections(thisObj); 
                }
            }); 
                  
//          thisObj.updateTermAndConditionDetail();
          thisObj.updateCommodityDetail();
     
          
        }
    
		this.importData = function importData(){ 
          
          thisObj.updateLocationDetail(varConstant.LOCTYPE.origin); 
          thisObj.updateLocationDetail(varConstant.LOCTYPE.freight); 
          thisObj.updateLocationDetail(varConstant.LOCTYPE.destination);
          
        }
    
           this.updateCommodityDetail = function updateCommodityDetail(){ 
                                                        
        
            thisObj.activeAjaxConnections = 0;
            var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0; 
                
            if(pkey == 0) return;
            
            var ajaxData;
            ajaxData = "action=getDetailCommodity&pkey=" + pkey;

            $.ajax({
                type: "GET",
                url:  'ajax-emkl-quotation-order.php',
                beforeSend:function (xhr){ 
                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                    var obj =  tabObj.find(".form-group"); 
                    obj.closest('.form-group').find('.transaction-detail-row').remove();

                    thisObj.activeAjaxConnections++; 
                },
                data: ajaxData,
                success: function(data){ 


                    data = parseJSON(data);
                    if(data.length == 0){
                        addNewTemplateRow('commodity-row-template','','',thisObj.rebindEl);

                        return;
                    }

                        var i;
                    
                        for(i=0;i<data.length;i++){  
                                     var arrPostValue = [];  
                            
                                        arrPostValue.push({"selector":'hidCommodityKey', "value":data[i].commoditykey}); 
                                        arrPostValue.push({"selector":'commodityName', "value":decodeHTMLEntities(data[i].commodityname)}); 
                                     
                                  addNewTemplateRow('commodity-row-template',JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
        
                             
                        }

                    decreaseActiveAjaxConnections(thisObj); 
                } ,
                 error: function(xhr, errDesc, exception) {
                     decreaseActiveAjaxConnections(thisObj); 
                }
            }); 
        }
           
         this.updateServiceDetail = function updateServiceDetail(row,loctypekey){ 
//                                                        console.log(data);
            thisObj.activeAjaxConnections = 0;
            var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0; 

            var ajaxData,templateRowName,inputHidDetail,inputChkContainer,inputRateCost,inputMinimumCost,inputNormalCost,inputRatePrice,inputMinimumPrice,inputNormalPrice,selZoneField,locationRowTemplate,inputLocationPOLKey,inputLocationPODKey,inputPOLKey,inputPODKey,inputPOLName,inputPODName,inputChkReim,inputAlias,inputServiceName,inputServiceKey,inputUnitName,inputUnitKey,inputSelCurrency,inputSelCurrencyCost,inputSelTax,inputSelTaxCost,inputIncludeTax,inputIncludeTaxCost,inputRemarks,inputRemarksCost,inputCarrierName,inputCarrierKey;
            var originType = varConstant.LOCTYPE.origin;
            var freightType = varConstant.LOCTYPE.freight;
            var destinationType = varConstant.LOCTYPE.destination;
            
            switch(loctypekey){
                case originType :
                    ajaxData = "action=getDetailOriginInformation&pkey=" + pkey +'&iscopy=1';//+ "&customerkey=" + carrierkey 
                    templateRowName = 'origin-row-template';   
                    locationRowTemplate = 'location-row-template';   
                
                    inputPOLKey = 'hidPickupDetailKey';
                    inputPOLName = 'pickupDetailName';
                    inputPODKey = 'hidPickupZoneDetailKey';
                    inputPODName = 'pickupZoneDetailName';
                
                    inputLocationPOLKey = 'hidDetailPickupZoneKey';
                    inputLocationPODKey = 'hidDetailZoneKey';

                    inputChkReim = 'chkIsReimburseOrigin';
                    inputAlias = 'aliasOrigin';
                    inputServiceKey = 'hidServiceOriginKey';
                    inputServiceName = 'serviceOriginName';
                    inputUnitKey = 'hidUnitOriginDetailKey';
                    inputUnitName = 'unitOriginDetailName';
                    inputSelCurrency = 'selCurrencyOriginDetail';
                    inputSelCurrencyCost = 'selCurrencyCostOriginDetail';
                    inputSelTax = 'taxPercentageOrigin';
                    inputSelTaxCost = 'taxPercentageOriginCost';
                    inputRemarks = 'serviceOriginRemarks';
                    inputRemarksCost = 'serviceOriginRemarksCost';
                    inputIncludeTax = 'chkIncludeTaxOriginDetail';
                    inputIncludeTaxCost = 'chkIncludeTaxOriginCostDetail';
                    inputRatePrice = 'ratePriceOrigin' ;              
                    inputNormalPrice = 'normalPriceOrigin' ;              
                    inputMinimumPrice = 'minimumPriceOrigin' ;    
					inputChkContainer = 'chkContainerOrigin';
                    inputHidDetail = 'hidDetailItemOriginKey';

                    inputRateCost = 'rateCostOrigin' ;              
                    inputNormalCost= 'normalCostOrigin' ;              
                    inputMinimumCost = 'minimumCostOrigin' ;  
                    
                    selZoneField = 'selOriginZone';



                    break;
                case freightType :
                    ajaxData = "action=getFreightDetail&pkey=" + pkey +'&iscopy=1';//+ "&customerkey=" + carrierkey 
					templateRowName = 'item-row-template';    
					locationRowTemplate = 'location-freight-row-template';   
					selZoneField = 'selFreightZone';

					inputPOLKey = 'hidDetailPOLKey';
					inputPOLName = 'detailPOLName';
					inputPODKey = 'hidDetailPODKey';
					inputPODName = 'detailPODName';

					inputLocationPOLKey = 'hidDetailFreightPOLKey';
					inputLocationPODKey = 'hidDetailFreightPODKey';

					inputChkReim = 'chkIsReimburseFreight';
					inputAlias = 'aliasCarrier';
					inputServiceKey = 'hidServiceFreightKey';
					inputServiceName = 'serviceFreightName';
					inputUnitKey = 'hidUnitFreightDetailKey';
					inputUnitName = 'unitFreightDetailName';
					inputSelCurrency = '    selCurrencyDetail';
					inputSelCurrencyCost = 'selCurrencyCostDetail';
					inputSelTax = 'taxPercentageCarrier';
					inputSelTaxCost = 'taxPercentageCarrierCost';
					inputRemarks = 'carrierRemarks';
					inputRemarksCost = 'remarksCost';
					inputIncludeTax = 'chkIncludeTaxCarrierDetail';
					inputIncludeTaxCost = 'chkIncludeTaxCarrierCostDetail';

					inputChkContainer = 'chkContainerFreight';


					inputHidDetail = 'hidDetailCarrierKey';


					inputRatePrice = 'rateFreight' ;              
					inputNormalPrice = 'normalPriceFreight' ;              
					inputMinimumPrice = 'minimumPriceFreight' ;    
                    
                    inputRateCost = 'costFreight' ;              
                    inputNormalCost= 'normalCostFreight' ;              
                    inputMinimumCost = 'minimumCostFreight' ; 
                
                    inputCarrierName = 'carrierDetailName';
                    inputCarrierKey = 'hidCarrierDetailKey';
                
                    break;
                 case destinationType :
					ajaxData = "action=getDetailServiceInformation&pkey=" + pkey +'&iscopy=1' ;//+ "&customerkey=" + carrierkey 
					templateRowName = 'service-row-template';    
					locationRowTemplate = 'location-destination-row-template';   
					inputChkContainer = 'chkContainerDest';

					selZoneField = 'selDestinationZone';
					inputHidDetail = 'hidDetailItemDestinationKey';

					inputPOLKey = 'hidLocationPickupDetailKey';
					inputPOLName = 'pickupLocationDetailName';
					inputPODKey = 'hidLocationZoneDetailKey';
					inputPODName = 'zoneLocationDetailName';

					inputLocationPOLKey = 'hidDetailLocationPickupKey';
					inputLocationPODKey = 'hidDetailLocationZoneKey';

					inputRatePrice = 'ratePriceDestination' ;              
					inputNormalPrice = 'normalPriceItem' ;              
					inputMinimumPrice = 'minimumPriceItem' ;    

					inputRateCost = 'rateCostItem' ;              
					inputNormalCost= 'normalCostItem' ;              
                    inputMinimumCost = 'minimumCostItem' ; 

                    inputChkReim = 'chkIsReimburse';
                    inputAlias = 'aliasService';
                    inputServiceKey = 'hidServiceKey';
                    inputServiceName = 'serviceName';
                    inputUnitKey = 'hidUnitItemDetailKey';
                    inputUnitName = 'unitItemDetailName';
                    inputSelCurrency = 'selCurrencyItemDetail';
                    inputSelCurrencyCost = 'selCurrencyItemCostDetail';
                    inputSelTax = 'taxPercentageService';
                    inputSelTaxCost = 'taxPercentageServiceCost';
                    inputRemarks = 'serviceRemarks';
                    inputRemarksCost = 'serviceRemarksCost';
                    inputIncludeTax = 'chkIncludeTaxServiceDetail';
                    inputIncludeTaxCost = 'chkIncludeTaxServiceCostDetail';
                
                    break;
            }
            
            
         

            $.ajax({
                type: "GET",
                url:  'ajax-emkl-quotation-order.php',
                beforeSend:function (xhr){ 
                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
//                    clearAllRows(tabObj.find(".detail-item"));

                    thisObj.activeAjaxConnections++; 
                },
                data: ajaxData,
                success: function(data){ 


                    var dataSet = parseJSON(data);

                    if(dataSet.length == 0) return;
                    
                        
                        var i,j;
                    var arrContainerOther = [];
                    row.each(function(){   
                        var itrRow = $(this);
                        var polkey = itrRow.find('[name="'+inputPOLKey+'[]"]').val(); 
                        var podkey = itrRow.find('[name="'+inputPODKey+'[]"]').val(); 
                        var data = dataSet[polkey+' - '+podkey]; 
                                          


                        if (data == undefined ) return;

                      

                         for(i=0;i<data.length;i++){  
   
                                   var arrPostValue = [];  
                                                             
                             
                                

                                    var price = parseFloat(data[i].price);
                                    var cost = parseFloat(data[i].cost);
                                    var normalprice = parseFloat(data[i].normalprice);
                                    var normalcost = parseFloat(data[i].normalcost);
                                    var minimumprice = parseFloat(data[i].minimumprice);
                                    var minimumcost = parseFloat(data[i].minimumcost);
                             
                                    if(price !== 0 || cost !== 0)
                                      arrContainerOther.push('1'); 
                             
                                    if(normalprice !== 0 || minimumcost !== 0)
                                      arrContainerOther.push('3'); 
                             
                             
                                    if(minimumprice !== 0 || normalcost !== 0)
                                      arrContainerOther.push('2'); 

                             
                                    arrPostValue.push({"selector":inputRatePrice, "value":data[i].price}); 
                                    arrPostValue.push({"selector":inputRateCost, "value":data[i].cost}); 
                                    arrPostValue.push({"selector":inputNormalPrice, "value":normalprice}); 
                                    arrPostValue.push({"selector":inputNormalCost, "value":normalcost}); 
                                    arrPostValue.push({"selector":inputMinimumPrice, "value":minimumprice}); 
                                    arrPostValue.push({"selector":inputMinimumCost, "value":minimumcost}); 
                                   
                                    arrPostValue.push({"selector":inputLocationPOLKey, "value":polkey}); 
                                    arrPostValue.push({"selector":inputLocationPODKey, "value":podkey}); 
                                    arrPostValue.push({"selector":inputHidDetail, "value":data[i].pkey}); 
                                    arrPostValue.push({"selector":inputChkReim, "value":data[i].isperreciept}); 
                                    arrPostValue.push({"selector":inputAlias, "value":data[i].alias}); 
                                    arrPostValue.push({"selector":inputServiceKey, "value":data[i].servicekey}); 
                                    arrPostValue.push({"selector":inputServiceName, "value":data[i].servicename}); 
                                    arrPostValue.push({"selector":inputSelCurrency, "value":data[i].currencykey  }); 
                                    arrPostValue.push({"selector":inputUnitName, "value":data[i].unitname}); 
                                    arrPostValue.push({"selector":inputUnitKey, "value":data[i].unitkey}); 
                                    arrPostValue.push({"selector":inputSelCurrency, "value": data[i].costcurrencykey}); 
                                    arrPostValue.push({"selector":inputRemarks, "value":data[i].remarks}); 
                                    arrPostValue.push({"selector":inputRemarksCost, "value":data[i].costremarks}); 
                                    arrPostValue.push({"selector":inputSelTax, "value":parseFloat(data[i].taxpercentage)}); 
                                    arrPostValue.push({"selector":inputSelTaxCost, "value":parseFloat(data[i].taxpercentagecost)}); 
                                    arrPostValue.push({"selector":inputIncludeTax, "value":data[i].ispriceincludetax}); 
                                    arrPostValue.push({"selector":inputIncludeTaxCost, "value":data[i].ispriceincludetaxcost});

                                    if(loctypekey == freightType){
                                        arrPostValue.push({"selector":inputCarrierName, "value":data[i].carriername}); 
                                        arrPostValue.push({"selector":inputCarrierKey, "value":data[i].carrierkey}); 
                                    }
                                    
                                  newrow = addNewTemplateRow(templateRowName,JSON.stringify(arrPostValue),itrRow,thisObj.rebindEl);   
                                    newrow.find("[name='"+inputIncludeTax+"[]']").val(data[i].ispriceincludetax).change();
                                    newrow.find("[name='"+inputIncludeTaxCost+"[]']").val(data[i].ispriceincludetaxcost).change();
                                    newrow.find("[name='"+inputChkReim+"[]']").val(data[i].isperreciept).change();
                                    thisObj.updateCostDetail(newrow,loctypekey);
                        }
                     });
                        
//                    console.log(arrContainerOther);
//                    var containerOthers = arrContainerOther.filter()
                    var containerOthers = arrContainerOther.filter(function(item, pos){
                          return arrContainerOther.indexOf(item) === pos; 
                        });
                    
                      if (containerOthers[0] == undefined ) return;

                            thisObj.checklistContainer(inputChkContainer,"-"+containerOthers[0]);
                  

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
           

         
//         this.updateTermAndConditionDetail = function updateTermAndConditionDetail(){ 
//            // nanti saja, karena nanti pake copy dari php                                            
//			return;
//        
//            thisObj.activeAjaxConnections = 0;
//            var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0; 
//                
//            if(pkey == 0) return;
//            
//            var ajaxData;
//            ajaxData = "action=getDetailTermAndCondition&pkey=" + pkey;
//
//            $.ajax({
//                type: "GET",
//                url:  'ajax-emkl-quotation-order.php',
//                beforeSend:function (xhr){ 
//                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
//                    var obj =  tabObj.find(".form-group"); 
//                    obj.closest('.form-group').find('.transaction-detail-row').remove();
//                 
//                    thisObj.activeAjaxConnections++; 
//                },
//                data: ajaxData,
//                success: function(data){ 
//
//
//                    data = parseJSON(data);
//                    if(data.length == 0){
//                           
//                    var selTermsAndCondition = tabObj.find("[name=\"selTermsConditionKey[]\"]");
//					selTermsAndCondition.val(tabObj.find("[name=\"selTermsConditionKey[]\"] option:first")).change();
//
//                        addNewTemplateRow('termsandcondition-row-template','','');
//
//                        return;
//                    }
//
//                        var i;
//                    
//                        for(i=0;i<data.length;i++){  
//                              var arrPostValue = [];  
//                            
//                             arrPostValue.push({"selector":'selTermsConditionKey', "value":data[i].termsconditionkey}); 
//                             newrow =  addNewTemplateRow('termsandcondition-row-template',JSON.stringify(arrPostValue));  
//                             newrow.find("[name='selTermsConditionKey[]']").val(data[i].termsconditionkey).change();
//
//                             
//                        }
//
//                    decreaseActiveAjaxConnections(thisObj); 
//                } ,
//                 error: function(xhr, errDesc, exception) {
//                     decreaseActiveAjaxConnections(thisObj); 
//                }
//            }); 
//        }
        
        this.updateLocationDetail = function updateLocationDetail(loctypekey){ 
                                                        
        
            thisObj.activeAjaxConnections = 0;
            var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0; 
                
            if(pkey == 0) return;
            
            var ajaxData,templateRowName,selZoneField,locationRowTemplate,inputPOLKey,inputPODKey,inputPOLName,inputPODName;
            var originType = varConstant.LOCTYPE.origin;
            var freightType = varConstant.LOCTYPE.freight;
            var destinationType = varConstant.LOCTYPE.destination;
            
            ajaxData = "action=getDetailLocation&pkey=" + pkey +'&type=' +loctypekey;//+ "&customerkey=" + carrierkey 

            switch(loctypekey){
                case originType :
					templateRowName = 'origin-row-template';   
					locationRowTemplate = 'location-row-template';   

					inputPOLKey = 'hidPickupDetailKey';
					inputPOLName = 'pickupDetailName';
					inputPODKey = 'hidPickupZoneDetailKey';
					inputPODName = 'pickupZoneDetailName';
					selZoneField = 'selOriginZone';

                
                    break;
                case freightType :
					templateRowName = 'item-row-template';    
					locationRowTemplate = 'location-freight-row-template';   

					inputPOLKey = 'hidDetailPOLKey';
					inputPOLName = 'detailPOLName';
					inputPODKey = 'hidDetailPODKey';
					inputPODName = 'detailPODName';

					selZoneField = 'selFreightZone';

                    break;
                 case destinationType :
					templateRowName = 'service-row-template';    
					locationRowTemplate = 'location-destination-row-template';   
					selZoneField = 'selDestinationZone';

                     
                    inputPOLKey = 'hidLocationPickupDetailKey';
                    inputPOLName = 'pickupLocationDetailName';
                    inputPODKey = 'hidLocationZoneDetailKey';
                    inputPODName = 'zoneLocationDetailName';

                
                    break;
            }
             
                        

			thisObj.resetChekclistContainer();
			thisObj.updateContainerInformation(loctypekey);
 

            $.ajax({
                type: "GET",
                url:  'ajax-emkl-quotation-order.php',
                beforeSend:function (xhr){ 
                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                   var obj =  tabObj.find(".table-container"); 
                    obj.closest('.table-container').find('.transaction-detail-row').remove();

                    thisObj.activeAjaxConnections++; 
                },
                data: ajaxData,
                success: function(data){ 


                    data = parseJSON(data);
                    if(data.length == 0){
                        
                        newrow = addNewTemplateRow(locationRowTemplate,'','',thisObj.rebindEl);
                        addNewTemplateRow(templateRowName,'',newrow,thisObj.rebindEl);
                            
                        return;
                    }

                        var i;
                    
                        for(i=0;i<data.length;i++){  
                                var arrPostValue = [];  

                                for(var j=0;j < data[i].length;j++){
                                        
                                   var polName = data[i][0].polcode;
                                   var podName = data[i][0].podcode;
                                        
//                                    
//                                    if(loctypekey == varConstant.LOCTYPE.freight){
//                                        polName = data[i][0].polcode+ ' - '+data[i][0].polname;
//                                        podName = data[i][0].podcode+ ' - '+data[i][0].podname;
//                                    }
//                                    
									
									arrPostValue.push({"selector":inputPOLKey, "value":data[i][0].polkey}); 
									arrPostValue.push({"selector":inputPOLName, "value":polName}); 
									arrPostValue.push({"selector":inputPODKey, "value":data[i][0].podkey}); 
									arrPostValue.push({"selector":inputPODName, "value":podName}); 
                                   
                                }
                        
                                newrow = addNewTemplateRow(locationRowTemplate,JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                                 
                                thisObj.updateServiceDetail(newrow,loctypekey);

                             
                             
                        }
                     thisObj.updateSelectZone(loctypekey);

                    decreaseActiveAjaxConnections(thisObj); 
                } ,
                 error: function(xhr, errDesc, exception) {
                     decreaseActiveAjaxConnections(thisObj); 
                }
            }); 
        }
        
        this.resetChekclistContainer = function resetChekclistContainer(){
                /*tabObj.find(".chklist-container").prop('checked', false);
                tabObj.find(".chklist-container").val(0);*/
               tabObj.find(".chklist-container").each(function() {  
                 var objContainer = $(this).closest('.table-container');
                 objContainer.find("div[relheaderkey="+$(this).attr('relcontainerkey')+"]").hide();
             });
        }
        
         this.updateCostDetail = function updateCostDetail(row,loctypekey){ 

            thisObj.activeAjaxConnections = 0;
            var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0; 

            var ajaxData,templateRowName,inputHidDetail,inputPrice,inputCost;
            var originType = varConstant.LOCTYPE.origin;
            var freightType = varConstant.LOCTYPE.freight;
            var destinationType = varConstant.LOCTYPE.destination;
            
             
            ajaxData = "action=getQuotationPriceAndCost&pkey=" + pkey + '&loctypekey=' + loctypekey +'&iscopy=1';//+ "&customerkey=" + carrierkey 

            switch(loctypekey){
                case originType :
                    templateRowName = 'origin-row-template';   
                   

                    inputPrice = 'containerOrigin_';
                    inputCost = 'costOrigin_';
                    inputHidDetail = 'hidDetailItemOriginKey';
                  

                    break;
                case freightType :
                    templateRowName = 'item-row-template';    


                    inputPrice = 'containerFreight_';
                    inputCost = 'cost_';
                    inputHidDetail = 'hidDetailCarrierKey';
               
                
                    break;
                 case destinationType :
                    templateRowName = 'service-row-template';    
       
                    inputPrice = 'containerDestination_';
                    inputCost = 'costItem_';
                    inputHidDetail = 'hidDetailItemDestinationKey';
             
                
                    break;
            }
            
            
         

            $.ajax({
                type: "GET",
                url:  'ajax-emkl-quotation-order.php',
                beforeSend:function (xhr){ 
                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
//                    clearAllRows(tabObj.find(".detail-item"));

                    thisObj.activeAjaxConnections++; 
                },
                data: ajaxData,
                success: function(data){  

                    var dataSet = parseJSON(data); 
                    if(dataSet.length == 0) return;
                        
					row.each(function(){   
                        var itrRow = $(this);
                        var pkey = itrRow.find('[name="'+inputHidDetail+'[]"]').val(); 
                        var data = dataSet[pkey]; 
                                                
                        if (data == undefined ) return;
                        var i; 
 						for(i=0;i<data.length;i++){     
                                itrRow.find("[name='"+inputPrice+""+data[i].containerkey+"[]']").val(data[i].price);
                                itrRow.find("[name='"+inputCost+""+data[i].containerkey+"[]']").val(data[i].cost); 
                        }
                     });
                        
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
         
         
          this.updateContainerInformation = function updateContainerInformation(loctypekey){
			var pkey  = tabObj.find("[name=hidQuotationKey]").val() || 0;  
			var inputChkContainer,templateRowName;
			var originType = varConstant.LOCTYPE.origin;
			var freightType = varConstant.LOCTYPE.freight;
			var destinationType = varConstant.LOCTYPE.destination;
			  
            switch(loctypekey){
                case originType :
                    templateRowName = 'origin-row-template';   
                    inputChkContainer = 'chkContainerOrigin';
 

                    break;
                case freightType :
                    templateRowName = 'item-row-template';    
                    locationRowTemplate = 'location-freight-row-template';   
                    inputChkContainer = 'chkContainerFreight';

                
                    break;
                 case destinationType :
                    templateRowName = 'service-row-template';    
                    locationRowTemplate = 'location-destination-row-template';   
                           inputChkContainer = 'chkContainerDest';

                
                    break;
            }			            

			  $.ajax({
                type: "GET",
                url:  'ajax-emkl-quotation-order.php', 
				asyc: false,
                beforeSend:function (xhr){

//                    clearAllRows(tabObj.find(".mnv-transaction")); 
                },
                data: "action=getContainerQuotation&pkey=" +  pkey + '&loctypekey=' + loctypekey + '&iscopy=1',  
                success: function(data){ 

                    var data = parseJSON(data);
               
                    if(data.length == 0){
                        return;
                        
                    }
                              
                    var i;
                      for(i=0;i<data.length;i++){  

                                var containerkey = data[i];


                                thisObj.checklistContainer(inputChkContainer,containerkey+"[]");
                                  
                                 
                        }
                    
                }
              })
			
			 
        }
          
         this.checklistContainer = function checklistContainer(inputChkContainer,containerkey){
             
                 tabObj.find("[name=\"dummy"+inputChkContainer+""+containerkey+"\"]").val(1).click();
                 tabObj.find("[name=\""+inputChkContainer+""+containerkey+"\"]").val(1).change();

             tabObj.find(".chklist-container:checked").each(function() {  
                 var objContainer = $(this).closest('.table-container');
                 objContainer.find("div[relheaderkey="+$(this).attr('relcontainerkey')+"]").show();
             });
         }
         
        
         this.updateItemInformation = function updateItemInformation(){
			
			 // update available BOM 
			
			  $.ajax({
                type: "GET",
                url:  'ajax-emkl-quotation-order.php', 
				asyc: false,
                beforeSend:function (xhr){

//                    clearAllRows(tabObj.find(".mnv-transaction")); 
                },
                data: "action=getEmklType&pkey=" +  tabObj.find("[name=selAirSea]" ).val(),  
                success: function(data){ 
					var selectOpt = JSON.parse(data);  

					var selContainerType = tabObj.find("[name=\"selContainerType\"]");
					reInsertSelectBox(selContainerType,selectOpt, {"key" : "pkey", "label" : "name"} ); 
					selContainerType.val(tabObj.find("[name=\"selContainerType\"] option:first").val()).change();
                    
                    thisObj.rebindPODPOL();
                }
              })
			
			 
        }
         
         
//         this.updateTermsAndCondition = function updateTermsAndCondition(){
//             
//			 return;
//			  $.ajax({
//                type: "GET",
//                url:  'ajax-terms-and-condition.php', 
//				asyc: false,
//                beforeSend:function (xhr){
////                    clearAllRows(tabObj.find(".detail-term")); 
//                },
//                data: "action=searchData&categorykey=" +  tabObj.find("[name=selTypeOfJob]" ).val(),  
//                success: function(data){ 
//					var selectOpt = JSON.parse(data);  
//
//					var selTermsAndCondition = tabObj.find("[name=\"selTermsConditionKey[]\"]");
//
//					reInsertSelectBox(selTermsAndCondition,selectOpt, {"key" : "pkey", "label" : "value"} ); 
//					selTermsAndCondition.val(tabObj.find("[name=\"selTermsConditionKey[]\"] option:first").val()).change();
//                    
//                }
//              })
//			
//			 
//        }         
        
        this.updateAirOrSea = function updateAirOrSea(onload){  
            var selAirSeaObj = tabObj.find("[name=selAirSea]");
            var selContainerObj = tabObj.find("[name=selContainerType]");
            var selVolumeTypeObj = tabObj.find("[name=selVolumeType]");
             
            if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea){

                tabObj.find(".incoterms").show();

            }else if(selAirSeaObj.val() == varConstant.EMKL.shipping.air){
                tabObj.find(".incoterms").show();

            }else{ 
                tabObj.find(".incoterms").hide();

            }
                
            selContainerObj.change();
            selVolumeTypeObj.change();
            
            thisObj.updateContainer(onload);
            
            
        }    
        
         this.updateChklistContainer = function updateChklistContainer(obj){
            
             var objContainer = obj.closest('.table-container');
              var col = objContainer.find("div[relheaderkey="+obj.attr('relcontainerkey')+"]");
             
              if(obj.val() == 1){
                 col.show();
              }else{
                  col.hide();
              }
         }
         
         this.showDetailLocation = function showDetailLocation(obj){
              
			var objContainer = obj.closest('.table-container');
			var polpodkey = objContainer.find("div[reldetailkey="+obj.val()+"]");
			var col =   objContainer.find(".destination-row");
			col.hide();
			polpodkey.show();
         
         }
      
                    
    
        
        this.updateContainer = function updateContainer(onload){
          
			// tidak perlu dipisah dulu sea/air

//
//			var selObj = tabObj.find("[name=selAirSea]");
//			if(onload == undefined) onload = false;
//
//			if(!onload)
//				tabObj.find('.chklist-container:checked').click();
//
//            tabObj.find(".ct-item").hide(); 
//            tabObj.find(".ct-type-"+selObj.val()).show();  
        } 
                
         
        
        this.updateContactInformation = function updateContactInformation(){
  
			 // update available BOM 
			
			  $.ajax({
                type: "GET",
                url:  'ajax-customer.php', 
				asyc: false,
                beforeSend:function (xhr){
//                    clearAllRows(tabObj.find(".mnv-transaction")); 
                },
                data: "action=getContactPerson&pkey=" +  tabObj.find("[name=hidCustomerKey]" ).val(),  
                success: function(data){  
					var selectOpt = JSON.parse(data);  

					var selPIC = tabObj.find("[name=\"selPIC\"]");
					reInsertSelectBox(selPIC,selectOpt, {"key" : "pkey", "label" : "name"} ); 
					selPIC.val(tabObj.find("[name=\"selPIC\"] option:first").val()).change();
                }
              })
			
			 
        }
        
        
          this.updatePickupZoneDetail = function updatePickupZoneDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
            var hidDetailZoneKey = detailRow.find("[name=\"hidDetailZoneKey[]\"]");

              for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  
  
                          hidDetailZoneKey.val(ui.item['pkey']);

//            hidDetailLocationPickupKey.val(ui.item['pkey']);
              
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"pickupZoneDetailName[]\"]").val(ui.item['value']); 
        }
          
        this.updateChecklistOrigin = function updateChecklistOrigin(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 

              for(i=0;i<objAndValue.length;i++){
                if (objAndValue[i].type == "checkbox"){

                  if( ui.item['reimburse'] == 1 ) {
                      detailRow.find("[name='" + objAndValue[i].object +"']").click();
                 
                       detailRow.find(".input-selling").find("option:not(:selected)").attr('disabled', true);
                       detailRow.find(".input-cost").find("option:not(:selected)").attr('disabled', true);

                       detailRow.find("[name='" + objAndValue[i].object +"']").prop("checked", true);
                  }else{
                        detailRow.find(".input-selling").attr('readonly', false);
                        detailRow.find(".input-cost").attr('readonly', false);    
                      detailRow.find(".input-selling").find("option:not(:selected)").removeAttr('disabled');
                      detailRow.find(".input-cost").find("option:not(:selected)").removeAttr('disabled');

                     detailRow.find("[name='" + objAndValue[i].object +"']").prop("checked", false);

                  }
                     
                }
                  
                    
                  detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  

 
              }   

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"serviceOriginName[]\"]").val(ui.item['value']); 

            thisObj.updateAliasOrigin(target);

        }
        
        this.updateChecklistDest = function updateChecklistDest(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
              for(i=0;i<objAndValue.length;i++){
                if (objAndValue[i].type == "checkbox"){

                  if( ui.item['reimburse'] == 1 ) {
                      detailRow.find("[name='" + objAndValue[i].object +"']").click();
                       detailRow.find(".input-selling").find("option:not(:selected)").attr('disabled', true);
                       detailRow.find(".input-cost").find("option:not(:selected)").attr('disabled', true);

                       detailRow.find("[name='" + objAndValue[i].object +"']").prop("checked", true);
                  }else{
                        detailRow.find(".input-selling").attr('readonly', false);
                        detailRow.find(".input-cost").attr('readonly', false);    
                      detailRow.find(".input-selling").find("option:not(:selected)").removeAttr('disabled');
                      detailRow.find(".input-cost").find("option:not(:selected)").removeAttr('disabled');

                     detailRow.find("[name='" + objAndValue[i].object +"']").prop("checked", false);

                  }
                     
                }
                  
                    
                  detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  

 
              }   

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"serviceDestinationName[]\"]").val(ui.item['value']); 

            thisObj.updateAliasDestination(target);

        }

    this.updateChecklistFreight = function updateChecklistFreight(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 

              for(i=0;i<objAndValue.length;i++){
                if (objAndValue[i].type == "checkbox"){

                  if( ui.item['reimburse'] == 1 ) {
                      detailRow.find("[name='" + objAndValue[i].object +"']").click();
                       detailRow.find(".input-selling").find("option:not(:selected)").attr('disabled', true);
                       detailRow.find(".input-cost").find("option:not(:selected)").attr('disabled', true);

                       detailRow.find("[name='" + objAndValue[i].object +"']").prop("checked", true);
                  }else{
                        detailRow.find(".input-selling").attr('readonly', false);
                        detailRow.find(".input-cost").attr('readonly', false);    
                      detailRow.find(".input-selling").find("option:not(:selected)").removeAttr('disabled');
                      detailRow.find(".input-cost").find("option:not(:selected)").removeAttr('disabled');

                     detailRow.find("[name='" + objAndValue[i].object +"']").prop("checked", false);

                  }
                     
                }
                  
                    
                  detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  

 
              }   

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"serviceFreightName[]\"]").val(ui.item['value']); 

            thisObj.updateAliasFreight(target);
        }

        this.updateAliasOrigin = function updateAliasOrigin(target) {

            var customerkey = tabObj.find("[name=hidCustomerKey]").val();
            var detailRow = $(target).closest(".transaction-detail-row"); 
            var itemkey = detailRow.find("[name=\"hidServiceOriginKey[]\"]").val(); 

             $.ajax({
                type: "GET",
                 url: 'ajax-customer.php', 
                beforeSend:function (xhr){ 
                    thisObj.activeAjaxConnections++;
	            }, 
                data: "action=getItemAlias&pkey=" + customerkey + "&itemkey=" + itemkey,  
                 success: function (data) {  
                     if(!data)return;                    

                    data = parseJSON(data);
                    if(data.length == 0)return;
                     
                    data = data[0];
                    detailRow.find("[name=\"aliasOrigin[]\"]").val(data.alias);
                },
	             error: function(xhr, errDesc, exception) { 
                        decreaseActiveAjaxConnections(thisObj);
                } 
            });

    }
    
    this.updateAliasFreight = function updateAliasFreight(target) {

            var customerkey = tabObj.find("[name=hidCustomerKey]").val();
            var detailRow = $(target).closest(".transaction-detail-row"); 
            var itemkey = detailRow.find("[name=\"hidServiceFreightKey[]\"]").val(); 

             $.ajax({
                type: "GET",
                 url: 'ajax-customer.php', 
                beforeSend:function (xhr){ 
                    thisObj.activeAjaxConnections++;
	            }, 
                data: "action=getItemAlias&pkey=" + customerkey + "&itemkey=" + itemkey,  
                 success: function (data) {  
                     if(!data)return;                    

                     data = parseJSON(data);
                    if(data.length == 0)return;                    
                     
                     data = data[0];

                    detailRow.find("[name=\"aliasCarrier[]\"]").val(data.alias);
                },
	             error: function(xhr, errDesc, exception) { 
                        decreaseActiveAjaxConnections(thisObj);
                } 
            });

        }


     this.updateAliasDestination = function updateAliasDestination(target) {

            var customerkey = tabObj.find("[name=hidCustomerKey]").val();
            var detailRow = $(target).closest(".transaction-detail-row"); 
            var itemkey = detailRow.find("[name=\"hidServiceKey[]\"]").val(); 

             $.ajax({
                type: "GET",
                 url: 'ajax-customer.php', 
                beforeSend:function (xhr){ 
                    thisObj.activeAjaxConnections++;
	            }, 
                data: "action=getItemAlias&pkey=" + customerkey + "&itemkey=" + itemkey,  
                success: function(data){  
                     if(!data)return;                    

                    data = parseJSON(data);
                    if(data.length == 0)return;
                    
                    data = data[0];
                    detailRow.find("[name=\"aliasService[]\"]").val(data.alias);
                },
	             error: function(xhr, errDesc, exception) { 
                        decreaseActiveAjaxConnections(thisObj);
                } 
            });

        }
          
        this.updatePickupDetail = function updatePickupDetail(target,objAndValue,ui){
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
            var hidDetailPickupZoneKey = detailRow.find("[name=\"hidDetailPickupZoneKey[]\"]");

              for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  
  
            hidDetailPickupZoneKey.val(ui.item['pkey']);

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"pickupDetailName[]\"]").val(ui.item['value']); 
        }
                
        this.updateLocationPickupDetail = function updateLocationPickupDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
            var hidDetailLocationPickupKey = detailRow.find("[name=\"hidDetailLocationPickupKey[]\"]");

              for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  
  
            hidDetailLocationPickupKey.val(ui.item['pkey']);
              
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"pickupLocationDetailName[]\"]").val(ui.item['value']); 
        }
                
        this.updateLocationZoneDetail = function updateLocationZoneDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
            var hidDetailLocationZoneKey = detailRow.find("[name=\"hidDetailLocationZoneKey[]\"]");

              for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  
  
            hidDetailLocationZoneKey.val(ui.item['pkey']);
              
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"zoneLocationDetailName[]\"]").val(ui.item['value']); 
        }            
        
          this.updatePOLDetail = function updatePOLDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
            var hidDetailPOLKey = detailRow.find("[name=\"hidDetailFreightPOLKey[]\"]");


              for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  
        
            hidDetailPOLKey.val(ui.item['pkey']);
              
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"detailPOLName[]\"]").val(ui.item['value']); 
        }
          

          
        this.updatePODDetail = function updatePODDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row"); 
   
            var hidDetailPODKey = detailRow.find("[name=\"hidDetailFreightPODKey[]\"]");

              for(i=0;i<objAndValue.length;i++)   
                detailRow.find("[name='" + objAndValue[i].object +"']").val(ui.item[objAndValue[i].value]).blur();  
  
            hidDetailPODKey.val(ui.item['pkey']);
              
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"detailPODName[]\"]").val(ui.item['value']); 
        }
        
        this.disabledTax = function disabledTax(obj){

                var objDetail = obj.closest('.transaction-detail-row');
                var objChkList = objDetail.find(".chk-list-reciept");
 

                    if(objChkList.val() == 1){
                        
                        objDetail.find(".chklist-include-selling").attr('readonly', true);
                        objDetail.find(".chklist-include-cost").attr('readonly', true);
                        objDetail.find(".input-selling").attr('readonly', true);
                        objDetail.find(".input-cost").attr('readonly', true);           
              
                    }else{ 
						objDetail.find(".input-selling").attr('readonly', false);
						objDetail.find(".input-cost").attr('readonly', false);
						objDetail.find(".chklist-include-selling").attr('readonly', false);
						objDetail.find(".chklist-include-cost").attr('readonly', false);
                    } 
            
        }

        this.updateLocationPickup = function updateLocationPickup(obj){
            var detailRow = obj.closest(".transaction-detail-row");  
            var locationkey = obj.val();
			detailRow.find("[name='hidDetailLocationPickupKey[]']").val(locationkey); 
        }   
        
        this.updateLocationZone = function updateLocationZone(obj){
            var detailRow = obj.closest(".transaction-detail-row");  
            var locationkey = obj.val();
			detailRow.find("[name='hidDetailLocationZoneKey[]']").val(locationkey); 
        }  
        
        this.updateLocationZone = function updateLocationZone(obj){
            var detailRow = obj.closest(".transaction-detail-row");  
            var locationkey = obj.val();
			detailRow.find("[name='hidDetailLocationZoneKey[]']").val(locationkey); 
        }  
        
        
         this.updatePickup = function updatePickup(obj){
			 
            var detailRow = obj.closest(".transaction-detail-row");  
			var locationkey = obj.val(); 
			detailRow.find("[name='hidDetailPickupZoneKey[]']").val(locationkey); 
        }   
        
        this.updateZone = function updateZone(obj){
            var detailRow = obj.closest(".transaction-detail-row");  
            var locationkey = obj.val();
			detailRow.find("[name='hidDetailZoneKey[]']").val(locationkey); 
            
        }  
        
        this.updatePOL = function updatePOL(obj){
            var detailRow = obj.closest(".transaction-detail-row");  
            var locationkey = obj.val();
			detailRow.find("[name='hidDetailFreightPOLKey[]']").val(locationkey);  
        }  
              
        this.updatePOD = function updatePOD(obj){
            var detailRow = obj.closest(".transaction-detail-row");  
            var locationkey = obj.val();
			detailRow.find("[name='hidDetailFreightPODKey[]']").val(locationkey); 
        }  
        
        this.getGroupObj = function getGroupObj(obj){
            return obj.closest(".destination-row");
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
                
            }else if(containerType == varConstant.EMKL.container.lclnc ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                tabObj.find(".truckingfcl, .lcl-only").hide(); 
                tabObj.find(".lclnc").show(); 
                
            }else if(containerType == varConstant.EMKL.container.fcl || 
					 containerType == varConstant.EMKL.container.trucking){
                 
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
            
            tabObj.find("[name=chkIsMaster]").change();
        }
        
        this.rebindPODPOL = function rebindPODPOL(){
                    
            var selObj = tabObj.find("[name=selAirSea]");
            

            var criteria = '';
//
//            
//            if (selObj.val() == varConstant.EMKL.shipping.sea){
//                
//                criteria = "&issea=1";
//
//            }else if(selObj.val() == varConstant.EMKL.shipping.air){ 
//                criteria = "&isair=1";
//
//            }else if(selObj.val() == varConstant.EMKL.shipping.land){
//                criteria = "&island=1";
//
//            }
                
            bindAutoCompleteForTransactionDetail('detailPODName[]',objAndValuePODAutoComplete,'ajax-port.php?action=searchData&limit=25'+criteria, thisObj.updatePODDetail);      
            bindAutoCompleteForTransactionDetail('detailPOLName[]',objAndValuePOLAutoComplete,'ajax-port.php?action=searchData&limit=25'+criteria, thisObj.updatePOLDetail);      
            bindAutoCompleteForTransactionDetail('unitOriginDetailName[]',objAndValueForDetailUnitOriginAutoComplete,'ajax-item-unit.php?action=searchData'+criteria);    
            bindAutoCompleteForTransactionDetail('unitFreightDetailName[]',objAndValueForDetailUnitCarrierAutoComplete,'ajax-item-unit.php?action=searchData'+criteria);    
            bindAutoCompleteForTransactionDetail('unitItemDetailName[]',objAndValueForDetailUnitItemAutoComplete,'ajax-item-unit.php?action=searchData'+criteria);    
  
        }
        

        
       this.updateSelectZone = function updateSelectZone(typekey){
		 
		   
           var hidDetailKey = '';
           var detailPOLName = '';
           var detailPODName = '';
           var selZoneField = '';
		   
			switch(typekey){
			  case 1 : 
					hidDetailKey = 'hidDetailOriginKey';
					detailPOLName = 'pickupDetailName';
					detailPODName = 'pickupZoneDetailName';
					selZoneField = 'selOriginZone';

					break;
				case 3: 
					hidDetailKey = 'hidDetailFreightKey';
					detailPOLName = 'detailPOLName';
					detailPODName = 'detailPODName';
					selZoneField = 'selFreightZone';

					break;

				case 2: 
					hidDetailKey = 'hidDetailDestinationKey';
					detailPOLName = 'pickupLocationDetailName';
					detailPODName = 'zoneLocationDetailName';
					selZoneField = 'selDestinationZone';

					break;
			  }


            var temp = 0;
            var newOptions = [];

            var detailKey = tabObj.find("[name='"+hidDetailKey+"[]']");
		    
		   
		   if(detailKey.length > 0){
					detailKey.each(function() {  
					   var row = $(this).closest('.destination-row');    


						if(row.hasClass("row-template")) return;
						 
						var pkey = $(this).val();

						polname = row.find('[name=\"'+detailPOLName+'[]\"]').val();
						podname = row.find('[name=\"'+detailPODName+'[]\"]').val();
						

						   if(pkey == 0) pkey = ++temp;   
							row.attr('reldetailkey', pkey);

						polpodname = opt.noLocation;

						if(polname !== "" && podname !== "") polpodname = polname+" - "+podname; 
						newOptions.push({'pkey' : pkey, 'name' : polpodname});  
					}) 

					newOptions = (newOptions[0].pkey == 0) ?  newOptions.push({'pkey' : 0, 'name' : opt.noLocation}) :  newOptions;

					var selZone = tabObj.find("[name=\""+selZoneField+"\"]");

					reInsertSelectBox(selZone,newOptions, {"key" : "pkey", "label" : "name"} ); 

					selZone.val(newOptions[0].pkey).change();
		 	} 
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
                                }
                        }  
                    });
    }
	
		//this.reorderList = function reorderList(arrObj){
		//		// per scope panel
//
		//		if (typeof obj === "undefined")
		//			arrObj = tabObj.find(".row-panel");
//
		//		$.each(arrObj, function( index, value ) {
		//		  	var i = 1;
		//			var obj = $(this);	
		//			
		//			obj.closest(".row-panel"); 
		//			obj.find(".arrow-nav").removeClass("disabled");
//
		//			obj.find(".transaction-detail-row").first().find(".arrow-nav").first().addClass("disabled");   
		//			obj.find(".transaction-detail-row").last().find(".arrow-nav").last().addClass("disabled");   
//
		//			// pake class karena setiap section nama DOM nya berbeda
		//			obj.find(".hid-order-list").each(function( index ) {
		//				$(this).val(i++);
		//			});
		//		});
 //
		//}
		//
  //  	this.updateOrder = function updateOrder(obj){
		//	var orderObj = obj.closest("div").find(".hid-order-list");
		//	var row = obj.closest(".transaction-detail-row");
		//	var panel = obj.closest(".row-panel");
		//	var rowBefore = row.prev();
		//	var rowAfter = row.next();
//
		//	var currOrder = row.find(".hid-order-list").val();
		//	var totalDetail = panel.find(".hid-order-list").length - 1;
//
		//	if(obj.attr("rel") < 0){ 
		//		if(currOrder == 1) return;
		//		row.insertBefore(rowBefore); 
		//	}else{ 
		//		if(currOrder == totalDetail) return;
		//		row.insertAfter(rowAfter);  
		//	}
 //
		//	thisObj.reorderList([panel]);
		//}

         
        this.rebindEl = function rebindEl(){   
      
             
            var chkListReim = tabObj.find(".chk-list-reciept");
     	    bindEl(chkListReim,'click',function(){ thisObj.disabledTax($(this)); });
                        
        
            tabObj.find(".chk-list-reciept:checked").each(function() {  
                 var obj = $(this).closest('.transaction-detail-row');
                 obj.find(".input-selling").attr('readonly', true);
                 obj.find(".input-cost").attr('readonly', true);
                 obj.find(".chklist-include-selling").attr('readonly', true);
                 obj.find(".chklist-include-cost").attr('readonly', true);
             });
            
             var hidLocationZoneDetailKey = tabObj.find("[name='hidLocationZoneDetailKey[]']");
     	    bindEl(hidLocationZoneDetailKey,'change',function(){ thisObj.updateLocationZone($(this)); });
            
            var hidLocationPickupDetailKey = tabObj.find("[name='hidLocationPickupDetailKey[]']");
     	    bindEl(hidLocationPickupDetailKey,'change',function(){ thisObj.updateLocationPickup($(this)); });
            
             var hidPickupDetailKey = tabObj.find("[name='hidPickupDetailKey[]']");
     	    bindEl(hidPickupDetailKey,'change',function(){ thisObj.updateZone($(this)); });
            
            var hidPickupZoneDetailKey = tabObj.find("[name='hidPickupZoneDetailKey[]']");
     	    bindEl(hidPickupZoneDetailKey,'change',function(){ thisObj.updatePickup($(this)); });
            
            var hidDetailPOLKey = tabObj.find("[name='hidDetailPOLKey[]']");
     	    bindEl(hidDetailPOLKey,'change',function(){ thisObj.updatePOL($(this)); });
            
            var hidDetailPODKey = tabObj.find("[name='hidDetailPODKey[]']");
     	    bindEl(hidDetailPODKey,'change',function(){ thisObj.updatePOD($(this)); });
 
            
            bindAutoCompleteForTransactionDetail('pickupZoneDetailName[]',objAndValuePickupZoneAutoComplete,'ajax-location.php?action=searchData', thisObj.updatePickupZoneDetail);      
            bindAutoCompleteForTransactionDetail('pickupDetailName[]',objAndValuePickupAutoComplete,'ajax-location.php?action=searchData', thisObj.updatePickupDetail);      
                
            bindAutoCompleteForTransactionDetail('zoneLocationDetailName[]',objAndValueLocationZoneAutoComplete,'ajax-location.php?action=searchData', thisObj.updateLocationZoneDetail);      
            bindAutoCompleteForTransactionDetail('pickupLocationDetailName[]',objAndValueLocationPickupAutoComplete,'ajax-location.php?action=searchData', thisObj.updateLocationPickupDetail);      
            
            bindAutoCompleteForTransactionDetail('carrierDetailName[]',objAndValueCarrierAutoComplete,'ajax-supplier.php?action=searchData');    
            bindAutoCompleteForTransactionDetail('serviceDestinationName[]',objAndValueForDetailServiceAutoComplete,'ajax-item.php?action=searchData&itemtype=3',thisObj.updateChecklistDest);         
            bindAutoCompleteForTransactionDetail('serviceOriginName[]',objAndValueForDetailOriginAutoComplete,'ajax-item.php?action=searchData&itemtype=3', thisObj.updateChecklistOrigin);         
            bindAutoCompleteForTransactionDetail('serviceFreightName[]',objAndValueForDetailFreightAutoComplete,'ajax-item.php?action=searchData&itemtype=3', thisObj.updateChecklistFreight);    
     
                       
//            bindAutoCompleteForTransactionDetail('commodityName[]',objAndValueForDetailCommodityAutoComplete,'ajax-commodity.php?action=searchData');    
     
            thisObj.rebindPODPOL();
			
			
			//bindEl(tabObj.find(".arrow-nav"),'click',function() { thisObj.updateOrder($(this)); } );
            //bindEl(tabObj.find(".arrow-nav"),'click',function() { updateOrder($(this)); } );
			//
			//$(tabObj.find(".arrow-nav")).unbind('hover').hover(
			  //function() {
				 //$(this).closest(".transaction-detail-row").addClass("highlight");
			  //}, function() {
				 //$(this).closest(".transaction-detail-row").removeClass("highlight");
			  //}
			//);


     		//thisObj.reorderList();
            //reorderList(tabObj.find(".row-panel"));
        }

     
        this.loadOnReady = function loadOnReady(){  
			 
            destinationRows = tabObj.find(".destination-row");
 
           	destinationRows.each(function() {  
                            
                  var itemrow = $(this).find(".detail-item .transaction-detail-row");  
                  if(itemrow.length == 0)  addNewTemplateRow('item-row-template',null,$(this),thisObj.rebindEl);  
                  if(itemrow.length == 0)  addNewTemplateRow('origin-row-template',null,$(this),thisObj.rebindEl);  
                  if(itemrow.length == 0)  addNewTemplateRow('service-row-template',null,$(this),thisObj.rebindEl);   
            }) 


         
              thisObj.updateAirOrSea(true);
        
            tabObj.find("[name=selAirSea]").change(function() {thisObj.updateAirOrSea(); thisObj.updateItemInformation(); thisObj.rebindPODPOL();thisObj.updateContainer() });
//            if(tabObj.find("[name=hidId]").val() == 0)            
                tabObj.find("[name=selContainerType]").change(); 
           
            if(tabObj.find("[name=hidId]").val() == 0)            
                tabObj.find("[name=selAirSea]").change();
                                                    
//            tabObj.find("[name=selTypeOfJob]").change(function() { thisObj.updateTermsAndCondition(); }); 
            
            if(tabObj.find("[name=hidId]").val() == 0){
                tabObj.find("[name=selTypeOfJob]").change();

            }

            
            tabObj.find( " .section-panel .title" ).click(function() {  
                $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
            });
                        
            
            if (!data['detailFreight'] || data['detailFreight'].length == 0)
                    addNewTemplateRow("location-freight-row-template",null,null,thisObj.rebindEl);

            if (!data['detailDestination'] || data['detailDestination'].length == 0)
                addNewTemplateRow("location-destination-row-template",null,null,thisObj.rebindEl);
        
            if (!data['detailOrigin'] || data['detailOrigin'].length == 0){
                addNewTemplateRow("location-row-template",null,null,thisObj.rebindEl);
            }

            thisObj.updateSelectZone(1);
            thisObj.updateSelectZone(2);
            thisObj.updateSelectZone(3);

            tabObj.find(".select-location").change(function() { thisObj.showDetailLocation($(this)); }); 
            tabObj.find(".select-location").change();

            tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });


            if (!data['commodityDetail'] || data['commodityDetail'].length == 0)
                addNewTemplateRow("commodity-row-template",null,null,thisObj.rebindEl);            
          
            if (!data['termsDetail'] || data['termsDetail'].length == 0){
                 addNewTemplateRow("termsandcondition-row-template",null,null,thisObj.rebindEl); 
            }


            tabObj.find(".chklist-container").click(function() {   
                thisObj.updateChklistContainer($(this));
            });         
            
            tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 
            
            tabObj.find("[name=btnAddOriginRows]").click(function() {   
                 var objClosest =  $(this).closest('.table-container');
                                                   
                 if(!objClosest.find('.destination-row').hasClass("row-template"))
                	objClosest.find('.destination-row').not(':first-child').hide();


                thisObj.updateSelectZone(1);
                newrow = addNewTemplateRow("location-row-template",null,null,thisObj.rebindEl); 
                var test1= objClosest.find(".freight-row-detail");
                var test = objClosest.find(".freight-row-detail").hasClass("transaction-detail-row");
                
                objClosest.find('.destination-row').each(function() {  
                            
                  var itemrow = $(this).find(".detail-item .transaction-detail-row");  
                  if(itemrow.length == 0)  addNewTemplateRow('origin-row-template',null,$(this),thisObj.rebindEl);  
                     
                });
              

            });
            
            tabObj.find("[name=btnAddFreightRows]").click(function() {  
                var objClosest =  $(this).closest('.table-container');
                
//                                if( objClosest.find('.destination-row').hasClass("row-template")) return;
                  if(!objClosest.find('.destination-row').hasClass("row-template"))
                  objClosest.find('.destination-row').not(':first-child').hide();
                
                thisObj.updateSelectZone(3);
                addNewTemplateRow("location-freight-row-template",null,null,thisObj.rebindEl); 
                
                 objClosest.find('.destination-row').each(function() {  
                            
                  var itemrow = $(this).find(".detail-item .transaction-detail-row");  
                  if(itemrow.length == 0)  addNewTemplateRow('item-row-template',null,$(this),thisObj.rebindEl);  
                    
                
               
                });
              
            });
            
            
            tabObj.find("[name=btnAddDestinationRows]").click(function() {  
                var objClosest =  $(this).closest('.table-container');
                
                  if(!objClosest.find('.destination-row').hasClass("row-template"))
                        objClosest.find('.destination-row').not(':first-child').hide();
                
                thisObj.updateSelectZone(2);
                newrow = addNewTemplateRow("location-destination-row-template",null,null,thisObj.rebindEl);  
                 objClosest.find('.destination-row').each(function() {  
                            
                  var itemrow = $(this).find(".detail-item .transaction-detail-row");  
                  if(itemrow.length == 0)  addNewTemplateRow('service-row-template',null,$(this),thisObj.rebindEl);  
                    
                
               
                });
            
            });
            
            tabObj.find(".chklist-container:checked").each(function() {  
                 var objContainer = $(this).closest('.table-container');
                 objContainer.find("div[relheaderkey="+$(this).attr('relcontainerkey')+"]").show();
             });

            tabObj.find("[name='hidLocationZoneDetailKey[]']").change();
            tabObj.find("[name='hidLocationPickupDetailKey[]']").change();         
            tabObj.find("[name='hidPickupDetailKey[]']").change();
            tabObj.find("[name='hidPickupZoneDetailKey[]']").change();
            tabObj.find("[name='hidDetailPOLKey[]']").change();
            tabObj.find("[name='hidDetailPODKey[]']").change();
            tabObj.find("[name=selCurrency]").change(function() {  thisObj.onChangeCurrency();});

                if(id){       
					if($("."+fileUploaderTarget).length > 0) createFileUploader(fileUploaderTarget,fileFolder, id , arrFile,true);  
				}else{ 
					if($("."+fileUploaderTarget).length > 0)  createFileUploader(fileUploaderTarget,fileFolder, "" , "",true); 
				}

			tabObj.find(".file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(fileUploaderTarget); }});
			tabObj.find(".file-list"  ).disableSelection();  
			tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);

            if (!data['volumeDetail'] || data['volumeDetail'].length == 0) {
                addNewTemplateRow("volume-row-template",null,null,thisObj.rebindEl);
            }

            tabObj.find(".quota-target-detail-button").click(function () {
                var objName = $(this).attr("relobj"); 
                //$("#" + tabID + " ." + objName).toggle();
                tabObj.find("."+objName).toggle();
                var temp = $(this).attr("relalt");   
                $(this).attr("relalt",$(this).text());
                $(this).text(temp);
            });  

            thisObj.updateJobType();
			//thisObj.reorderList(); 
            thisObj.rebindEl();
        }
}
