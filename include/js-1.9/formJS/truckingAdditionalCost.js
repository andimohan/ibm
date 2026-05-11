function TruckingAdditionalCost(tabID)
{
    
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    


    this.getWorkOrderData = function getWorkOrderData(typekey)
    {
         
        var pkey = tabObj.find("[name=hidWorkOrderKey]").val();
        var containerNumber = tabObj.find("[name=containerNumber]").val();
        
        // utamakan no spk
        var keyword = '';
        
        // typekey 1 : SPK
        // typekey 2 : Container Number
        
        if (typekey == 2) keyword = '&containernumber='+ containerNumber;
        else keyword = '&pkey='+ pkey;
            
        $.ajax({
            type: "GET",
            url:  'ajax-trucking-service-work-order.php',
            async: false,
            data: "action=getDataForTruckingAdditionalCost" + keyword ,  
            beforeSend : function() { 

                                tabObj.find("[name=containerNumber]").val(""); 
                                tabObj.find("[name=selWarehouse]").val(""); 
                                tabObj.find("[name=hidJobOrderKey]").val(""); 
                                tabObj.find("[name=jobOrderCode]").val(""); 
                                tabObj.find("[name=hidEmployeeKey]").val(""); 
                                tabObj.find("[name=driverName]").val(""); 
                                tabObj.find("[name=policeNumber]").val(""); 
                                tabObj.find("[name=workOrderCode]").val(""); 
                                tabObj.find("[name=hidWorkOrderKey]").val(""); 

					} 
        }).done(function(data) { 
        
            if(!data) return;
            
            data = parseJSON(data);
            
            if(data.length == 0){ 
                alert(phpErrorMsg[213])
                return;
            }
                    
            data = data[0];

            var containerNumber = '';
            if(data.containernumber != '') containerNumber = data.containernumber;
            else if(data.container2number != '') containerNumber = data.container2number;
            
            tabObj.find("[name=selWarehouse]").val(data.warehousekey); 
            tabObj.find("[name=hidJobOrderKey]").val(data.refkey); 
            tabObj.find("[name=jobOrderCode]").val(data.serviceordercode); 
            tabObj.find("[name=hidEmployeeKey]").val(data.driverkey); 
            tabObj.find("[name=driverName]").val(data.drivername); 
            tabObj.find("[name=policeNumber]").val(data.policenumber); 
            tabObj.find("[name=containerNumber]").val(containerNumber); 
            tabObj.find("[name=workOrderCode]").val(data.code); 
            tabObj.find("[name=hidWorkOrderKey]").val(data.pkey); 
                    
        });  

    }
    
    this.rebindEl = function rebindEl()
    {   
    
    } 
    
    this.loadOnReady = function loadOnReady(){ 
        thisObj.rebindEl();  
        tabObj.find("[name=containerNumber]").on('change', function() { thisObj.getWorkOrderData(2); });  
    }
    
}
