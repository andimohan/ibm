function GoodsOut(tabID)
{
    var thisObj = this;
    var tabObj = $("#" + tabID);    
    
    this.tabID = tabID; 

    this.importData = function importData()
    {
        thisObj.activeAjaxConnections = 0;
        
        var pkey = tabObj.find("[name=hidItemReceivingKey]").val();

		if (!pkey) {
			alert("Nomor penerimaan harus diisi!");
			return;
		}

        $.ajax({
	            type: "GET",
	            url:  'ajax-item-receiving.php',
	            beforeSend:function (xhr){ 
                    // clearAllRows(tabObj.find(".mnv-transaction"));

                    thisObj.activeAjaxConnections++; 
	            }, 
	            data: 'action=getDataForGoodsOut&pkey=' + pkey, 
	            success: function(data){ 
                        
                        var data = parseJSON(data);

						if (data.length > 0) {
							var firstRow = tabObj.find("[name='hidRefReceivingDetailKey[]']").first().val();
							if (firstRow == "") {
								clearAllRows(tabObj.find(".mnv-transaction"));
							}
						}
					
	                    var i;
                        var newrow;
                    
                             
	                    for(i=0;i<data.length;i++){ 
							
							var arrPostValue = []; 
							
								// var outstanding = parseInt(data[i].qtylabeled) - parseInt(data[i].issuedqty);

	                            arrPostValue.push({"selector":"hidRefReceivingHeaderKey", "value":data[i].pkey});
								arrPostValue.push({ "selector": "hidRefReceivingDetailKey", "value": data[i].detailkey });
							 	arrPostValue.push({"selector":"selWarehouseLayoutDetail", "value":data[i].warehouselayoutkey});
	                            arrPostValue.push({"selector":"itemReceiving", "value":data[i].code});
	                            arrPostValue.push({"selector":"submissionDetailNumber", "value":data[i].submissionnumber});  
	                            arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});  
	                            arrPostValue.push({"selector":"itemCode", "value":data[i].itemcode});  
	                            arrPostValue.push({"selector":"itemName", "value":data[i].label});  
	                            arrPostValue.push({"selector":"itemQty", "value":data[i].quantity});  
								arrPostValue.push({ "selector": "issuedQty", "value": data[i].issuedqty });  
								arrPostValue.push({ "selector": "qty", "value": data[i].outstanding });  
								arrPostValue.push({ "selector": "amount", "value": data[i].amount});  
								
	                            
							newrow = addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));
							newrow.find("[name='selWarehouseLayoutDetail[]']").val(data[i].warehouselayoutkey).attr("selected", true).trigger("change");
                         
	                    }

					tabObj.find("[name=hidItemReceivingKey]").val("");
					tabObj.find("[name=itemReceivingCode]").val("");

                     thisObj.rebindEl();
                    
	                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
	                 tabObj.find(".inputnumber, .inputdecimal, .inputautodecimal").blur(); 

	                decreaseActiveAjaxConnections(thisObj); 
                    
	            } ,
	             error: function(xhr, errDesc, exception) { 
                    decreaseActiveAjaxConnections(thisObj); 
                }
	        });

    }
    
    this.rebindEl = function rebindEl() {   
    } 
         
	this.loadOnReady = function loadOnReady() {  
		tabObj.find("[name='selWarehouseLayoutDetail[]']").attr('readonly', true);
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); }); 
        thisObj.rebindEl(); 
    }
}