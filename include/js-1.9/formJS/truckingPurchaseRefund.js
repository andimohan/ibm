function TruckingPurchaseRefund(tabID) {
   var thisObj = this;
   var tabObj = $("#" + tabID);

   this.tabID = tabID;
   
   this.updateSOInformation = function updateSOInformation()
   { 
      $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-service-order.php', 
                    async: false,
                    data: "action=getDataRowById&pkey=" + tabObj.find("[name=hidSOKey]").val() ,  
		  			beforeSend:function (xhr){ 
					   tabObj.find("[name=customerName]").val("");
                       tabObj.find("[name=hidCustomerKey]").val("");
					},
		  			success: function(data){   
		  
							data = parseJSON(data); 
							if(data.length == 0) return;

							 data = data[0]; 

							 tabObj.find("[name=customerName]").val(data.customername);
							 tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
                    
		  
					}
	  });
		  
   }
      
   this.rebindEl = function rebindEl()
   {   
         
   } 
     
   this.loadOnReady = function loadOnReady(){ 
         thisObj.rebindEl(); 

   }

}