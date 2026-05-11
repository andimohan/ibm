function WarrantyClaimProgress(tabID,varConstant){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
        this.tabID = tabID;    

        /*this.updateItem =  function updateItem(){
            var sn = tabObj.find("[name=serialNumber]" ).val();

            tabObj.find("[name=itemName]").val("");
            tabObj.find("[name=hidItemKey]").val("");
            tabObj.find("[name=vendorPartNumber]").val("");
            tabObj.find("[name=hidVendorPartNumberKey]").val(""); 


            $.ajax({
                type: "GET",
                url:  'ajax-item.php', 
                data: "action=searchSerialNumberInMarket&sn=" + sn
            }).done(function( data ) { 
                data = JSON.parse(data) ; 

                if (data.length > 0 ){
                    data = data[0];
                    tabObj.find("[name=itemName]").val(data.itemname);
                    tabObj.find("[name=hidItemKey]").val(data.itemkey);
                    tabObj.find("[name=vendorPartNumber]").val(data.partnumber); 
                    tabObj.find("[name=hidVendorPartNumberKey]").val(data.vendorpartnumberkey); 
               
                }else {
                    alert(phpErrorMsg[213]);
                }
            }); 
        }*/
            
        this.updatePartNumberInformation =  function updatePartNumberInformation(){
            var vendorPartNumber = tabObj.find("[name=newVendorPartNumber]" ).val();

            tabObj.find("[name=newItemName]").val("");
            tabObj.find("[name=hidNewItemKey]").val(""); 

            // kalo gk ad SN langsung return
            if (!vendorPartNumber) return;
            
            $.ajax({
                type: "GET",
                url:  'ajax-item.php', 
                data: "action=searchVendorPartNumber&term=" + vendorPartNumber
            }).done(function( data ) { 
                if (!data){
                 alert(phpErrorMsg[213]);
                 return;
                }
                    
                data = JSON.parse(data) ;     
                data = data[0];
                tabObj.find("[name=newItemName]").val(data.itemname);
                tabObj.find("[name=hidNewItemKey]").val(data.itemkey);  
                
            }); 
        }
        
        this.updateNewItem =  function updateNewItem(){
            var sn = tabObj.find("[name=newSerialNumber]" ).val();

            tabObj.find("[name=newItemName]").val("");
            tabObj.find("[name=hidNewItemKey]").val("");
            tabObj.find("[name=newVendorPartNumber]").val("");
            tabObj.find("[name=hidNewVendorPartNumberKey]").val(""); 

            // kalo gk ad SN langsung return
            if (!sn) return;
            
            $.ajax({
                type: "GET",
                url:  'ajax-item.php', 
                data: "action=searchAvailableSerialNumber&sn=" + sn
            }).done(function( data ) { 
                if (!data){
                 alert(phpErrorMsg[213]);
                 return;
                }
                    
                data = JSON.parse(data) ;     
                data = data[0];
                tabObj.find("[name=newItemName]").val(decodeHTMLEntities(data.itemname));
                tabObj.find("[name=hidNewItemKey]").val(data.itemkey);
                tabObj.find("[name=newVendorPartNumber]").val(data.partnumber); 
                tabObj.find("[name=hidNewVendorPartNumberKey]").val(data.vendorpartnumberkey);  
                
            }); 
        }
        
        this.updateWarranty =  function updateWarranty(){
            var warrant = tabObj.find("[name=hidRefKey]" ).val();

            tabObj.find("[name=customerName]").val("");
            tabObj.find("[name=hidCustomerKey]").val("");


            $.ajax({
                type: "GET",
                url:  'ajax-warranty-claim.php', 
                data: "action=getDataRowById&pkey=" + warrant
            }).done(function( data ) { 
                data = JSON.parse(data) ; 

                if (data.length > 0 ){
                    data = data[0];
                    tabObj.find("[name=customerName]").val(data.customername);
                    tabObj.find("[name=hidCustomerKey]").val(data.customerkey);

                }else {
                    alert(phpErrorMsg[213]);

                }
            }); 
        }

        /*this.updateSNInformation = function updateSNInformation(row){
              row.find(".sn-information input").val("");

              $.ajax({  
                        type: "GET", 
                        url:  'ajax-sn.php', 
                        data: "action=getSNInformation&sn="+ row.find("[name=\"serialNumber[]\"]").val() ,  
                        success: function(data){   
                          var data = JSON.parse(data);  

                            data = data[0]; 
                            var solddate =  (data.lastsolddate != '') ? moment(data.lastsolddate).format("DD / MM / YYYY") : '';
                            row.find("[name=\"soldDate[]\"]").val(solddate);

                            var warrantyperiodexpireddate =  (data.warrantyperiodexpireddate != '') ? moment(data.warrantyperiodexpireddate).format("DD / MM / YYYY") : '';
                            row.find("[name=\"warrantyPeriodExpiredDate[]\"]").val(warrantyperiodexpireddate); 
                            row.find("[name=\"storeName[]\"]").val(data.recipientname);   
                        }  
                    }) ; 
        }*/


        this.updateReplacementPanel = function updateReplacementPanel(){
            var claim = tabObj.find("[name=selClaimResult]").val();
            var replacementPanel = tabObj.find(".replacement-panel");
            if (claim == varConstant.CLAIM_TYPE.replace || claim == varConstant.CLAIM_TYPE.upgrade)
                replacementPanel.show();
            else
                replacementPanel.hide();
            
            
            // kalo replace. SN, Part Number dan tgl expired harus sama
            if (claim == varConstant.CLAIM_TYPE.replace){
               // tabObj.find("[name=newVendorPartNumber]").prop("readonly", true);
                tabObj.find("[name=newVendorPartNumber]").val( tabObj.find("[name=vendorPartNumber]").val() );
                tabObj.find("[name=hidNewVendorPartNumberKey]").val( tabObj.find("[name=hidVendorPartNumberKey]").val() );
                tabObj.find("[name=newItemName]").val( tabObj.find("[name=itemName]").val() );
                tabObj.find("[name=hidNewItemKey]").val( tabObj.find("[name=hidItemKey]").val() );
                tabObj.find("[name=newWarrantyDate]").val( tabObj.find("[name=warrantyDate]").val() );  
            }else{
                
               // tabObj.find("[name=newVendorPartNumber]").prop("readonly", false);
            }
        }
            
        this.rebindEl = function rebindEl(){  
            //bindEl(tabObj.find("[name='serialNumber[]']"),'change',function(){ thisObj.updateItem($(this)) });  
        }
         
        
        this.loadOnReady = function loadOnReady(){ 
            //tabObj.find(".section-panel .title" ).click(function() { $(this).closest(".section-panel").find(".section-panel-content").first().toggle(); });
            //tabObj.find("[name=serialNumber]").change(function() { thisObj.updateItem(); });
            
            tabObj.find("[name=newSerialNumber]").change(function() { thisObj.updateNewItem(); }); 
            //tabObj.find("[name=newVendorPartNumber]").change(function() { thisObj.updatePartNumberInformation(); }); 
            
            tabObj.find("[name=refCode]").change(function() { thisObj.updateWarranty(); });
            tabObj.find("[name=selClaimResult]").change(function() { thisObj.updateReplacementPanel(); });
            thisObj.rebindEl();
        }
}
