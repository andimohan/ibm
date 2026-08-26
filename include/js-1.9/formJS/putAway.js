function PutAway(tabID){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

    var objAndValue = new Array;
	objAndValue.push({object:'hidPalletDetailKey[]', value :'pkey'});  
    var objAndValueForPalletDetailAutoComplete = objAndValue; 

    this.importData = function importData(typekey)
    {

        // loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        var refkey = tabObj.find("[name=hidRefKey]").val() || 0;

         if (!refkey) {
                alert('No. Pengajuan harus diisi.');
                return;
            }

        var action = ""; 
        thisObj.updateItemReceivingData();
        if (typekey == 1) {
            
            var submissionNumber = tabObj.find("[name=submissionNumber]").val() || "";
            action = "action=getDataForZoneTransfer&pkey=" + refkey;
        } else if (typekey == 2) {
            var warehouselayoutoriginkey = tabObj.find("[name=hidWarehouseLayoutOriginKey]").val();

            if(!warehouselayoutoriginkey) {
                alert('Tata Letak Gudang Asal harus diisi.');
                return;
            }

            action = "action=getDataForZoneTransfer&pkey=" + refkey + "&warehouselayoutoriginkey=" + warehouselayoutoriginkey;
        } else {
            var warehouselayoutkey = tabObj.find("[name=hidWarehouseLayoutKey]").val();

            if(!warehouselayoutkey) {
                alert('Tata Letak Gudang harus diisi.');
                return;
            }

            action = "action=getDataForZoneTransfer&pkey=" + refkey + "&warehouselayoutoriginkey=" + warehouselayoutkey;
        }

        if(!refkey) return;

        $.ajax({
            type: "GET",
            url:  'ajax-item-receiving.php',
            beforeSend:function (xhr){ 
                clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++; 
            },
            data: action,
            success: function(data){ 
    
                if (!data) {    
                        addNewTemplateRow("detail-row-template",'','',thisObj.rebindEl);  
                        return;
                    }

                    var data = parseJSON(data);

                    var i;
                    for(i=0;i<data.length;i++){      
                        
                        var arrPostValue = []; 
                        
                        arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                        arrPostValue.push({ "selector": "itemName", "value": data[i].itemname });
                        
                        if (typekey == 1) {
                            arrPostValue.push({ "selector": "hidItemReceivingHeaderKey", "value": data[i].refkey2 });
                            arrPostValue.push({ "selector": "hidItemReceivingDetailKey", "value": data[i].refdetailkey });
                            arrPostValue.push({ "selector": "containerNumber", "value": data[i].containernumber });
                            arrPostValue.push({ "selector": "receivingQty", "value": data[i].qtyinbaseunit });
                            arrPostValue.push({ "selector": "putAwayQty", "value": data[i].putawayqty }); 
                            arrPostValue.push({ "selector": "zoneDetailName", "value": data[i].warehouselayoutname}); 
                            arrPostValue.push({ "selector": "hidZoneDetailKey", "value": data[i].warehouselayoutkey }); 
                        } else if (typekey == 2) {
                            arrPostValue.push({ "selector": "hidItemReceivingHeaderKey", "value": data[i].refkey2 });
                            arrPostValue.push({ "selector": "qty", "value": data[i].qtyinbaseunit });
                        } else {
                            arrPostValue.push({ "selector": "hidItemReceivingHeaderKey", "value": data[i].refkey2 });
                            arrPostValue.push({ "selector": "qty", "value": data[i].qtyinbaseunit });
                        }
                            
                        
                        addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));  
                    }

                   thisObj.rebindEl(); 

                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

                decreaseActiveAjaxConnections(thisObj); 
            } ,
             error: function(xhr, errDesc, exception) {
                 decreaseActiveAjaxConnections(thisObj); 
            }
        }); 
        
    }

    this.updatePalletDetail = function updatePalletDetail(){
        var palletkey = tabObj.find("[name=hidPalletKey]").val();
        var palletName = tabObj.find("[name=palletName]").val();
        
        tabObj.find("[name='hidPalletDetailKey[]']").val(palletkey);
        tabObj.find("[name='palletDetailName[]']").val(palletName);
        
    }

    this.updateItemReceivingData = function updateItemReceivingData()
    { 
        var refkey = tabObj.find("[name=hidRefKey]").val() || 0; 
        var submissionNumber = tabObj.find("[name=submissionNumber]").val() || "";

        $.ajax({    
            type: "GET",
            url:  'ajax-item-receiving.php', 
            data: "action=getDataRowById&pkey=" +  refkey,  
            success: function(data){ 
                var data = JSON.parse(data);
				if(data.length == 0) return;
                data = data[0];
				tabObj.find("[name=submissionNumber]").val(data.submissionnumber) 
				tabObj.find("[name=warehouseLayoutOriginName]").val(data.warehouselayoutname) 
                tabObj.find("[name=hidWarehouseLayoutOriginKey]").val(data.warehouselayoutkey) 
                tabObj.find("[name=selWarehouseKey]").val(data.warehousekey).change(); 
                // tabObj.find(".baseunitname").html(data[0].baseunitname);  
            }
        });
    }

      
    this.rebindEl = function rebindEl() {   
        
        bindAutoCompleteForTransactionDetail('palletDetailName[]',objAndValueForPalletDetailAutoComplete,'ajax-pallet.php?action=searchData');
    } 
     
    this.loadOnReady = function loadOnReady(){ 
    
        thisObj.rebindEl(); 

    }
    
}
