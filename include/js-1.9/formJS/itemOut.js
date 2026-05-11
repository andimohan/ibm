function ItemOut(tabID,tablekey){   
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
        this.tablekey = tablekey;  
     

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
     
            var fulldelivery =  tabObj.find("[name=chkIsFullDelivered]").val();
            
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
          
         
        this.updateOptions = function updateOptions(obj){
              
            var row = $(obj).closest(".transaction-detail-row");  
            var formPanel = row.find(".form-panel");
    
            if (formPanel.is(":visible")) {
                var hasValue = false;
                $varSN = row.find("[name='snList[]']").val();
                var list = $varSN.split(/[\n, ]+/); 

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
                          
                if(hasValue) 
                    row.find(".form-panel-result").html($varSN).show();
                else
                    row.find(".form-panel-result").html("").hide(); 
            }
            
            
            thisObj.calculateSNNeeded(row);
              
        }
        
        this.rebindEl = function rebindEl(){  
            bindEl(tabObj.find("[name='qty[]'] "),'change', function() { thisObj.calculateSNNeeded(this); });  
            bindEl(tabObj.find(".btn-more-options"),'click', function() { thisObj.updateOptions(this); mnvOptionsRowOnClick($(this)); });   
            
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('vendorPartNumber[]',objAndValueForVendorDetailAutoComplete ,'ajax-item.php?action=searchVendorPartNumber&limit=25',thisObj.updateItem);
         }
        
       
        
        this.loadOnReady = function loadOnReady(){
             
            tabObj.find("[name=chkIsFullDelivered]").on('change', function() { thisObj.updateSNOptions(); });
            //tabObj.find("[name=chkIsInternal]" ).on('change',function(){thisObj.showInternal(this)}); 
            tabObj.find("[name=chkIsInternal]" ).change(function(){thisObj.showInternal(this)})  
            tabObj.find("[name=selTransactionType]" ).change(function(){
				var value = parseInt($(this).val());
				if(value < 999)
					$(".coa-link").hide();
				else
					$(".coa-link").show(); 
			})  
			tabObj.find("[name=selTransactionType]" ).change(); 
            tabObj.find(".transaction-detail-row").each(function(){ thisObj.calculateSNNeeded($(this));  }); 

            thisObj.rebindEl();
        }
}