function ChartOfAccount(tabID,rs) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID;  
        this.rs = (rs.length > 0) ? rs[0] : null;
        this.pkey = (rs.length > 0) ? rs[0]['pkey'] : 0;
    
        this.updateIncrementNumber =  function updateIncrementNumber(){
               
             var trDate = 0;
             var resetTypeKey = tabObj.find("[name=selResetType]").val();
             var objIncrementIn = tabObj.find("[name=inIncrement]" );
             var objIncrementOut = tabObj.find("[name=outIncrement]" );
 

             if (resetTypeKey == 2){
                trDate =  tabObj.find("[name=selDailyPeriod]" ).val();
                objIncrementIn = tabObj.find("[name=inDailyIncrement]" );
                objIncrementOut = tabObj.find("[name=outDailyIncrement]" );
             }else if (resetTypeKey == 3){
                trDate = tabObj.find("[name=selMonthlyPeriod]" ).val();
                objIncrementIn = tabObj.find("[name=inMonthlyIncrement]" );
                objIncrementOut = tabObj.find("[name=outMonthlyIncrement]" );
             }else if (resetTypeKey == 4){
                trDate =  tabObj.find("[name=selAnnuallyPeriod]" ).val(); 
                objIncrementIn = tabObj.find("[name=inAnnuallyIncrement]" );
                objIncrementOut = tabObj.find("[name=outAnnuallyIncrement]" );
             }

             $.ajax({
                    type: "POST",
                    url:  'ajax-coa.php', 
                    data: {
                            action : 'getRunningNumber',
                            pkey : thisObj.pkey,
                            resetTypeKey : resetTypeKey ,
                            trDate : trDate 
                         },
                    success: function(data){
                            if (!data) return;
                            var data = JSON.parse(data);  

                            var counterIn =  0  ;
                            var counterOut =   0 ;
                        
                            if(data.length > 0){
                                data = data[0];
                                counterIn =  data.counterin;
                                counterOut =  data.counterout;
                            }
                           
                            objIncrementIn.val(counterIn).blur();
                            objIncrementOut.val(counterOut).blur();
                    }  
            }); 
        }
         
        this.rebindEl = function rebindEl(){}
          
        this.loadOnReady = function loadOnReady(){ 
        
            tabObj.find("[name=chkCashBank]").on('change', function() { 
                tabObj.find(".cashbank").toggle();
             });
            
            tabObj.find("[name=selResetType]" ).change(function() {
               tabObj.find(".increment-number").hide();
               tabObj.find(".increment-number." + $(this).val()).show();  
               thisObj.updateIncrementNumber();
            });

            tabObj.find("[name=selDailyPeriod], [name=selMonthlyPeriod], [name=selAnnuallyPeriod]").change(function() { thisObj.updateIncrementNumber(); }); 
            tabObj.find("[name=selResetType]").change();
            
            
            thisObj.rebindEl();
        
        } 
}
