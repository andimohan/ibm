function ItemIn(tabID,opt){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'COGS[]', value :'cogs'});  
		objAndValue.push({object:'hidNeedSN[]', value :'needsn'});  
        var objAndValueForDetailAutoComplete = objAndValue;
        
        var objAndValue = new Array;
		objAndValue.push({object:'hidVendorPartNumberKey[]', value :'pkey'});  
		objAndValue.push({object:'itemName[]', value :'itemname'});    
		objAndValue.push({object:'hidItemKey[]', value :'itemkey'});    
		objAndValue.push({object:'hidTempItemKey[]', value :'itemkey'});  
		objAndValue.push({object:'hidNeedSN[]', value :'needsn'});
        objAndValue.push({object:'COGS[]', value :'cogs'});
        var objAndValueForVendorDetailAutoComplete = objAndValue;
	  	 	  
        this.tabID = tabID;    
        this.tablekey = opt.TABLEKEY;
        this.snRegex = opt.SN_REGEX;     
     
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
                calculateSNNeeded(tabObj,target);
            
            if (detailRow.find("[name=\"vendorPartNumber[]\"]").is(":visible")) { 
                    var tempItemKey =  detailRow.find("[name=\"hidTempItemKey[]\"]").first().val();
                    var itemKey =  detailRow.find("[name=\"hidItemKey[]\"]").first().val();  
                    if (tempItemKey != itemKey){ 
                        detailRow.find("[name=\"vendorPartNumber[]\"]").first().val('');  
                        detailRow.find("[name=\"hidVendorPartNumberKey[]\"]").first().val('');   
                    }
           }
          
           updateSNOptions(tabObj,detailRow);
        }  
        
        this.updateItem = function updateItem (target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row");  
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();
            
                for(i=0;i<objAndValue.length;i++)    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])); //.change().blur();  
              
                detailRow.find(".inputnumber").change().blur(); 
            
                updateSNOptions(tabObj,detailRow);

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"vendorPartNumber[]\"]").first().val(ui.item['value']);  
                updateAvailableUnit(itemKeyObj, selUnitObj); 
              
        }
    
        
        this.updateWOItemDetail = function updateWOItemDetail(){ 

            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;
 
            var sokey = tabObj.find("[name=refkey]").val(); 

            var ajaxData = "action=getDetailById&pkey=" + sokey;

            $.ajax({
                type: "GET",
                url:  'ajax-sales-order-rental-work-order.php',
                beforeSend:function (xhr){ 
                    // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                    //clearAllRows(tabObj.find(".mnv-transaction"));
                    clearAllRows($("#defaultForm-"+tabID));
                    thisObj.activeAjaxConnections++; 
                },
                data: ajaxData,
                success: function(data){ 
                        var data = JSON.parse(data);  
                        var i;
                        for(i=0;i<data.length;i++){  

                                var arrPostValue = []; 
                                arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                                arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                                arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                                arrPostValue.push({"selector":"selUnit", "value":data[i].unitkey});  
                                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));

                        } 

                    thisObj.rebindEl(); 
                    tabObj.find(".inputnumber").change().blur();
                    decreaseActiveAjaxConnections(thisObj); 

                } ,
                 error: function(xhr, errDesc, exception) {
                     decreaseActiveAjaxConnections(thisObj); 
                }
            }); 
        } 
          
       this.updateWorkInformation =  function updateWorkInformation(){
            $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-rental-work-order.php',
                    async: false, 
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=refkey]" ).val() ,  
                    success: function(data){ 
                        if (!data) return;
                        
                        data = JSON.parse(data); 
                         
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
  
                        data = data[0]; 
                        
                        tabObj.find("[name=customerName]" ).val(data.customername); 
                        tabObj.find("[name=hidCustomerKey]" ).val(data.customerkey); 

                    } 
                });
        }
        
        this.rebindEl = function rebindEl(){  
            bindEl(tabObj.find("[name='qty[]'] "),'change', function() { calculateSNNeeded(tabObj,this); });   
            bindEl(tabObj.find(".btn-sn-options"),'click', function() {  SNOptHander(tabObj, this, thisObj.snRegex); mnvOptionsRowOnClick($(this)); });    
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('vendorPartNumber[]',objAndValueForVendorDetailAutoComplete ,'ajax-item.php?action=searchVendorPartNumber&limit=25',thisObj.updateItem);
         }
        
       
        
        this.loadOnReady = function loadOnReady(){  
            tabObj.find("[name=chkIsFullReceive]").on('change', function() { updateSNOptions(tabObj); }); 
            tabObj.find(".transaction-detail-row").each(function(){ calculateSNNeeded(tabObj, $(this));  })
			            
			tabObj.find("[name=selTransactionType]" ).change(function(){
				var value = parseInt($(this).val());
				if(value < 999)
					$(".coa-link").hide();
				else
					$(".coa-link").show(); 
			})
			tabObj.find("[name=selTransactionType]" ).change(); 
			
            thisObj.rebindEl();
        }
}
