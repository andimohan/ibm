function ItemInReceive(tabID,rs){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
	    var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue; 
     
        this.rs = (rs.length > 0) ? rs[0] : null;
        this.tabID = tabID;    
     
        this.importData =  function importData(){ 

                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;

                $.ajax({
                    type: "GET",
                    url:  'ajax-item-in.php',
                    beforeSend:function (xhr){ 
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getRelatedInformation&pkey=" +  tabObj.find("[name=hidItemInKey]" ).val() ,  
                    success: function(data){ 

                        if (!data) return;

                        var data = JSON.parse(data);  

                        tabObj.find("[name=reference]").val(data[0].refcode);
                        tabObj.find("[name=supplierName]").val(data[0].suppliername); 

                    } ,
                     error: function(xhr, errDesc, exception) {
                        //decreaseActiveAjaxConnections(thisObj); 
                    },
                    complete:function(xhr, desc) { 
                        thisObj.updateSNOptions();
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });


                $.ajax({
                    type: "GET",
                    url:  'ajax-item-in.php',
                    beforeSend:function (xhr){
                        clearAllRows($("#"+tabID));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getOutstandingDetail&pkey=" +  tabObj.find("[name=hidItemInKey]" ).val() ,  
                    success: function(data){ 
                            if (!data) return;
                            var data = JSON.parse(data);  

                            var i; 
                            for(i=0;i<data.length;i++){    
                                
                                        var arrPostValue = []; 
                                        arrPostValue.push({"selector":"hidItemInDetailKey", "value":data[i].pkey});
                                        arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                                        arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                                        arrPostValue.push({"selector":"hidVendorPartNumberKey", "value":data[i].vendorpartnumberkey}); 
                                        arrPostValue.push({"selector":"vendorPartNumber", "value":data[i].partnumber}); 
                                        arrPostValue.push({"selector":"hidNeedSN", "value":data[i].needsn}); 
                                        arrPostValue.push({"selector":"orderedQtyInBaseUnit", "value":data[i].qtyinbaseunit}); 
                                        arrPostValue.push({"selector":"qtyMinusInBaseUnit", "value":data[i].qtyinbaseunit - data[i].receivedqtyinbaseunit}); 
                                        arrPostValue.push({"selector":"receivedQtyInBaseUnit", "value":data[i].qtyinbaseunit - data[i].receivedqtyinbaseunit}); 


                                        $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                                        $newRow.find(".baseitemunit").first().html(data[i].baseunitname); 
                            } 

                             // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                             tabObj.find(".inputnumber").blur();
                             thisObj.calculateSNNeeded(tabObj.find("[name=\"hidItemKey[]\"]"));
                             thisObj.rebindEl();
                             

                    }  ,
                    complete:function() { 
                        thisObj.updateSNOptions();
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
            }   

        this.updateItemInReceiveMinusQty = function updateItemInReceiveMinusQty(){  
 
            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0; 
    
            $.ajax({ 
                    beforeSend: function(xhr) {  thisObj.activeAjaxConnections++;   },
                    type: "GET",
                    url:  'ajax-item-in.php', 
                    data: "action=getOutstandingDetail&pkey=" +  tabObj.find("[name=hidItemInKey]" ).val(),  
                    success: function(data){  
                       
                        if (!data) return; 
                        
                        var data = JSON.parse(data);  
                        var i;
                        
                         tabObj.find("[name=\"hidItemKey[]\"]").each(function() {  
                             if (!$(this).closest(".transaction-detail-row").hasClass("detail-row-template"))
                               $(this).closest(".transaction-detail-row").addClass("will-delete");
                         }) 
                       
                        
                        for(i=0;i<data.length;i++){  
                            var row = tabObj.find("[name=\"hidItemKey[]\"][value='"+data[i].itemkey+"']").closest('.transaction-detail-row');
                            var qtyInBaseUnit = parseInt(data[i].qtyinbaseunit);
                            var receivedQtyInBaseUnit = parseInt(data[i].receivedqtyinbaseunit);
                            
                           row.find("[name=\"orderedQtyInBaseUnit[]\"]").first().val(qtyInBaseUnit); 
                           row.find("[name=\"qtyMinusInBaseUnit[]\"]").first().val(qtyInBaseUnit - receivedQtyInBaseUnit); 
                                 
                           if(qtyInBaseUnit != receivedQtyInBaseUnit)
                            row.removeClass("will-delete");
                            
                        }

                         // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                        tabObj.find(".inputnumber").change().blur();
                        tabObj.find(".will-delete").remove(); 

                    } , 
                    complete:function() { 
                        thisObj.updateSNOptions();
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                }) ;  
 
        }
        
        this.updateSNOptions = function updateSNOptions(row){
            
            //var targetObj = $(target); 
        
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
        
        this.calculateSNNeeded = function calculateSNNeeded(target){
            
            if(target)  
               detailRow =  ( target.nodeType )  ? $(target).closest(".transaction-detail-row") : target;
            else
               detailRow =  tabObj.find(".transaction-detail-row");
              
            
            detailRow.each(function(){  
                if (detailRow.find("[name='hidNeedSN[]']").val() != 1 || detailRow.find(".options-row").is(":visible") == false)
                    return;

                detailRow.find(".total-sn-label").show();
                //alert(detailRow);
                disabledButton(detailRow.find("[name=btnMoreOptions]"),false);
                detailRow.find(".options-row").show();

                totalQty = detailRow.find("[name='receivedQtyInBaseUnit[]']").val();
                totalQty = unformatCurrency(totalQty);

                totalSN = detailRow.find(".tag-list li").length; 
                remaining = totalSN-totalQty;

                $(this).find(".total-sn-remaining").html(remaining);
                $(this).find(".total-sn-label").removeClass("text-red-cardinal text-blue-munsell");
                if(remaining < 0)
                    $(this).find(".total-sn-label").addClass("text-red-cardinal");
                else if(remaining > 0)
                    $(this).find(".total-sn-label").addClass("text-blue-munsell");  

            }); 
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
            bindEl(tabObj.find("[name='receivedQtyInBaseUnit[]']"),'change', function() { thisObj.calculateSNNeeded(this); });  
            bindEl(tabObj.find(".btn-more-options"),'click', function() { thisObj.updateOptions(this); mnvOptionsRowOnClick($(this)); });   
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);
         }
        
       
        
        this.loadOnReady = function loadOnReady(){
             
            tabObj.find("[name=chkIsFullReceive]").on('change', function() { thisObj.updateSNOptions(); }); 
            tabObj.find(".transaction-detail-row").each(function(){ thisObj.calculateSNNeeded($(this));  });
             
            if (thisObj.rs && thisObj.rs.statuskey == 1)
                thisObj.updateItemInReceiveMinusQty();

            thisObj.rebindEl();
        }
}