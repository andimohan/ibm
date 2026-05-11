function InstallationWorkOrder(tabID,rs) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
         
        var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
        //objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
        var objAndValueForDetailAutoComplete = objAndValue; 
    
        var objAndValue = new Array;  
		objAndValue.push({object:'hidTechnicianKey[]', value :'pkey'}); 
        var objAndValueForTechnicianAutoComplete = objAndValue; 
    
        this.tabID = tabID;   
    
        this.rs = (rs.length > 0) ? rs[0] : null;

      
        this.updateDetail = function updateDetail(target,objAndValue,ui){

                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first(); 
             
              
                for(i=0;i<objAndValue.length;i++){   
                    
                    //overwrite kalo kg
                    if(objAndValue[i].object == 'hidGramasi[]' && ui.item['weightunitkey'] == 2)
                    ui.item[objAndValue[i].value] *= 1000; 
                    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
             
                updateAvailableUnit(itemKeyObj, selUnitObj);

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
                
//                thisObj.calculateDetail(itemKeyObj);
 
         }
        
         this.updateDetailTechnician = function updateDetailTechnician(target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidTechnicianKey[]\"]").first();
             
              
                for(i=0;i<objAndValue.length;i++){    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
             
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"technicianName[]\"]").first().val(ui.item['value']); 
         }
          
         this.updateOutsource = function updateOutsource(obj){
              if ($(obj).val() == 1){ 
                 
             }else{
                 tabObj.find("[name=hidSupplierKey]").val('');
                 tabObj.find("[name=supplierName]").val('');
             }
         }
         
         /*this.showOutsource = function showOutsource(obj){  

             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .technician-detail").hide();
                 $("#" + tabID + " .outsource").show();
             }else{ 
                 $("#" + tabID + " .technician-detail").show();
                 $("#" + tabID + " .outsource").hide();
             }

         }*/

         this.updateOrderInformation = function updateOrderInformation(){
            var soKey = tabObj.find("[name=hidSalesOrderKey]").val();
              
             $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-subscription.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + soKey ,  
                }).done(function( data ) { 
 
                        if(!data) return;
                 
                        data = JSON.parse(data) ; 
                        data = data[0];
  
                        tabObj.find("[name=customerName]").val(data.customername);
                        tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
                        tabObj.find("[name=hidSalesKey]").val(data.saleskey);
                        tabObj.find("[name=salesName]").val(data.salesname);
                        tabObj.find("[name=selMedia]").val(data.mediakey);
                        tabObj.find("[name=locationName]").val(data.locationname);
                        tabObj.find("[name=phone]").val(data.phone);
                        tabObj.find("[name=address]").val(data.address);
                        tabObj.find("[name=selJobDetails]").val(data.jobdetailskey);
                 
                });
 
        }  
         
        this.rebindEl = function rebindEl(){
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('technicianName[]',objAndValueForTechnicianAutoComplete,'ajax-employee.php?action=searchData',thisObj.updateDetailTechnician);

        }
         
        this.loadOnReady = function loadOnReady(){ 
            
            //var chkisOutsource = tabObj.find("[name=chkIsOutsource]");
            //thisObj.updateOutsource(chkisOutsource);
            // thisObj.showOutsource(chkisOutsource);
            
            if (!thisObj.rs)
                 addNewTemplateRow("technician-row-template",null,null,thisObj.rebindEl);
             
             
            tabObj.find("[name=btnAddTechnician]").on('click', function() { addNewTemplateRow("technician-row-template",null,null,thisObj.rebindEl); });
            thisObj.rebindEl(); 
        }
}
 
