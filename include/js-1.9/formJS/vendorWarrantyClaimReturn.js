function VendorWarrantyClaimReturn(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'COGS[]', value :'cogs'});  
		objAndValue.push({object:'hidNeedSN[]', value :'needsn'});  
        var objAndValueForDetailAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidOldItemKey[]', value :'pkey'});   
		//objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		//objAndValue.push({object:'COGS[]', value :'cogs'});  
		objAndValue.push({object:'hidOldNeedSN[]', value :'needsn'});  
        var objAndValueForDetailAutoCompleteOldItem = objAndValue;
        
        var objAndValue = new Array;
		objAndValue.push({object:'hidVendorPartNumberKey[]', value :'pkey'});  
		objAndValue.push({object:'itemName[]', value :'itemname'});    
		objAndValue.push({object:'hidItemKey[]', value :'itemkey'});    
		objAndValue.push({object:'hidTempItemKey[]', value :'itemkey'}); 
		objAndValue.push({object:'hidNeedSN[]', value :'needsn'});
        objAndValue.push({object:'COGS[]', value :'cogs'});
        var objAndValueForVendorDetailAutoComplete = objAndValue;
	  	 	  
     
        this.tabID = tabID;   

      this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
            var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();

            disabledButton(detailRow.find("[name=btnMoreOptions]"));
            detailRow.find(".options-row").hide();


            for(i=0;i<objAndValue.length;i++)  
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
         
            updateAvailableUnit(itemKeyObj, selUnitObj);
          
            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(decodeHTMLEntities(ui.item['value'])); 
            detailRow.find(".baseitemunit").html(ui.item['baseunitname']);    

            if (ui.item['needsn'] == 1) 
                thisObj.calculateSNNeeded(target);
            
            if (detailRow.find("[name=\"vendorPartNumber[]\"]").is(":visible")) { 
                    var tempItemKey =  detailRow.find("[name=\"hidTempItemKey[]\"]").first().val();
                    var itemKey =  detailRow.find("[name=\"hidItemKey[]\"]").first().val();  
                    if (tempItemKey != itemKey){ 
                        detailRow.find("[name=\"vendorPartNumber[]\"]").first().val('');  
                        detailRow.find("[name=\"hidVendorPartNumberKey[]\"]").first().val('');   
                    }
           }
          
          
           thisObj.updateSNOptions(detailRow);
        }  
        
        this.updateItem = function updateItem (target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row");  
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();
            
                for(i=0;i<objAndValue.length;i++)    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])); //.change().blur();  
              
                detailRow.find(".inputnumber").change().blur(); 
            
                thisObj.updateSNOptions(detailRow);

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"vendorPartNumber[]\"]").first().val(ui.item['value']);  
            
                updateAvailableUnit(itemKeyObj, selUnitObj);
            
        }
        
        
        this.updateSNOptions = function updateSNOptions(row){
     
//            var fulldelivery =  tabObj.find("[name=chkIsFullDelivered]").val();
            var fulldelivery =  1;
            
            if(fulldelivery == 0){
                tabObj.find(".btn-more-options").prop("disabled",true); 
                tabObj.find(".options-row").hide(); 
            }else{ 
                
                var rowList = (row) ? row :  tabObj.find(".transaction-detail-row");
                rowList.each(function(){  
                    var useSN = $(this).find("[name=\"hidNeedSN[]\"]").val();  
                    if(useSN == 1){ 
                        $(this).find(".btn-more-options").prop("disabled",false);  
                        $(this).find(".options-row").show(); 
                        thisObj.calculateSNNeeded($(this));
                    }else{ 
                        $(this).find(".btn-more-options").prop("disabled",true);  
                        $(this).find(".options-row").hide(); 
                    }
                })
            }
            
         }
        
        
        this.showInternal = function showInternal(obj){
           tabObj.find(".internal, .external").toggle();
        }
        
        this.calculateSNNeeded = function calculateSNNeeded(target){
             
            if(target)  
               detailRow =  ( target.nodeType )  ? $(target).closest(".transaction-detail-row") : target;
            else
               detailRow =  tabObj.find(".transaction-detail-row");
             
            detailRow.each(function(){   
                    if ($(this).find("[name='hidNeedSN[]']").val() != 1 || $(this).find(".options-row").is(":visible") == false)
                        return;
 
                    $(this).find(".total-sn-label").show();
                    disabledButton($(this).find("[name=btnMoreOptions]"),false);
                    $(this).find(".options-row").show();

                    totalQty = $(this).find("[name='qty[]']").val();
                    totalQty = unformatCurrency(totalQty);

                    totalSN = $(this).find(".tag-list li").length; 
                    remaining = totalSN-totalQty;

                    $(this).find(".total-sn-remaining").html(remaining);
                    $(this).find(".total-sn-label").removeClass("text-red-cardinal text-blue-munsell");
                    if(remaining < 0)
                        $(this).find(".total-sn-label").addClass("text-red-cardinal");
                    else if(remaining > 0)
                        $(this).find(".total-sn-label").addClass("text-blue-munsell");                      
            })
 
                          
        }
        
        this.calculateDetail = function calculateDetail(obj){      
               
                    var row =  $(obj).closest(".transaction-detail-row");   
                    var itemkey =  row.find("[name='hidItemKey[]']").val();
                    var currencyRate = unformatCurrency( tabObj.find("[name='currencyRate']").val() );
               
                        
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
                    var unitkey =  row.find("[name='selUnit[]']").val();

                    //var subtotal = qty  * priceInUnit * currencyRate;
                    var subtotal = qty  * priceInUnit;
                    row.find("[name='subtotal[]']").val(subtotal).blur(); 

                    thisObj.calculateTotal();
	   }
        
        this.calculateTotal = function calculateTotal(){  
         
                    var subtotal = 0; 
                    tabObj.find("[name='subtotal[]']").each(function(){ subtotal += parseFloat(unformatCurrency($(this).val())) || 0;  })
                    tabObj.find("[name='grandtotal']").val(subtotal).blur();
		 
	   }
          
        this.updateOutstanding = function updateOutstanding(obj){
            var obj = $(obj);
            var row = obj.closest(".transaction-detail-row");  
            row.find("[name='qtyOutstanding[]']").val(obj.val()).blur();
        } 
        
        this.updateOptions = function updateOptions(obj, fromImport){
              
            var row = $(obj).closest(".transaction-detail-row");  
            var formPanel = row.find(".form-panel"); 
            
            if (formPanel.is(":visible") || fromImport) {
                var hasValue = false;
                var hasValueReplacement = false;
                $varSN = row.find("[name='snList[]']").val();
                $varSNReplacement = row.find("[name='snReplacementList[]']").val();
                var list = $varSN.split(/[\n, ]+/); 
                var listReplacement = $varSNReplacement.split(/[\n, ]+/); 

                $varSN = '';
                if (list.length > 0 ){
                    $varSN = "<ul class=\"tag-list\">";

                    for(i=0;i<list.length;i++){ 
                        if (list[i].length == 0)
                            continue;

                        $varSN += '<li>'+list[i]+'</li>';
                        hasValue = true;
                    }

                    $varSN += "<ul>";
                }
                
                $varSNReplacement = '';
                if (listReplacement.length > 0 ){
                    $varSNReplacement = "<ul class=\"tag-list-replacement\">";

                    for(i=0;i<listReplacement.length;i++){ 
                        if (listReplacement[i].length == 0)
                            continue;

                        $varSNReplacement += '<li>'+listReplacement[i]+'</li>';
                        hasValueReplacement = true;
                    }

                    $varSNReplacement += "<ul>";
                }
                          
                if(hasValue)
                    row.find(".form-panel-result-rma").html($varSN).show();
                else
                    row.find(".form-panel-result-rma").html("").hide();
                
                
                if(hasValueReplacement)
                    row.find(".form-panel-result-replacement").html($varSNReplacement).show();
                else
                    row.find(".form-panel-result-replacement").html("").hide();
 
            }
            
            
            thisObj.calculateSNNeeded(row);
              
        }
        
        this.updateClaimInformation = function updateClaimInformation(){
            
             $.ajax({
                    type: "GET",
                    url:  'ajax-vendor-warranty-claim.php', 
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidRefKey]").val(),  
                    beforeSend:function (xhr){ 
                         tabObj.find("[name=hidSupplierKey]").val('');
                         tabObj.find("[name=supplierName]").val(''); 
                         tabObj.find("[name=selCurrency]").prop('selectedIndex', 0).change();
                         tabObj.find("[name=currencyRate]").val(1).blur(); 
                    },
                    success: function(data){  
                            if(data){
                                 var data = JSON.parse(data);   
                                 tabObj.find("[name=hidSupplierKey]").val(data[0].recipientkey);
                                 tabObj.find("[name=supplierName]").val(data[0].recipientname);
                                 tabObj.find("[name=selCurrency]").val(data[0].currencykey).change();
                                 tabObj.find("[name=currencyRate]").val(data[0].rate).blur();
                            }
                    }  
                }); 
        }
        
        this.importData = function importData(){  
           
                var importButton =  tabObj.find("[name=btnImport]"); 
                    
                var refkey = tabObj.find("[name=hidRefKey]").val();
                   
                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;
 
                $.ajax({
                    type: "GET",
                    url:  'ajax-vendor-warranty-claim.php',
                    beforeSend:function (xhr){
                        importButton.prop('disabled', true) ;   
                        clearAllRows($("#defaultForm-"+tabID));
                        thisObj.activeAjaxConnections++; 
                    },
                    async: false,
                    data: "action=getDetailById&pkey=" +  tabObj.find("[name=hidRefKey]").val(),
                    success: function(data){ 
                            if(!data) return;
                                
                            var data = JSON.parse(data);  
                            var i;

                            for(i=0;i<data.length;i++){   
                                    var arrPostValue = []; 
                                
                                    arrPostValue.push({"selector":"hidClaimDetailKey", "value":data[i].pkey}); 
                                    arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey}); 
                                    arrPostValue.push({"selector":"hidOldItemKey", "value":data[i].itemkey}); 
                                    arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                                    arrPostValue.push({"selector":"oldItemName", "value":data[i].itemname}); 
                                    arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                                    arrPostValue.push({"selector":"unitkey", "value":data[i].unitkey}); 
                                    arrPostValue.push({"selector":"qtyOutstanding", "value":data[i].qtyoutstanding}); 
                                    arrPostValue.push({"selector":"hidNeedSN", "value":data[i].needsn}); 
                                    arrPostValue.push({"selector":"hidVendorPartNumberKey", "value":data[i].vendorpartnumberkey}); 
                                    arrPostValue.push({"selector":"vendorPartNumber", "value":data[i].partnumber}); 
                                    arrPostValue.push({"selector":"hidTempItemKey", "value":data[i].itemkey}); 
                                    arrPostValue.push({"selector":"priceInUnit", "value":data[i].priceinunit}); 
                                
                                    var sn = '';
                                    $.ajax({
                                        type: "GET",
                                        url:  'ajax-vendor-warranty-claim.php',
                                        async: false,
                                        data: 'action=getSNDetail&pkey=' + data[i].pkey, 
                                        success: function(data){  
                                            if(data){
                                                 var data = JSON.parse(data);    
                                                 sn = data; 
                                            }
                                        }   
                                    }); 
                                
                                    arrPostValue.push({"selector":"snList", "value":sn});
                                
                                
                                    $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));    
				                    thisObj.updateSNOptions($newRow);
                           } 
                        
                            thisObj.rebindEl(); 
                        
                            tabObj.find(".transaction-detail-row").each(function(){  thisObj.updateOptions(this,true); }); 
                            tabObj.find(".inputnumber, .inputdecimal").change().blur();  
  
                    } , 
                    complete:function() { 
                        importButton.prop('disabled', false);   
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                     
                }); 
        }
        
        this.updateRate = function updateRate(){
            tabObj.find(".transaction-detail-row").each(function(){ thisObj.calculateDetail($(this));  });  
        }
        
        this.rebindEl = function rebindEl(){   
            
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('oldItemName[]',objAndValueForDetailAutoCompleteOldItem,'ajax-item.php?action=searchData&itemtype=1');
            bindAutoCompleteForTransactionDetail('vendorPartNumber[]',objAndValueForVendorDetailAutoComplete ,'ajax-item.php?action=searchVendorPartNumber',thisObj.updateItem);
            
            bindEl(tabObj.find(".btn-more-options"),'click', function() { thisObj.updateOptions(this); mnvOptionsRowOnClick($(this)); });   
            bindEl(tabObj.find("[name='priceInUnit[]'],  [name='selUnit[]']" ), 'change',  function(){ thisObj.calculateSNNeeded(this); thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='qty[]']"), 'change',  function(){  thisObj.updateOutstanding(this); thisObj.calculateSNNeeded(this); thisObj.calculateDetail(this) }); 
	        //tabObj.find("[name=hidSupplierKey]").change(function() { thisObj.updateSupplier(); });
         }
        
       
        
        this.loadOnReady = function loadOnReady(){
            tabObj.find(".transaction-detail-row").each(function(){ thisObj.calculateSNNeeded($(this)); thisObj.calculateDetail($(this));  }); 
            tabObj.find("[name=btnImport]" ).on('click', function() { thisObj.importData(); });
            tabObj.find("[name=currencyRate]").on('change',  function() { thisObj.updateRate(); });
 
            thisObj.rebindEl();
        }
}
