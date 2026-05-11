function TicketSupport(tabID, uploadFolder, rsImage){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
  
        this.tabID = tabID;    
 
        var folder = uploadFolder;
        var imageUploaderTarget = "item-image-uploader";
        var arrImage = Array(); 
        var arrPHPThumbHash = Array();
        
        var id = tabObj.find("[name=hidId]").val(); 
        
        this.importData =  function importData(){
            //console.log(tabObj.find("[name=hidPurchaseOrderKey]" ).val())
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    beforeSend:function (xhr){
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidCustomerKey]" ).val() ,  
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
                        tabObj.find("[name=cityName]" ).val(data.cityname);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });
        }
        this.rebindEl = function rebindEl(){  
        }
          
        this.loadOnReady = function loadOnReady(){  
            if(id){   
				for($i=0;$i<rsImage.length;$i++) {
				    arrImage.push(rsImage[$i].file);
				    arrPHPThumbHash.push(rsImage[$i].phpThumbHash);
                } 
                createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":id, "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},true);
 
			}else{ 
				 createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,true); 
			}
             
            thisObj.rebindEl();
        }
}