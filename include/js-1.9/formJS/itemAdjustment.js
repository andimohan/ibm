function ItemAdjustment(tabID, rs,tablekey){   
        var thisObj = this;
        var tabObj = $("#" + tabID);  
        this.tablekey = tablekey;        
    
		var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
		objAndValue.push({object:'qtyBefore[]', value :'qoh'});
		objAndValue.push({object:'qtyBeforeInPcs[]', value :'qohinpcs'});   
		objAndValue.push({object:'hidIsWeightFixed[]', value :'isweightfixed'});   
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'});      
		objAndValue.push({object:'COGS[]', value :'cogs'});   
        var objAndValueForDetailAutoComplete = objAndValue;
     
        this.tabID = tabID;   
        this.rs = (rs.length > 0) ? rs[0] : null;
    
        this.importData = function importData(){  
                    loadOverlayScreen({content: _LOADING_TEMPLATE_});
                    thisObj.activeAjaxConnections = 0;

                    $.ajax({
                        type: "GET",
                        url:  'ajax-item.php',
                        beforeSend:function (xhr){
                            clearAllRows($("#defaultForm-"+tabID));
                            thisObj.activeAjaxConnections++; 
                        }, 
                        data: "action=searchData&categorykey=" +  tabObj.find("[name=selCategoryKey]" ).val() + "&trdate="+ tabObj.find("[name=trDate]" ).val()+"&getQOH=1&itemtype=1&warehousekey=" + tabObj.find("[name=selWarehouseKey]" ).val(),  
                        success: function(data){ 

                                data = JSON.parse(data);  
                                var i;
                            
				                var hasQtyInPcs = (tabObj.find("[name='qtyBeforeInPcs[]']").length > 0) ? true : false;
                                for(i=0;i<data.length;i++){ 
									
                                    var arrPostValue = []; 
                                    arrPostValue.push({"selector":"hidItemKey", "value":data[i].pkey});
                                    arrPostValue.push({"selector":"itemName", "value":data[i].value}); 
                                    arrPostValue.push({"selector":"qtyBefore", "value":data[i].qoh}); 

                                    if (hasQtyInPcs) {
                                        arrPostValue.push({ "selector": "qtyBeforeInPcs", "value": data[i].qohinpcs });
                                         arrPostValue.push({ "selector": "hidGramasi", "value": data[i].gramasi });
                                         arrPostValue.push({ "selector": "hidIsWeightFixed", "value": data[i].isweightfixed });
                                        if (data[i].isweightfixed == 1) {
                                            arrPostValue.push({ "selector": "qtyAfterInPcs", "value": data[i].gramasi });
                                        }
                                    }
                                    arrPostValue.push({"selector":"COGS", "value":data[i].cogs}); 

                                    $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue)); 
                                    $newRow.find(".baseitemunit").html(data[i].baseunitname);  

                                    if (hasQtyInPcs && data[i].isweightfixed == 1) {
                                        $newRow.find("[name='qtyAfterInPcs[]']").prop("readonly", true);
                                    }
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

        
        this.updateDetail = function updateDetail(target,objAndValue,ui){  
                    var detailRow = $(target).closest(".transaction-detail-row");
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();

                    for(i=0;i<objAndValue.length;i++){   
                        detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
                    } 

                    // harus handle manual utk obj autosearch
                    if (ui.item['value'] != ''){  
                        var row = detailRow.find("[name=\"itemName[]\"]").first();
                        row.val(ui.item['value']);
                        detailRow.find(".baseitemunit").html(ui.item['baseunitname']);    

                        thisObj.updateQOH(row); 
                    } 

            thisObj.updateIsWeightFixed(itemKeyObj.val(), detailRow);
        } 

        this.calculateTotal = function calculateTotal(obj){    
                    //console.log("test");
            
                    var rowObj  = $(obj).closest(".transaction-detail-row"); 
                    var qtyBefore =  parseFloat(unformatCurrency(rowObj.find("[name=\"qtyBefore[]\"]").val())) || 0; 
                    var qtyAfter = parseFloat(unformatCurrency(rowObj.find("[name=\"qtyAfter[]\"]").val())) || 0; 
                    var qtyAdjust = qtyAfter - qtyBefore; 
                    rowObj.find("[name=\"qtyAdjust[]\"]").val(qtyAdjust);

    
                    if (tabObj.find("[name='qtyAdjustInPcs[]']").length > 0) {
                        var qtyBeforeInPcs = parseFloat(unformatCurrency(rowObj.find("[name=\"qtyBeforeInPcs[]\"]").val())) || 0;
                        var qtyAfterInPcs = parseFloat(unformatCurrency(rowObj.find("[name=\"qtyAfterInPcs[]\"]").val())) || 0;
                        var isWeightFixed = rowObj.find("[name='hidIsWeightFixed[]']").val();
                        var gramasi = parseFloat(unformatCurrency(rowObj.find("[name=\"hidGramasi[]\"]").val())) || 0;
                        
                        if (isWeightFixed == 1) {
                            qtyAfterInPcs = qtyAfter * gramasi;
                            rowObj.find("[name=\"qtyAfterInPcs[]\"]").val(qtyAfterInPcs);
                        }
                        
                        var qtyAdjustInPcs = qtyAfterInPcs - qtyBeforeInPcs;
                        rowObj.find("[name=\"qtyAdjustInPcs[]\"]").val(qtyAdjustInPcs);
                    
                    }

                    if (tabObj.find("[name='chkCostInPcs[]']").length > 0) {
                        var isPriceInPcs = rowObj.find("[name='chkCostInPcs[]']").val();
                        
                        var COGS = parseFloat(unformatCurrency(rowObj.find("[name='COGS[]']").val())) || 0;
                        var COGSInPcs = parseFloat(unformatCurrency(rowObj.find("[name='COGSInPcs[]']").val())) || 0;

                        var COGSBaseUnit = 0;
                        var COGSInPcsVal = 0;

                        if (isPriceInPcs == 1) {
                            if (qtyAfter > 0) {
                                COGSBaseUnit = (qtyAfterInPcs * COGSInPcs) / qtyAfter;
                            }
                            rowObj.find("[name='COGS[]']").val(COGSBaseUnit);
                        } else {
                            if (qtyAfterInPcs > 0) {
                                COGSInPcsVal = (qtyAfter * COGS) / qtyAfterInPcs;
                            }
                            rowObj.find("[name='COGSInPcs[]']").val(COGSInPcsVal);
                        }


                    }

                tabObj.find(".inputnumber, .inputdecimal").blur(); 

        } 

        this.updateQOH = function updateQOH(row){  

                    var selectedWarehouseKey = tabObj.find("[name=selWarehouseKey]" ).val();   
             
                    var trdate =  tabObj.find("[name=trDate]" ).val(); 

                    var selRow = row.closest(".transaction-detail-row");
                    var itemkey = selRow.find(" [name=\"hidItemKey[]\"]").first().val(); 

                    $.ajax({ 
                        beforeSend: function(xhr) { thisObj.activeAjaxConnections++;   },
                        type: "GET",
                        url:  'ajax-item.php',
                        async : false,
                        data: "action=searchData&getQOH=1&pkey="+itemkey+"&warehousekey=" + selectedWarehouseKey+"&trdate=" + trdate ,  
                        success: function(data){  
                             var temp = JSON.parse(data)[0];  
                             if (temp != undefined){
				// taro diatas, agar sekalian manggil change() di qtyBefore
                                if (tabObj.find("[name=\"qtyBeforeInPcs[]\"]").length > 0) {
                                    selRow.find("[name=\"qtyBeforeInPcs[]\"]").first().val(temp.qohinpcs).blur();
                                }

                                selRow.find("[name=\"qtyBefore[]\"]").first().val(temp.qoh).change().blur(); 

								 
								// tdk perlu update COGS, karena nanti user akan sellau ke load ulagn cogsnya,
								// nilai cogs sudah akan diproses ulagn ketika konfirmasi di class
                                //selRow.find("[name=\"COGS[]\"]").first().val(temp.cogs).change().blur(); 
                             } 
                            decreaseActiveAjaxConnections(thisObj); 
                        } ,
                        error: function(xhr, errDesc, exception) { 
                            decreaseActiveAjaxConnections(thisObj);
                        }
                    }) ; 

        }
        
        this.recalculateQOH = function recalculateQOH(){
              loadOverlayScreen({content: _LOADING_TEMPLATE_}); 

              //update QOH for each row 
              var itemNameRows = tabObj.find("[name=\"itemName[]\"]");    

              itemNameRows.each(function() {    
                  thisObj.updateQOH($(this)); 
              }) ; 

              hideOverlayScreen();

        }
        
   this.updateIsWeightFixed = function updateIsWeightFixed(itemKey, row)
        {

            if (tabObj.find("[name='qtyAfterInPcs[]']").length <= 0) {
                return;
            }
            
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
                    row.find("[name='qtyAfterInPcs[]']").first().prop("readonly", true);
                    var qtyAfterInPcs = parseFloat(data.gramasi);
                    row.find("[name='qtyAfterInPcs[]']").first().val(qtyAfterInPcs);
                    row.find("[name='hidGramasi[]']").first().val(qtyAfterInPcs);
                } else {
                    row.find("[name='qtyAfterInPcs[]']").first().prop("readonly", false);
                }

                 tabObj.find(".inputnumber, .inputdecimal").blur(); 

            });
        }

        this.onChangePriceInPcs = function onChangePriceInPcs(obj) {
            
            var row = $(obj).closest(".transaction-detail-row"); 
            var priceInPcs = row.find("[name='chkCostInPcs[]']").val();

            if (priceInPcs == 0) {
                row.find("[name='COGS[]']").attr("readonly", false); 
                row.find("[name='COGSInPcs[]']").attr("readonly", true); 
            } else {
                row.find("[name='COGS[]']").attr("readonly", true);  
                row.find("[name='COGSInPcs[]']").attr("readonly", false);
            }

        }
        
        this.rebindEl = function rebindEl(){ 
            // kalo bisa pindahin ke addnewrow
            bindEl(tabObj.find("[name='qtyBefore[]'], [name='qtyAfter[]'], [name='qtyBeforeInPcs[]'], [name='qtyAfterInPcs[]'],[name='COGS[]'],[name='COGSInPcs[]']"),'change', function() { thisObj.calculateTotal(this); });  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1&limit=25',thisObj.updateDetail);  
            bindEl(tabObj.find("[name='chkCostInPcs[]']"),'change',function(){ thisObj.onChangePriceInPcs($(this)) }); 
        }
        
        this.loadOnReady = function loadOnReady(){
            
            tabObj.find("[name=selWarehouseKey], [name=trDate]").change(function() { thisObj.recalculateQOH(); }); 
            tabObj.find("[name=btnImport]" ).on('click', function() { 
            
              var hasItem = false;    
              var itemNameRows = tabObj.find("[name=\"hidItemKey[]\"]");    
              itemNameRows.each(function() {   
                if ($(this).val() != 0 && $(this).val() != "")
                    hasItem = true;
              });

                      
               var importButton = $(this);                
               importButton.prop('disabled', true) ;     

                if (hasItem == true){

                    $( "#dialog-message" ).html("Import data akan mereset detail transaksi.");
                    $( "#dialog-message" ).dialog({ 
                          width: 300,
                          modal: true,
                          title:"Konfirmasi Import Data", 
                          open: function() { 
                              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                          },  
                          close:function() {}, 
                          buttons : {
                              OK : function (){    
                                    thisObj.importData();
                                    $( this ).dialog( "close" );
                              },
                              Cancel : function (){    
                                    $( this ).dialog( "close" );
                              }
                          },
                     });	 

                }else{
                     thisObj.importData();
                } 
            
                 
               importButton.prop('disabled', false) ;   

            });  
             
            if (thisObj.rs && thisObj.rs.statuskey == 1)
                thisObj.recalculateQOH();
            
            thisObj.rebindEl();
        }
}
