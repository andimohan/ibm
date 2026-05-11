function PaymentConfirmation(tabID){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      

    this.tabID = tabID; 
         
    this.updateOrderInformation = function updateOrderInformation(){    
                    var saleskey = tabObj.find("[name='hidSalesKey']").val();

                     $.ajax({
                        type: "GET",
                        async:false,
                        url:  'ajax-sales-order.php',
                        data: "action=getDataRowById&pkey=" +  saleskey + "&statuskey=1" ,  
                    }).done(function( data ) { 
                            data = JSON.parse(data) ;  
                            data = data[0]; 
                            tabObj.find("[name='customerName']").val(data.customername).blur(); 
                            tabObj.find("[name='amount']").val(data.grandtotal).blur(); 
                    });  
        } 


    this.rebindEl = function rebindEl(){   
    }

    this.loadOnReady = function loadOnReady(){ 


        thisObj.rebindEl();
    }
}