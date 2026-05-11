function CustomCode(tabID,rs){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.pkey = (rs.length > 0) ? rs[0]['pkey'] : 0;
 
    this.updateIncrementNumber =  function updateIncrementNumber(){
               
         var trDate = 0;
         var resetTypeKey = tabObj.find("[name=selResetType]").val();;
         var warehousekey = tabObj.find("[name=selWarehouseKey]").val();
         
         var resetWarehouse = tabObj.find("[name=chkResetWarehouse]").val();
         if (resetWarehouse == 0) warehousekey = 0;
             
         var objIncrement = tabObj.find("[name=increment]" );


         if (resetTypeKey == 2){
            trDate =  tabObj.find("[name=selDailyPeriod]" ).val();
            objIncrement = tabObj.find("[name=dailyIncrement]" );
         }else if (resetTypeKey == 3){
            trDate = tabObj.find("[name=selMonthlyPeriod]" ).val();
            objIncrement = tabObj.find("[name=monthlyIncrement]" );
         }else if (resetTypeKey == 4){
            trDate =  tabObj.find("[name=selAnnuallyPeriod]" ).val(); 
            objIncrement = tabObj.find("[name=annuallyIncrement]" );
         }

         $.ajax({
                type: "POST",
                url:  'ajax-custom-code.php', 
                data: {
                        action : 'getRunningNumber',
                        pkey : thisObj.pkey,
                        resetTypeKey : resetTypeKey ,
                        warehousekey : warehousekey ,
                        trDate : trDate 
                     },
                success: function(data){ 
                        var data = JSON.parse(data);   
                        objIncrement.val(data).blur();
                }  
        }); 
    }
          
    this.showAutoCode = function showAutoCode(obj){   
		
		var parentModule = parseInt(tabObj.find("[name=selParentModule]").val());
		
		if(parentModule != 0) return;
			
         if ($(obj).val() == 1)
             tabObj.find(".showAutoCode").show();
         else
             tabObj.find(".showAutoCode").hide(); 
    }
      

	this.showNotParent = function showNotParent(obj){
		  if ($(obj).val() == 0)
             tabObj.find(".showNotParent").show();
         else
             tabObj.find(".showNotParent").hide(); 
	}
	
	this.rebindEl = function rebindEl(){ }

    this.loadOnReady = function loadOnReady(){ 
        // on change currency, chnage amout decimal type
        
        tabObj.find("[name=selResetType]" ).change(function() {
		   tabObj.find(".increment-number").hide();
		   tabObj.find(".increment-number." + $(this).val()).show();  
           thisObj.updateIncrementNumber();
		});
        
        tabObj.find("[name=selDailyPeriod], [name=selMonthlyPeriod], [name=selAnnuallyPeriod], [name=selWarehouseKey], [name=chkResetWarehouse]").change(function() { thisObj.updateIncrementNumber(); }); 
        tabObj.find("[name=selResetType]").change();

		
        tabObj.find("[name=chkIsAutoCode]").on('change',function() {  thisObj.showAutoCode(this) });
        tabObj.find("[name=chkIsAutoCode]").change();
		
		// harus dibawah 
        tabObj.find("[name=selParentModule]").on('change',function() {  thisObj.showNotParent(this) });
        tabObj.find("[name=selParentModule]").change();
		 
    }
}