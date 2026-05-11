function TicketSupportWorkOrder(tabID,rs) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'});
        objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue; 
    
        var objAndValue = new Array;  
		objAndValue.push({object:'hidTechnicianKey[]', value :'pkey'}); 
        var objAndValueForTechnicianAutoComplete = objAndValue; 
    
        this.tabID = tabID;  
         
        this.rs = (rs.length > 0) ? rs[0] : null;
        
        this.customerData =  function customerData(){
            var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    beforeSend:function (xhr){
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataRowById&pkey=" + customerkey ,  
                    success: function(data){
                    
                        data = JSON.parse(data) ;
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
                        
                        data = data[0];
                        tabObj.find("[name=sid]" ).val(data.sid);
                        //tabObj.find("[name=hidCustomerKey]" ).val(data.customerkey);
                        tabObj.find("[name=customerName]" ).val(data.name);
                        tabObj.find("[name=phone]" ).val(data.phone);
                        tabObj.find("[name=attention]" ).val(data.attention);
                        tabObj.find("[name=email]" ).val(data.email);
                        tabObj.find("[name=address]" ).val(data.address);
                        tabObj.find("[name=cityName]" ).val(data.cityname);
                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
        this.updateTicketInformation =  function updateTicketInformation(){
            $.ajax({
                    type: "GET",
                    url:  'ajax-ticket-support.php',
                    beforeSend:function (xhr){
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidSupportTicketKey]").val() ,  
                    success: function(data){ 
                        data = JSON.parse(data) ;
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
                        data = data[0];
                        
                        tabObj.find("[name=message]" ).val(data.message);
                        tabObj.find("[name=hidCustomerKey]" ).val(data.customerkey);
                        thisObj.customerData();
                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }  
        
        this.rebindEl = function rebindEl(){
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1',thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('technicianName[]',objAndValueForTechnicianAutoComplete,'ajax-employee.php?action=searchData',thisObj.updateDetailTechnician);

        }
         
        this.loadOnReady = function loadOnReady(){ 
            
             if (!thisObj.rs)
                 addNewTemplateRow("technician-row-template",null,null,thisObj.rebindEl);
               
                
            
            tabObj.find("[name=btnAddTechnician]").on('click', function() { addNewTemplateRow("technician-row-template",null,null,thisObj.rebindEl); });
            thisObj.rebindEl(); 
        }
}