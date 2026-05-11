function SalesOrderReturn(tabID, rs, cashTOP){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue; 
	
        var firstOpened = true;
    
        this.tabID = tabID;    
        this.rs = (rs.length > 0) ? rs[0] : null;
        
    
        this.importData =  function importData(){  

                //loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;

                $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order.php',
                    beforeSend:function (xhr){
                        clearAllRows(tabObj.find('.sales-delivery-detail'));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataForSalesOrderReturn&pkey=" +  tabObj.find("[name=hidSalesOrderKey]" ).val() ,  
                    success: function(data){ 
                            if (!data) return;
                        
                            var data = JSON.parse(data);   
                            // console.log(data);
                        
                            for(i=0;i<data.length;i++){   
                                var qtyInBaseUnit = parseInt(data[i].qtyinbaseunit);
                                var deliveredQtyInBaseUnit = parseInt(data[i].deliveredqtyinbaseunit);
                                var returnQtyInBaseUnit = parseInt(data[i].returnqtyinbaseunit);

                                var arrPostValue = [];  
                                arrPostValue.push({"selector":"hidSODetailKey", "value":data[i].pkey});
                                arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                                arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                                arrPostValue.push({"selector":"orderedQtyInBaseUnit", "value":qtyInBaseUnit}); 
                                arrPostValue.push({"selector":"qtyMinusInBaseUnit", "value":deliveredQtyInBaseUnit - returnQtyInBaseUnit}); 
                                arrPostValue.push({"selector":"returnQtyInBaseUnit", "value":deliveredQtyInBaseUnit - returnQtyInBaseUnit}); 
                                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                                $newRow.find(".baseitemunit").first().html(data[i].baseunitname); 
                            } 
                            

                        thisObj.rebindEl(); 
                        tabObj.find(".inputnumber, .inputdecimal").blur(); 

                    }  , 
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
            
        this.calculateTotal = function calculateTotal(){ 

                var shipmentFee = parseInt(unformatCurrency(tabObj.find("[name='shipmentFee']").val())) || 0 ;    

                var total =  shipmentFee ;
                tabObj.find("[name='total']").val(total).blur();

                var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

                var balance = totalPayment - total ;
                tabObj.find("[name='balance']").val(balance).blur();

        }   
 
        this.updateSalesDeliveryMinusQty = function updateSalesDeliveryMinusQty(){ 
         
            //loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;
    
            $.ajax({ 
                    beforeSend: function(xhr) { thisObj.activeAjaxConnections++;   },
                    type: "GET",
                    url:  'ajax-sales-order.php', 
                    data: "action=getDataForSalesOrderReturn&pkey=" +  tabObj.find("[name=hidSalesOrderKey]" ).val(),  
                    success: function(data){  
                       
                        var data = JSON.parse(data);  
                        var i;
                        
                         tabObj.find("[name=\"hidItemKey[]\"]").each(function() {  
                             if (!$(this).closest(".transaction-detail-row").hasClass("detail-row-template"))
                               $(this).closest(".transaction-detail-row").addClass("will-delete");
                         }) 
                       
                        
                        for(i=0;i<data.length;i++){  
                            var row = tabObj.find("[name=\"hidItemKey[]\"][value='"+data[i].itemkey+"']").closest('.transaction-detail-row');
                             
                           //row.find("[name=\"orderedQty[]\"]").first().val(data[i].qty);
                           row.find("[name=\"qtyMinusInBaseUnit[]\"]").first().val(data[i].deliveredqtyinbaseunit - data[i].returnqtyinbaseunit); 
                           row.removeClass("will-delete");
                            
                        }

                         // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber 
                         tabObj.find(".inputnumber, .inputdecimal").blur();
                         tabObj.find(".will-delete").remove();
                          
                    }  ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                }) ;  
 
            }
        
         this.updateTOP = function updateTOP(){
          
                    var selTermOfPaymentKey = tabObj.find("[name=selTermOfPaymentKey]" ).val();   
                    var supplierkey = tabObj.find("[name=hidSupplierKey]" ).val(); 

                       $.ajax({
                            type: "GET",
                            url:  'ajax-supplier.php',
                            data: "action=getDataRowById&pkey=" + supplierkey ,  
                        }).done(function( data ) {

                                data = JSON.parse(data) ; 
                                data = data[0];

                                if (firstOpened == true){
                                    firstOpened = !firstOpened;
                                    thisObj.updateCustomerInformation(data.termofpaymentkey);
                                }else if (selTermOfPaymentKey != data.termofpaymentkey ){

                                        $( "#dialog-message" ).html("Apakah Anda ingin mengganti data pembayaran dengan data default untuk pemasok ini ?");
                                        $( "#dialog-message" ).dialog({
                                          width: 300,
                                          modal: true,
                                          title:"Konfirmasi Perubahan Data Pembayaran", 
                                          open: function() {
                                              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                                          }, 
                                          buttons : {
                                              OK : function (){    
                                                    thisObj.updateCustomerInformation(data.termofpaymentkey);
                                                   $( this ).dialog( "close" );
                                              },
                                              Cancel : function (){  
                                                    $( this ).dialog( "close" );
                                              }
                                          } 

                                        });	    
                                } 

                        }); 

        }
                
         this.updateCustomerInformation =  function updateCustomerInformation (topkey){
            if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + topkey + "']").length > 0)
                tabObj.find("[name=selTermOfPaymentKey]").val(topkey).change();  
        }
                
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
            thisObj.calculateTotal(); ;   
        }
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
            
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1');
        }
        
        this.loadOnReady = function loadOnReady(){
             
            tabObj.find("[name=selTermOfPaymentKey]" ).change(function() {
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

               tabObj.find(".cashTOP").show();
            });   
            tabObj.find("[name=selTermOfPaymentKey]" ).change();   
    
            if (thisObj.rs && thisObj.rs.statuskey == 1)
                thisObj.updateSalesDeliveryMinusQty();
               
            tabObj.find("[name=shipmentFee]" ).change(function(){thisObj.calculateTotal(this)}) 
        
            thisObj.rebindEl(); 
          
        }
}