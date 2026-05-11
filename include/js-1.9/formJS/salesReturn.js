function SalesReturn(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
        this.tabID = tabID;   
      
        this.calculateDetail = function calculateDetail(obj){     
            var row =  $(obj).closest(".transaction-detail-row");   
            //var itemkey =  row.find("[name='hidItemKey[]']").val();

            var qty =  unformatCurrency(row.find("[name='qty[]']").val());
            var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());

            var subtotal = qty  *  priceInUnit;
            row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 

            thisObj.calculateTotal();
	   }
	
	    this.calculateTotal = function calculateTotal(){  
         
            var subtotal = 0; 
            tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
           // tabObj.find("[name='subtotal']").val(subtotal).blur();
 
            var total = subtotal;
            tabObj.find("[name='total']").val(total).blur();

            var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));
 
            var balance = totalPayment - total; 
            tabObj.find("[name='balance']").val(balance).blur();

	   }
      
        this.updateInformation = function updateInformation(){
            var sokey = tabObj.find("[name=hidRefKey]").val();
           
            $.ajax({
                type: "GET",
                url:  'ajax-sales-order-car-service.php',
                beforeSend:function (xhr){ 
                    thisObj.activeAjaxConnections++; 
                },
                data: "action=getDataRowById&pkey=" + sokey,  
                success: function(data){  
                    if (!data) return;

                    var data = JSON.parse(data);   
                    tabObj.find("[name=customerName]").val(data[0].customername);
                    
                }, 
                complete:function(xhr, desc) {  
                    decreaseActiveAjaxConnections(thisObj);  
                }
            });

             $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-car-service.php',
                    beforeSend:function (xhr){
                        clearAllRows(tabObj.find('.sales-return-detail'));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDetailById&pkey=" + sokey,  
                    success: function(data){ 
                        if (!data) return;
                        var data = JSON.parse(data);  

                        var i; 
                        for(i=0;i<data.length;i++){     
                            var arrPostValue = [];  
                            arrPostValue.push({"selector":"hidSODetailKey", "value":data[i].pkey}); 
                            arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey}); 
                            arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                            arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                            arrPostValue.push({"selector":"selUnit", "value":data[i].unitkey}); 
                            arrPostValue.push({"selector":"priceInUnit", "value": (data[i].total / data[i].qty)}); 
                            arrPostValue.push({"selector":"detailSubtotal", "value":data[i].total});
                            
                            $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));
                            
                            updateComboboxReadonly($newRow.find("[name='selUnit[]']")); 
                            
                        }  
                        
                         tabObj.find(".inputnumber").blur(); 
                         thisObj.calculateDetail(); 
                         thisObj.rebindEl(); 

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
            
            
        }
                     
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
            thisObj.calculateTotal();  
        }
                      
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
        
        this.rebindEl = function rebindEl(){
            //bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);
            bindEl(tabObj.find("[name='qty[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
        }
         
        this.loadOnReady = function loadOnReady(){ 
            
             
            thisObj.rebindEl(); 
        }
}