function ItemUploadReciept(tabID, parentEl){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
        this.tabID = tabID;      
    
        this.closeTab = function closeTab(){
            selectedTab.newTab[0].remove();
            $tabs.tabs("refresh");   

            var num_tabs = findTabIndexByTitle(parentEl.parentTitle); 
            $tabs.tabs( "option", "active", num_tabs );  
            
            updateData(false,  parentEl.parentPanelId ); 
        }
        
        this.showCancelReason =  function showCancelReason(obj){  
            var status = $("[name=selStatus]").val();
            if (status != 3){
                $("[name=selCancelReasonKey]").val(0);
            } 
        }
        
        
        /*this.updateStatus = function updateStatus(obj){  
            var status = $(obj).attr("rel");
            var pkey = tabObj.find("[name=hidId]").val();
            var cancelreason  = tabObj.find("[name=selCancelReasonKey] option:selected").text();
            
            //var ajaxData = "action=updateStatus&pkey=" + pkey + "&statuskey=" + status+ "&cancelreason=" +cancelreasonkey;
            var ajaxData = { 
                'action': 'updateStatus', 
                'pkey': pkey, 
                'statuskey': status ,
                'cancelReason' : cancelreason
            }

            disabledButton(tabObj.find(".btn-primary"));   
            
            $.ajax({
                type: "POST",
                url:  'ajax-item-upload-receipt.php', 
                data: ajaxData,
                success: function(result){ 
                    
                    if(result.length == 0) return;
                    
                    result = JSON.parse(result);
                    
                    var error = ""; 
                    for (i=0;i<result.length;i++)    
                        error = error + "<li>" + result[i].message + "</li>";  

                    if (error != "")
                        error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";  

                    tabObj.find(".notification-msg").html(error).hide().fadeToggle("fast");
                    
                    if (!result[0].valid){ 
                        tabObj.find(".notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal");  
                        $("html, body").animate({ scrollTop: 0 }, "slow"); 
                    }else{
                         thisObj.closeTab();    
                    }
 
                    disabledButton(tabObj.find(".btn-primary"),false);   
                }  
            }); 
            
        }*/
        
        this.rebindEl = function rebindEl(){ 
             
        } 
        
        this.loadOnReady = function loadOnReady(){  
         
            //bindEl(tabObj.find("[name=btnApprove],[name=btnDecline]"),'click', function() { thisObj.updateStatus(this); });  
            //bindEl(tabObj.find("[name=selStatus]"),'change', function() { thisObj.showCancelReason(this); });  
            
            tabObj.find("[name=selStatus] option:first").attr("disabled",true);
            tabObj.find("[name=selStatus]").change(function(){thisObj.showCancelReason(this)}) 
            
            thisObj.rebindEl();
        
        }
        
}
