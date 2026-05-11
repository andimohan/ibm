function CashBankRealization(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
      
        var objAndValue = new Array;  
        objAndValue.push({object:'hidCostKey[]', value :'pkey'});   
        var objAndValueForDetailItemAutoComplete = objAndValue;
    
        this.tabID = tabID;    

        this.calculateDetail = function calculateDetail(obj){      

            var row =  $(obj).closest(".transaction-detail-row");    
            var qty =  unformatCurrency(row.find("[name='qty[]']").val());
            //var realCostValue =  unformatCurrency(row.find("[name='realCostValue[]']").val()); 
            var amount =  unformatCurrency(row.find("[name='amount[]']").val()); 
            var realCostValue = amount / qty;

            /*if(row.find("[name='hidSettlementType[]']").val() == 0)
                row.find("[name='costValue[]']").val(realCostValue).blur(); */ 
                 
            //var subtotalCostValue = qty * unformatCurrency(row.find("[name='costValue[]']").val()) ;  
            //row.find("[name='hidSubtotalCostValue[]']").val(subtotalCostValue).blur();   
            
            row.find("[name='realCostValue[]']").val(realCostValue).blur();  

            thisObj.calculateTotal();
       }

     
     
        this.calculateTotal = function calculateTotal(){ 
            var totalRequest = 0;    
            var realization = 0;   
        
            var amountObj = tabObj.find("[name='amount[]']");
               
            tabObj.find("[name='hidSubtotalCostValue[]']").each(function(){ totalRequest += parseInt(unformatCurrency($(this).val())) || 0; })      
            
            tabObj.find("[name=total]").val(totalRequest).blur();   
           
            tabObj.find("[name='amount[]']").each(function(){  realization += parseInt(unformatCurrency($(this).val())) || 0;  })            
            tabObj.find("[name='totalRealization']").val(realization).blur();   
              c
            var totalReceived = totalRequest - realization; 
            tabObj.find("[name='totalReceived']").val(totalReceived).blur();   
            
            var totalPayment =  parseInt(unformatCurrency( tabObj.find("[name='totalPayment']").val() ));   
            var employeeAR =  parseInt(unformatCurrency(tabObj.find("[name='employeeAR']").val()));   
             
            var balance = totalPayment - totalReceived + employeeAR;
            balance *= -1;
            tabObj.find("[name='balance']").val(balance).blur();  
            thisObj.updateAccountSattlement(); 
            
        }

        this.updateReference = function updateReference(){
            
             $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-cost-cash-out.php', 
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidRefKey]").val(),  
                    beforeSend:function (xhr){ 
                         tabObj.find("[name=refCode2]").val("");
                         tabObj.find("[name=hidRefKey2]").val(0);
                         tabObj.find("[name=refCode3]").val("");
                         tabObj.find("[name=hidRefKey3]").val(0);
                         tabObj.find("[name=hidRefTable]").val(0);
                         tabObj.find("[name=employeeName]").val("");
                         tabObj.find("[name=hidEmployeeKey]").val(0);
                         tabObj.find("[name=customerName]").val("");
                         tabObj.find("[name=consigneeName]").val("");
                        
                         clearAllRows(tabObj.find(".mnv-transaction"));
                    },
                    success: function(data){  
                        
                            if(!data) return; 
                            var data = JSON.parse(data); 
                        
                            if (data.length == 0) return;
                          
                             tabObj.find("[name=hidRefTable]").val(data[0].reftabletype);
                             tabObj.find("[name=refCode2]").val(data[0].refcode);
                             tabObj.find("[name=hidRefKey2]").val(data[0].refkey);
                             tabObj.find("[name=refCode3]").val(data[0].refcode2);
                             tabObj.find("[name=hidRefKey3]").val(data[0].refkey2);
                             tabObj.find("[name=selWarehouse]").val(data[0].warehousekey);

                             updateComboboxReadonly(tabObj.find("[name=selWarehouse]"));
                             updateComboboxReadonly(tabObj.find("[name=hidRefTable]"));

                            // kalo JO, otomatis munculin nama penerima 
                             tabObj.find("[name=employeeName]").val(data[0].employeename);
                             tabObj.find("[name=hidEmployeeKey]").val(data[0].employeekey); 
                        
                             tabObj.find("[name=customerName]").val(data[0].customername);
                             tabObj.find("[name=consigneeName]").val(data[0].consigneename);

                             thisObj.importData();
                           
                    }  
                }); 
        }

        this.importData = function importData(){  
           
                var importButton =  tabObj.find("[name=btnImport]"); 
             
                var employeekey =  tabObj.find("[name=hidEmployeeKey]").val();    
                var refkey = tabObj.find("[name=hidRefKey]").val();
             
                if(!refkey) return;
                   
                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;
 
                $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-cost-cash-out.php',
                    beforeSend:function (xhr){
                        importButton.prop('disabled', true) ;   
                        clearAllRows($("#defaultForm-"+tabID));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDetailById&pkey="+refkey,  
                    success: function(data){ 
                            if(!data) return;
                                
                            var data = JSON.parse(data);  
                            var i;

                            for(i=0;i<data.length;i++){   
                                    var arrPostValue = []; 
                                    var qty = parseInt(unformatCurrency(data[i].qty)) || 0; 
                                    var costValue = parseInt(unformatCurrency(data[i].costvalue)) || 0; 
                                    var subtotalCost = qty * costValue;
                                  
                                    arrPostValue.push({"selector":"refheadercostkey", "value":data[i].refheadercostkey});
                                    arrPostValue.push({"selector":"hidCostKey", "value":data[i].costkey}); 
                                    arrPostValue.push({"selector":"costName", "value":data[i].costname});  
                                    arrPostValue.push({"selector":"qty", "value":qty}); 
                                    arrPostValue.push({"selector":"costValue", "value":costValue}); 
                                    arrPostValue.push({"selector":"realCostValue", "value":costValue}); 
                                    arrPostValue.push({"selector":"amount", "value":data[i].amount}); 
                                    arrPostValue.push({"selector":"hidSubtotalCostValue", "value":subtotalCost});  
                                    arrPostValue.push({"selector":"detailDesc", "value":data[i].description}); 
                                    arrPostValue.push({"selector":"hidSettlementType", "value":1});  
                                    $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));    
                           } 
                        
                            thisObj.rebindEl(); 
                            thisObj.calculateTotal();
                            tabObj.find(".inputnumber").change().blur(); 
  
                    } , 
                    complete:function() { 
                        importButton.prop('disabled', false);   
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                     
                }); 
        }  
        
        this.updateAccountSattlement = function updateAccountSattlement(){

           var balance = parseInt(unformatCurrency(tabObj.find("[name=balance]").val()));
           if(balance > 0){
               tabObj.find(".account").show();               
           }else{
               tabObj.find(".account").hide();               
           }
            
        }

        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
            thisObj.calculateTotal(); 
        }
                
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }

        this.rebindEl = function rebindEl(){
            bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailItemAutoComplete,'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');
            bindEl(tabObj.find("[name='amount[]'], [name='qty[]'] "),'change', function() { thisObj.calculateDetail($(this)); });   
        }
        
        this.loadOnReady = function loadOnReady(){
            //tabObj.find("[name=btnImport]" ).on('click', function() { thisObj.importData(); }); 
            //bindEl(tabObj.find("[name='employeeAR'] "),'change', function() { thisObj.calculateTotal(); });  
	     	tabObj.find("[name=employeeAR]").change(function(){thisObj.calculateTotal();}) 
                       
            thisObj.rebindEl();
        }
}
