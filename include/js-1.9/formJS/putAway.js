function PutAway(tabID){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

    this.importData = function importData()
    {

        // loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        var refkey = tabObj.find("[name=hidRefKey]").val() || 0; 
        thisObj.updateItemReceivingData();
        var submissionNumber = tabObj.find("[name=submissionNumber]").val() || "";
        
        if(!refkey) return;

        $.ajax({
            type: "GET",
            url:  'ajax-item-receiving.php',
            beforeSend:function (xhr){ 
                clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++; 
            },
            data: "action=getDataForPutAway&pkey=" + refkey ,
            success: function(data){ 
    
                    if(!data) {                        
                        return;
                    }

                    var data = JSON.parse(data); 
                    console.log(data);


                    var i;
                    for(i=0;i<data.length;i++){  
                        
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidItemReceivingDetailKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                            arrPostValue.push({"selector":"itemName", "value":data[i].itemname});
                            arrPostValue.push({"selector":"receivingQty", "value": data[i].qtyinbaseunit});
                            arrPostValue.push({"selector":"putAwayQty", "value": data[i].putawayqty});
                            
                            addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
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
                // tabObj.find(".baseunitname").html(data[0].baseunitname);  
            }
        });
    }

      
    this.rebindEl = function rebindEl(){   
    
        

    } 
     
    this.loadOnReady = function loadOnReady(){ 
    
        thisObj.rebindEl(); 

    }
    
}
