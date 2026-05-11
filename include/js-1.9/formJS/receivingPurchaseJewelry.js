function ReceivingPurchaseJewelry(tabID, rs,varConstant){   
        var thisObj = this;
        var tabObj = $("#" + tabID);  
        this.tablekey = varConstant.TABLEKEY;     
        this.labelWeight = varConstant.LABEL_WEIGHT ?? 0;
     
    	var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue; 
	
        var objAndValue = new Array;  
        objAndValue.push({object:'hidPackagingKey[]', value :'pkey'}); 
        var objAndValueForDetailPackagingAutoComplete = objAndValue;  
    
        var firstOpened = true;
    
        this.tabID = tabID;    
        this.rs = (rs.length > 0) ? rs[0] : null;
    
        this.importData =  function importData(){  

                // loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;

                $.ajax({
                    type: "GET",
                    url:  'ajax-purchase-order-jewelry.php',
                    beforeSend:function (xhr){
                        clearAllRows(tabObj.find('.purchase-receive-detail'));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getOutstandingDetail&pkey=" +  tabObj.find("[name=hidPurchaseOrderKey]" ).val(),  
                    success: function (data) { 
                        
                        if (!data) return;
                       
                            var data = JSON.parse(data); 
                            
                            var selItemPOObj = tabObj.find("[name='selItemPurchaseOrder[]']");  
                            var arrData = [];

                            // arrData.unshift({
                            //     pkey: 0,
                            //     value: "- - - - - -"
                            // });

                            for(var i=0; i < data.length; i++) {
                                var obj = {};
                                obj['pkey'] = data[i]['pkey'];
                                obj['value'] = decodeHTMLEntities(parseFloat(data[i].number) + ' - ' + data[i].itemname);
                                arrData.push(obj);
                            }
                            var selectOpt = arrData;
                            reInsertSelectBox(selItemPOObj, selectOpt, { "key": "pkey", "label": "value" });
                        
                            for(i=0;i<data.length;i++){   
                                var qtyInBaseUnit = parseFloat(data[i].qtyinbaseunit);
                                var receivedQtyInBaseUnit = parseFloat(data[i].receivedqtyinbaseunit);
                                var qtyInPcs = parseFloat(data[i].qtyinpcs);
                                var receivedQtyInPcs = parseFloat(data[i].receivedqtyinpcs);
 
                                var arrPostValue = []; 
                                //arrPostValue.push({"selector":"hidPODetailKey", "value":data[i].pkey});
                                // arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey}); 
                                arrPostValue.push({"selector":"orderedQtyInBaseUnit", "value":qtyInBaseUnit}); 
                                arrPostValue.push({"selector":"qtyMinusInBaseUnit", "value":qtyInBaseUnit - receivedQtyInBaseUnit}); 
                                arrPostValue.push({"selector":"receivedQtyInBaseUnit", "value":qtyInBaseUnit -receivedQtyInBaseUnit}); 
                                arrPostValue.push({"selector":"orderedQtyInPcs", "value":qtyInPcs}); 
                                arrPostValue.push({"selector":"qtyMinusInPcs", "value":qtyInPcs - receivedQtyInPcs}); 
                                arrPostValue.push({"selector": "receivedQtyInPcs", "value": qtyInPcs - receivedQtyInPcs }); 
                                
                                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                                
                                $newRow.find(".baseitemunit").first().html(data[i].unitname); 
                                
                                // === isi select box
                                var $sel = $newRow.find("[name='selItemPurchaseOrder[]']");
                                // set value ke pkey
                                $sel.val(data[i].pkey).trigger("change");
                            } 
                        

                        thisObj.rebindEl(); 
                        tabObj.find(".inputnumber, .inputdecimal").blur();
                        decreaseActiveAjaxConnections(thisObj);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
            
        this.updateItemPODetail = function updateItemPODetail(obj)
        {
            var row =  $(obj).closest(".transaction-detail-row");  
            
            var pkey = $(obj).val();
            
            $.ajax({
                type: "GET",
                url:  'ajax-purchase-order-jewelry.php',
                async : false,
                data: "action=getDetailForReceiving&pkey="+pkey,  
            }).done(function (data) { 
            
            
                var data = parseJSON(data); 

                if (!data || data.length === 0) {
                    return;
                }
                
                data = data[0];
                
                var qtyInBaseUnit = parseFloat(data.qtyinbaseunit);
                var receivedQtyInBaseUnit = parseFloat(data.receivedqtyinbaseunit);
                var qtyInPcs = parseFloat(data.qtyinpcs);
                var receivedQtyInPcs = parseFloat(data.receivedqtyinpcs);
                
                row.find("[name='orderedQtyInBaseUnit[]']").val(qtyInBaseUnit).change();
                row.find("[name='qtyMinusInBaseUnit[]']").val(qtyInBaseUnit - receivedQtyInBaseUnit).change();
                row.find("[name='receivedQtyInBaseUnit[]']").val(qtyInBaseUnit -receivedQtyInBaseUnit).change();
                row.find("[name='orderedQtyInPcs[]']").val(qtyInPcs).change();
                row.find("[name='qtyMinusInPcs[]']").val(qtyInPcs - receivedQtyInPcs).change();
                row.find("[name='receivedQtyInPcs[]']").val(qtyInPcs - receivedQtyInPcs).change();
                row.find(".baseitemunit").first().html(data.unitname);

                tabObj.find(".inputnumber, .inputdecimal").blur();  
            });
        }

        this.updateDetail = function updateDetail(target, objAndValue, ui) {
            var detailRow = $(target).closest(".transaction-detail-row"); 
            
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();

            for (i = 0; i < objAndValue.length; i++) {
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur(); 
            }

            thisObj.updateItemBaseUnit(itemKeyObj.val(), detailRow);

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 

        }
 
        this.updateItemBaseUnit = function updateItemBaseUnit(itemKey, row)
        { 
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

                row.find("[name='hidBaseUnitKey[]']").first().val(data.baseunitkey);
                row.find(".baseitemunit").first().html(data.baseunitname);

            });
        }
            
        this.calculateGrossWeight = function calculateGrossWeight(obj){  
            var beforeGrossWeight = parseFloat(unformatCurrency(obj.val())) || 0;
            var grossWeight = beforeGrossWeight + thisObj.labelWeight;
            obj.closest(".transaction-detail-row").find("[name='grossWeight[]']").val(grossWeight).blur();
        }
    
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData&itemtype=1', thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('packagingName[]',objAndValueForDetailPackagingAutoComplete,'ajax-packaging.php?action=searchData');
            
            bindEl(tabObj.find("[name='beforeGrossWeight[]']"), 'change', function () { thisObj.calculateGrossWeight($(this))});          
            
            bindEl(tabObj.find("[name='selItemPurchaseOrder[]']"), 'change', function () {
                thisObj.updateItemPODetail($(this));
            });

        }
        
        this.loadOnReady = function loadOnReady(){
             
         
            //thisObj.importData();

            tabObj.find("[name=btnAddRows]").on("click", function () {
                var newRow = tabObj.find(".transaction-detail-row").last();
                var selectObj = newRow.find("[name='selItemPurchaseOrder[]']");
                thisObj.updateItemPODetail(selectObj);
            });

            thisObj.rebindEl(); 
          
        }
}
