function WarehouseTransfer(tabID,tablekey) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      

        this.tabID = tabID;    
        this.tablekey = tablekey;     
    
	    var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'});   
		objAndValue.push({object:'hidNeedSN[]', value :'needsn'});  
        var objAndValueForDetailAutoComplete = objAndValue;  
	  
	    var objAndValue = new Array;
		objAndValue.push({object:'hidItemDestKey[]', value :'pkey'});  
		objAndValue.push({object:'selDestUnit[]', value :'deftransunitkey'});    
        var objAndValueDestinationForDetailAutoComplete = objAndValue;  
	  
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
            thisObj.updateIsWeightFixed(itemKeyObj.val(), detailRow);

            if (ui.item['needsn'] == 1) 
                thisObj.calculateSNNeeded(target);
 
           thisObj.updateSNOptions(detailRow);
        }
        
        this.updateDestinationDetail = function updateDestinationDetail(target,objAndValue,ui){    
            var detailRow = $(target).closest(".transaction-detail-row");
            var itemKeyObj = detailRow.find("[name=\"hidItemDestKey[]\"]").first();
            var selUnitObj = detailRow.find("[name=\"selDestUnit[]\"]").first();
              
            for(i=0;i<objAndValue.length;i++)  
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
             
            updateAvailableUnit(itemKeyObj, selUnitObj);

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemDestName[]\"]").first().val(decodeHTMLEntities(ui.item['value'])); 
            detailRow.find(".baseitemunit").html(ui.item['baseunitname']);   
        }
        
         this.updateSNOptions = function updateSNOptions(row){
            var rowList = (row) ? row : tabObj.find(".transaction-detail-row");
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

        this.updateIsWeightFixed = function updateIsWeightFixed(itemKey, row)
        {
            
            if ($("[name='qtyInPcs[]']").length == 0) return;
            
            $.ajax({
                type: "GET",
                url: 'ajax-item.php',
                async: false,
                data: "action=getDataRowById&pkey=" + itemKey,
            }).done(function (data) {
                var data = parseJSON(data); 

                if (!data || data.length === 0) {
                    return;
                }
                
                data = data[0];
                
                var isWeightFixed = data.isweightfixed;

                if (isWeightFixed == 1) {
                    row.find("[name='qtyInPcs[]']").first().prop("readonly", true);
                    var qtyInPcs = parseFloat(data.gramasi);
                    row.find("[name='qtyInPcs[]']").first().val(qtyInPcs);
                } else {
                    row.find("[name='qtyInPcs[]']").first().prop("readonly", false);
                }

                 tabObj.find(".inputnumber, .inputdecimal").blur(); 

            });
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
            bindEl(tabObj.find("[name='qty[]']"),'change', function() { thisObj.calculateSNNeeded(this); });  
            bindEl(tabObj.find(".btn-more-options"),'click', function() { thisObj.updateOptions(this); mnvOptionsRowOnClick($(this)); });   
       
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',getTabObj().updateDetail);  
            bindAutoCompleteForTransactionDetail('itemDestName[]',objAndValueDestinationForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',getTabObj().updateDestinationDetail);
        }
         
        this.loadOnReady = function loadOnReady(){
            tabObj.find(".transaction-detail-row").each(function(){ thisObj.calculateSNNeeded($(this)); }); 

             tabObj.find("[name=btnAddDestinationRows]").on('click', function() {
                var newRow = addNewTemplateRow("destination-row-template"); // harus sesuai sama ini
                bindAutoCompleteForTransactionDetail('itemDestName[]',objAndValueDestinationForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',getTabObj().updateDestinationDetail);
             });
             
            if(tabObj.find("[name=\"hidItemDestKey[]\"]").length <= 1){ 
                var newRow = addNewTemplateRow("destination-row-template");
                bindAutoCompleteForTransactionDetail('itemDestName[]',objAndValueDestinationForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',getTabObj().updateDestinationDetail);  
            }
            
            thisObj.rebindEl();
        }
}
