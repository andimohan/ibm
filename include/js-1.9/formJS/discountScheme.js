function DiscountScheme(tabID, rs){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
		var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});   
		objAndValue.push({object:'sellingPrice[]', value :'sellingprice'});   
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
                        data: "action=searchData&categorykey=" +  tabObj.find("[name=selCategoryKey]" ).val() +"&getQOH=1&itemtype=1&warehousekey=" + tabObj.find("[name=selWarehouseKey]" ).val(),  
                        success: function(data){ 

                                var data = JSON.parse(data);  
                                var i;

                                for(i=0;i<data.length;i++){ 

                                    var arrPostValue = []; 
                                    arrPostValue.push({"selector":"hidItemKey", "value":data[i].pkey});
                                    arrPostValue.push({"selector":"itemName", "value":data[i].value}); 
                                    arrPostValue.push({"selector":"sellingPrice", "value":data[i].sellingprice}); 

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

        this.updateDetail = function updateDetail(target,objAndValue,ui){  
                var detailRow = $(target).closest(".transaction-detail-row");

                for(i=0;i<objAndValue.length;i++){   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
                } 

                // harus handle manual utk obj autosearch
                if (ui.item['value'] != ''){  
                    var row = detailRow.find("[name=\"itemName[]\"]").first();
                    row.val(ui.item['value']); 
                    } 

        } 

        this.rebindEl = function rebindEl(){ 
            // kalo bisa pindahin ke addnewrow
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);  
        }
        
        this.loadOnReady = function loadOnReady(){
            
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
            
            thisObj.rebindEl();
        }
}