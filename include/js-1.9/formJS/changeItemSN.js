function ChangeItemSN(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      

    
        this.tabID = tabID;    

       this.updateSNInformation =  function updateSNInformation(){
            var sn = tabObj.find("[name=serialNumber]" ).val();

            tabObj.find("[name=itemName]").val("");
            tabObj.find("[name=hidItemKey]").val("");
            tabObj.find("[name=vendorPartNumber]").val("");
            tabObj.find("[name=hidVendorPartNumberKey]").val(""); 

            // kalo gk ad SN langsung return
            if (!sn) return;
            
            $.ajax({
                type: "GET",
                url:  'ajax-item-sn.php', 
                data: "action=searchSN&sn=" + sn
            }).done(function( data ) {  
                
                //if (!data){ alert(phpErrorMsg[213]);  tabObj.find("[name=serialNumber]").val(''); return; }
                    
                data = JSON.parse(data) ;     
                
                if (data.length == 0){ 
                    alert(phpErrorMsg[213]);  
                    var serialNumberObj = tabObj.find("[name=serialNumber]");
                    serialNumberObj.val(''); 
                    serialNumberObj.closest('form').bootstrapValidator('revalidateField', serialNumberObj.attr("name")); 
                    return; 
                }
                
                data = data[0];
                tabObj.find("[name=itemName]").val(decodeHTMLEntities(data.itemname));
                tabObj.find("[name=hidItemKey]").val(data.itemkey);
                tabObj.find("[name=vendorPartNumber]").val(data.partnumber); 
                tabObj.find("[name=hidVendorPartNumberKey]").val(data.vendorpartnumberkey);  
                
            }); 
        }
            
        this.rebindEl = function rebindEl(){    
//            bindEl(tabObj.find("[name='serialNumber[]']"),'change',function(){ thisObj.updateItem($(this)) });  
         } 
        
        this.loadOnReady = function loadOnReady(){
             
            tabObj.find("[name=serialNumber]").change(function() { thisObj.updateSNInformation(); }); 

            thisObj.rebindEl();
        }
}
