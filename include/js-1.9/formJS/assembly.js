 function Assembly(tabID,varConstant) {
        var thisObj = this;
		var tabObj = $("#" + tabID);
     
        this.tablekey = varConstant.TABLEKEY;  

		this.tabID = tabID;	
	         var id = tabObj.find("[name=hidId]").val();  

		var objAndValue = new Array;
		objAndValue.push({object:'hidItemDetailKey[]', value :'pkey'});   
		var objAndValueForDetailAutoComplete = objAndValue;

        this.updateItemInformation = function updateItemInformation(){
              $.ajax({
                type: "GET",
                url:  'ajax-item.php', 
                data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidItemKey]" ).val(),  
                success: function(data){ 
                    var data = JSON.parse(data);  
					if(data.length == 0) return;
					
                    tabObj.find(".baseunitname").html(data[0].baseunitname);  
                }
              });
			
			 // update available BOM 
			
			  $.ajax({
                type: "GET",
                url:  'ajax-bom.php', 
				asyc: false,
                beforeSend:function (xhr){
                    clearAllRows(tabObj.find(".mnv-transaction")); 
                },
                data: "action=searchData&itemkey=" +  tabObj.find("[name=hidItemKey]" ).val(),  
                success: function(data){ 
					//console.log(data);
					var selectOpt = JSON.parse(data);  

					var selBOM = tabObj.find("[name=\"selBOM\"]");
					reInsertSelectBox(selBOM,selectOpt, {"key" : "pkey", "label" : "value"} ); 
					selBOM.val(tabObj.find("[name=\"selBOM\"] option:first").val()).change();
					thisObj.importBOM();
                }
              })
			
			 
        }
        
        this.importBOM =  function importBOM(){  
                
			if (!tabObj.find("[name=selBOM]").val())  return;
			 
            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;
            
            $.ajax({
                type: "GET",
                url:  'ajax-bom.php',
                async : false,
                beforeSend:function (xhr){
                    clearAllRows(tabObj.find(".mnv-transaction"));
                    thisObj.activeAjaxConnections++; 
                },
                data: "action=getBOMDetail&pkey=" +  tabObj.find(" [name=selBOM]" ).val(),  
                success: function(data){ 
                        var data = JSON.parse(data);  
                        var i; 

                        for(i=0;i<data.length;i++){   
                            
                                var arrPostValue = []; 
                                arrPostValue.push({"selector":"hidItemDetailKey", "value":data[i].itemkey});
                                arrPostValue.push({"selector":"itemNameDetail", "value":data[i].itemname}); 
                                arrPostValue.push({"selector":"qtyDetail", "value":data[i].qty}); 
                                arrPostValue.push({"selector":"qtyUsed", "value":data[i].qty}); 
                                
                                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue)); 
                                $newRow.find(".baseitemBOMunit").first().html(data[i].baseunitname);
                                $newRow.find(".baseitemusedunit").first().html(data[i].baseunitname);

                                bindAutoCompleteForTransactionDetail('itemNameDetail[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData'); 

                        }

                         // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                         tabObj.find(".inputnumber").change().blur();
						 decreaseActiveAjaxConnections(thisObj); 

                } ,
                error: function(xhr, errDesc, exception) {
                    decreaseActiveAjaxConnections(thisObj);  
                }
            }); 
        }
        
         this.calculateQtyUsed =  function calculateQtyUsed(){  
            var qty =  unformatCurrency(tabObj.find("[name=qty]").val());
            var qtyBom = 0;
              
            tabObj.find("[name='hidItemDetailKey[]']").each(function() {    
                qtyBom  =  parseFloat(unformatCurrency($(this).closest(".transaction-detail-row").find("[name=\"qtyDetail[]\"]").val())) || 0;  
                $(this).closest(".transaction-detail-row").find("[name=\"qtyUsed[]\"]").val(qtyBom * qty).blur();
            })  
         }
		 
		this.rebindEl = function rebindEl() { 
            bindAutoCompleteForTransactionDetail('itemNameDetail[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData');

		}
		
		this.loadOnReady = function loadOnReady() { 
			
			tabObj.find(" [name=btnImport]" ).on('click', function() {  thisObj.importBOM();  });  
			tabObj.find(" [name=qty]" ).on('change', function() {  thisObj.calculateQtyUsed();  });  
			thisObj.rebindEl();

    	}
		 
    } 
