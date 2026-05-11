function Termination(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;   
         
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
                        tabObj.find("[name=sid]").val(data.sid);
                        tabObj.find("[name=attention]").val(data.attention);

                  
                });
 
        } 
         
        this.rebindEl = function rebindEl(){

        }
         
        this.loadOnReady = function loadOnReady(){ 
           thisObj.rebindEl(); 
        }
}
  
