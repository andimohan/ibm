function Car(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID;   
    
        this.showVendor = function showVendor(){  
             var isVendor = tabObj.find("[name=chkIsVendor]").val(); 
             var vendor = tabObj.find(".vendor");
               
             if (isVendor == 1) 
                 vendor.show();
             else 
                 vendor.hide();
               
         } 
               
        this.updateVolumeChange = function updateVolumeChange(){
             
            var length = parseFloat(unformatCurrency(tabObj.find("[name=length]").val())) || 0;
            var width = parseFloat(unformatCurrency(tabObj.find("[name=width]").val())) || 0;
            var height = parseFloat(unformatCurrency(tabObj.find("[name=height]").val())) || 0;
              
             var volume = length * width * height;
             tabObj.find("[name='cbm']").val(volume).blur();
         }

        this.rebindEl = function rebindEl(){}
            
        this.loadOnReady = function loadOnReady(){ 
             
            tabObj.find("[name=chkIsVendor]").on('change', function() { thisObj.showVendor(); });
              
            tabObj.find("[name=length], [name=width], [name=height]").change(function () {
                thisObj.updateVolumeChange()
            });
			
            thisObj.rebindEl(); 


        }
}