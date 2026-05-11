function InstallationBAST(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
  
        this.tabID = tabID;    
        
        var id = tabObj.find("[name=hidId]").val(); 
        
        
        this.importCustData =  function importCustData(val){
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    beforeSend:function (xhr){
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataRowById&pkey=" +  val ,  
                    success: function(data){
                        data = JSON.parse(data) ; 
                     
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
  
                        data = data[0]; 
                          
                        tabObj.find("[name=customerName]" ).val(data.name);
                        tabObj.find("[name=phone]" ).val(data.phone);
                        tabObj.find("[name=attention]" ).val(data.attention);
                        tabObj.find("[name=email]" ).val(data.email);
                        tabObj.find("[name=address]" ).val(data.address);
                        tabObj.find("[name=media]" ).val(data.medianame);
                        tabObj.find("[name=locationName]" ).val(data.locationname);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
        
        this.importJobDetailData =  function importJobDetailData(val){
            $.ajax({
                    type: "GET",
                    url:  'ajax-job-detail.php',
                    beforeSend:function (xhr){
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataRowById&pkey=" +  val ,  
                    success: function(data){ 
                        data = JSON.parse(data) ; 
                     
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
   
                        data = data[0]; 
                        tabObj.find("[name=jobDetails]" ).val(data.name);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
         
        this.importData =  function importData(){
            $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-subscription.php',
                    beforeSend:function (xhr){
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidSalesOrderSubsKey]" ).val() ,  
                    success: function(data){
                        data = JSON.parse(data) ; 
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
  
                        data = data[0]; 
                        thisObj.importCustData(data.customerkey);
                        //thisObj.importJobDetailData(data.jobdetailskey);
                        tabObj.find("[name=selJobDetails]").val(data.jobdetailskey);
                        tabObj.find("[name=selWarehouseKey]" ).val(data.warehousekey);
                        tabObj.find("[name=PICName]" ).val(data.employeename);
                        tabObj.find("[name=products]" ).val(data.product);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
        
        this.rebindEl = function rebindEl(){  
        }
          
        this.loadOnReady = function loadOnReady(){  
             
            thisObj.rebindEl();
        }
}