function WarehouseLayout(tabID, uploadFolder){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;    
  
        var id = tabObj.find("[name=hidId]").val(); 
      
        this.rebindEl = function rebindEl(){
			 // tetep kirim marketplacekey gpp, nanti di class akan otoamtis diambil dr providernya 
        }

        this.updateWarehouse = function updateWarehouse(){ 
            var parentKey =  tabObj.find("[name=selCategory]").val();
            var warehouse =  tabObj.find("[name=selWarehouseKey]");

            if (parentKey == 0) {
                warehouse.prop('disabled', false);
            } else {

                $.ajax({
                    type: "GET",
                    url:  'ajax-warehouse-layout.php', 
                    async : false,
                    data: "action=getDataWarehouse&pkey=" + parentKey,  
                    beforeSend:function (xhr){ 
                        // tabObj.find("[name=hidSalesKey]").val(0);
                        // tabObj.find("[name=salesName]").val("");
                    },
                success: function (data) {  
                        data = parseJSON(data); 
                        // data = data[0]; 
                        console.log(data[0]);
                        console.log(data[0].warehousekey);
                        if(data.length == 0)return;  

                        warehouse.val(data[0].warehousekey);
                        
                    //    tabObj.find("[name=hidSalesKey]").val(data.pkey);
                    //    tabObj.find("[name=salesName]").val(data.name);
                         
                    }  
                });
                warehouse.prop('disabled', true);
            }   
        }
          
        this.loadOnReady = function loadOnReady(){ 
            
            tabObj.find("[name=selCategory]").change(function(){thisObj.updateWarehouse(this)}); 
            thisObj.updateWarehouse();
            thisObj.rebindEl();
        }
}