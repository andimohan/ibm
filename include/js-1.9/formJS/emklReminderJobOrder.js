function EMKLReminderJobOrder(tabID,data,varConstant) {   
     
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
        this.tabID = tabID;

    	var objAndValue = new Array;
		objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'});  
        var objAndValueForDetailAutoComplete = objAndValue;
            
    	var objAndValue = new Array;
		objAndValue.push({object:'hidServiceDetailKey[]', value :'pkey'});   
        var objAndValueForDetailServiceAutoComplete = objAndValue;


        this.updateAirOrSea = function updateAirOrSea(){  
            var selAirSeaObj = tabObj.find("[name=selAirSea]");
            var selContainerObj = tabObj.find("[name=selContainerType]");
            var selVolumeTypeObj = tabObj.find("[name=selVolumeType]");
             
            if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea){
                //laut
                //selContainerObj.prop("disabled", false);
                selVolumeTypeObj.val(varConstant.EMKL.volume.cbm); 
            }else{ 
                //udara
                //selContainerObj.prop("disabled", true); 
                selVolumeTypeObj.val(varConstant.EMKL.volume.kg);
            }
            
            selContainerObj.change();
            selVolumeTypeObj.change();
         
        }
        
        this.updateJobType = function updateJobType(){
           // kalo LCL gk ad customer dan conginee 
            var selContainerObj = tabObj.find("[name=selContainerType]");  
            var selAirSeaObj = tabObj.find("[name=selAirSea]");
            var fclOnlyObj = tabObj.find(".fcl-only");
            var lclOnlyObj = tabObj.find(".lcl-only");
            var customerDetailRow = tabObj.find(".customer-row").not(".row-template");
            
            var containerType = selContainerObj.val(); 
            if(containerType == varConstant.EMKL.container.lcl ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                tabObj.find(".truckingfcl").hide();
                tabObj.find(".lcl-only").show();

                // kayanya udah gk kepake
               $(".fcl-readonly").attr("readonly", false); 
                
            }else if(containerType == varConstant.EMKL.container.lclnc ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                tabObj.find(".truckingfcl, .lcl-only").hide(); 
                tabObj.find(".lclnc").show(); 
                
            }else if(containerType == varConstant.EMKL.container.fcl || 
					 containerType == varConstant.EMKL.container.trucking){
                 
				lclOnlyObj.hide();
                fclOnlyObj.show();
                
                      
                $(".fcl-readonly").attr("readonly", true); 
                tabObj.find(".truckingfcl").show();
                //tabObj.find(".lcl").hide();
				tabObj.find(".lclonly").hide();


                 // kalo jenisnya air, gk ad container
                 
                if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea)
                 tabObj.find(".sea-only").show(); 
                else
                 tabObj.find(".sea-only").hide(); 
            }else{
                lclOnlyObj.hide();
                fclOnlyObj.show();
                
                $(".fcl-readonly").attr("readonly", true); 
                tabObj.find(".truckingfcl").hide();
                tabObj.find(".lcl").hide();
   
                
                 // kalo jenisnya air, gk ad container
                 
                if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea)
                 tabObj.find(".sea-only").show(); 
                else
                 tabObj.find(".sea-only").hide(); 
                
                
            }  
            
        }

       
       this.updateSelectDisabled= function updateSelectDisabled(obj){
	  
           var row =  obj.closest('.form-group');
           
           if(obj.val() == 1){
				
                row.find('.select-object').prop('disabled',false);
                row.find('.dateDisabled').removeClass("force-readonly");
                row.find('.dateDisabled').datepicker('enable');
                row.find('.dateDisabled').val('');

           }else{
			    row.find('.select-object').prop('disabled',true);
				row.find('.dateDisabled').addClass("force-readonly");
				row.find('.dateDisabled').datepicker('disable');
				row.find('.dateDisabled').val(''); 
				row.find('.select-object').val(0);  
           }
           
        }
       
       this.updateSelectOption = function updateSelectOption(obj){
			var row =  obj.closest('.form-group');
			if(obj.val() == 2){ 
				row.find('.dateDisabled').removeClass("force-readonly");
				row.find('.dateDisabled').datepicker('enable');
			}else{  
				row.find('.dateDisabled').addClass("force-readonly");
				row.find('.dateDisabled').datepicker('disable');
				row.find('.dateDisabled').val(''); 
			}

       }
  
		
        this.updateVolumeType = function updateVolumeType(){
            var volumeTypeObj = tabObj.find(".volume-type").html( tabObj.find("[name=selVolumeType]").find("option:selected").text() );
        }

        this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(newRow){
            //get previous row
            prevRow = newRow.prev();
            var containerName = prevRow.find("[name='containerDetailName[]']").val();
            var containerKey = prevRow.find("[name='hidContainerDetailKey[]']").val();
            
            newRow.find("[name='containerDetailName[]']").val(containerName);
            newRow.find("[name='hidContainerDetailKey[]']").val(containerKey);
              
        }

         
        this.getRowObj = function getRowObj(obj){
            return obj.closest(".div-table-row");
        }
    

        this.rebindEl = function rebindEl(){   
            
            bindAutoCompleteForTransactionDetail('serviceDetailName[]',objAndValueForDetailServiceAutoComplete,'ajax-item.php?action=searchData&itemtype=3');    
            bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailAutoComplete,'ajax-container.php?action=searchData');   

        }
        
        this.loadOnReady = function loadOnReady(){  
          
            tabObj.find("[name=selAirSea]").change(function() { thisObj.updateAirOrSea(); });
            tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 
            tabObj.find("[name=selVolumeType]").change(function() { thisObj.updateVolumeType(); });
     
            thisObj.updateAirOrSea();
            tabObj.find( " .section-panel .title" ).click(function() {  
                $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
            });

            tabObj.find(".chkDisabled").change(function(){thisObj.updateSelectDisabled($(this))});
            tabObj.find(".selectOption").change(function(){thisObj.updateSelectOption($(this))}); // khusus telex
			 
            if (!data['volumeDetail'] || data['volumeDetail'].length == 0)
                addNewTemplateRow("volume-row-template",null,null,thisObj.rebindEl);
            
            if (!data['containerNumberDetail'] || data['containerNumberDetail'].length == 0)
                addNewTemplateRow("container-row-template",null,null,thisObj.rebindEl);
            
  
            tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
            
            
            thisObj.rebindEl();
        }
}
