function TemplateEMKLJobOrder(tabID,data,varConstant) {

    var thisObj = this;
    var tabObj = $("#" + tabID); 

    this.tabID = tabID;    

    var LCLTYPE = varConstant.LCLTYPE || 0;

    var objAndValue = new Array;
    objAndValue.push({object:'hidCommodityKey[]', value :'pkey'});   
    var objAndValueForDetailCommodityAutoComplete = objAndValue;
    
    this.updateAirOrSea = function updateAirOrSea(){  
        
        var selAirSeaObj = tabObj.find("[name=selAirSea]");
        var selContainerObj = tabObj.find("[name=selContainerType]");
        var selVolumeTypeObj = tabObj.find("[name=selVolumeType]");

        var seaOnlyObj = tabObj.find(".sea-only");
        var airOnlyObj = tabObj.find(".air-only");
            
        if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea){
            //laut
            //selContainerObj.prop("disabled", false);
            selVolumeTypeObj.val(varConstant.EMKL.volume.cbm); 
            airOnlyObj.hide();
            seaOnlyObj.show();
        }else{ 
            //udara
            //selContainerObj.prop("disabled", true); 
            seaOnlyObj.hide();
            airOnlyObj.show();
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
                
            }else if(containerType == varConstant.EMKL.container.lclnc ||
                     containerType == varConstant.EMKL.container.freightcustomlcl  ||
                     containerType == varConstant.EMKL.container.customlcl
                    ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                tabObj.find(".truckingfcl, .lcl-only").hide(); 
                tabObj.find(".lclnc").show(); 
                
            }else if(containerType == varConstant.EMKL.container.fcl || 
                     containerType == varConstant.EMKL.container.freightcustomfcl || 
					 containerType == varConstant.EMKL.container.trucking || 
					 containerType == varConstant.EMKL.container.customfcl){
                 
				lclOnlyObj.hide();
                fclOnlyObj.show();
                
                      
                $(".fcl-readonly").attr("readonly", true); 
                tabObj.find("[name=chkIsMaster]").val(0);
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
                tabObj.find("[name=chkIsMaster]").val(0);
                tabObj.find(".truckingfcl").hide();
                tabObj.find(".lcl").hide();
   
                
                 // kalo jenisnya air, gk ad container
                 
                if (selAirSeaObj.val() == varConstant.EMKL.shipping.sea)
                 tabObj.find(".sea-only").show(); 
                else
                 tabObj.find(".sea-only").hide(); 
                
                
            }  
            
            thisObj.updateJobTypeLabel();
            tabObj.find("[name=chkIsMaster]").change();
        }
        
        this.updateJobTypeLabel = function updateJobTypeLabel(){
            
            var containerType = tabObj.find("[name=selContainerType] option:selected").html();
            var airSeaType = tabObj.find("[name=selAirSea] option:selected").html();
            
            tabObj.find(".label-container-type").html(containerType);
            tabObj.find(".label-air-sea").html(airSeaType);
        }


    this.updateFieldfForLCLDetail = function updateFieldfForLCLDetail(){
           
            // kalo statusnya sudah bkn menunggu, langsugn return saja
            
            var status = tabObj.find("[name=selStatus]").val();
            if (status > 2) return; // sementara
            
            
            var readonlyField = false;
            var containerType = tabObj.find("[name=selContainerType]").val(); 
            var isMaster = tabObj.find("[name=chkIsMaster]").val();
              
            var headerPanel = tabObj.find(".header-panel");
            
            if (containerType == varConstant.EMKL.container.lcl && isMaster == 0){ 
				
				// tgl kalo dipaksa readonly jadnya bisa dihapus
				// lagian LCL mungkin saja tglnya boleh berbeda
				
                readonlyField = true;
                headerPanel.find(".auto-complete .add-button").hide(); 
                headerPanel.find("[name=selAirSea],[name=selWarehouseKey]").not("[attr-header=\"true\"]").addClass("force-readonly"); 
            }else{ 
                headerPanel.find(".auto-complete .add-button").show(); 
                headerPanel.find("[name=selAirSea],[name=selWarehouseKey]").not("[attr-header=\"true\"]").removeClass("force-readonly");  
            }
            
            // code readonly sementara saja. harusnya tergantung settingan db
            headerPanel.find("input").not("[name=jobOrderCode], [name=trDate],[name=salesName],[name=code],[name=podName],[attr-header=\"true\"]").prop("readonly",readonlyField);    
//            headerPanel.find(".input-date").prop("disabled",readonlyField);
            headerPanel.find("[name=containerNumber]").prop("disabled",readonlyField);
            
            headerPanel.find("[name=selAirSea],[name=selWarehouseKey]").not("[attr-header=\"true\"]").attr('disabled', readonlyField);
             
            
            // select jobtype gk boleh disabled agar bisa divalidasi ulang di class
            //headerPanel.find("select").prop("disabled",readonlyField);
            
    }

    this.updateCustomerInformation = function updateCustomerInformation(){ 
            
            // update salesman
            
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php', 
                    async : false,
                    data: "action=getSalesman&pkey=" + tabObj.find("[name=hidCustomerKey]").val(),  
                    beforeSend:function (xhr){ 
                        tabObj.find("[name=hidSalesKey]").val(0);
                        tabObj.find("[name=salesName]").val("");
                    },
                success: function (data) {  
                        data = parseJSON(data); 
                        if(data.length == 0)return;  
                        
                       tabObj.find("[name=hidSalesKey]").val(data.pkey);
                       tabObj.find("[name=salesName]").val(data.name);
                         
                    }  
                });
            
    
    }

        this.updateFlag = function updateFlag() {
			
			var hidVesselKey = tabObj.find("[name=hidVesselKey]");  
			if(hidVesselKey.length == 0) return ; 
			
            var vesselkey = hidVesselKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + vesselkey; 
            $.ajax({
                type: "GET",
                url:  'ajax-vessel.php',
                async : false,
                beforeSend:function (xhr){
                    
                },
                data: ajaxData,
                success: function (data) {
                    if (!data) return;
                    data = JSON.parse(data);

                    if(vesselkey == null || data.length <= 0) {
                        tabObj.find("[name=flag]").val("");
                    } else {
                        tabObj.find("[name=flag]").val(data[0]['flag']);
                    }
                    
                }
            });  
        }

    this.updateNotifyParty = function updateNotifyParty()
        {
            var hidNotifyPartyKey = tabObj.find("[name=hidNotifyPartyKey]");  
			if( hidNotifyPartyKey.length == 0) return ; 
			
            var consigneekey = hidNotifyPartyKey.val();

            var ajaxData = "action=getDataRowById&pkey=" + consigneekey; 
            $.ajax({
                type: "GET",
                url:  'ajax-consignee.php',
                async : false,
                beforeSend:function (xhr){
                    
                },
                data: ajaxData,
                success: function (data) {
                    if (!data) return;
                    data = JSON.parse(data);

                    if(consigneekey == null || data.length <= 0) {
                        tabObj.find("[name=notifyPartyAddress]").val("");
                    } else {
                        tabObj.find("[name=notifyPartyAddress]").val(data[0]['address']);
                    }
                    
                }
            });  
        }

        this.updateOverwrite  = function updateOverwrite(obj){ 
			var chkVal = obj.val();
			var attrName = obj.closest("div").attr("rel");
		   
			if(chkVal == 1){
				tabObj.find(".overwrite-" + attrName).show();      
				tabObj.find(".non-overwrite-" + attrName).hide();      	
			}else{
				tabObj.find(".overwrite-" + attrName).hide();      
				tabObj.find(".non-overwrite-" + attrName).show();       
			}
        }  
    
    this.rebindEl = function rebindEl(){    

        bindAutoCompleteForTransactionDetail('commodityName[]', objAndValueForDetailCommodityAutoComplete, 'ajax-commodity.php?action=searchData');  

    } 
    
    this.loadOnReady = function loadOnReady() { 

        tabObj.find("[name=chkIsOverwriteNotifyParty]").change(function(){thisObj.updateOverwrite($(this))}); 
        tabObj.find("[name=chkIsOverwriteNotifyParty]" ).change();
        
        tabObj.find("[name=hidVesselKey]").change(function(){thisObj.updateFlag()});
    
        tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 
        tabObj.find("[name=selAirSea]").change(function () { thisObj.updateAirOrSea(); });

        if (!data['volumeDetail'] || data['volumeDetail'].length == 0)
            addNewTemplateRow("volume-row-template",null,null,thisObj.rebindEl);
        
        if (!data['commodityDetail'] || data['commodityDetail'].length == 0)
            addNewTemplateRow("commodity-row-template",null,null,thisObj.rebindEl);

        thisObj.updateAirOrSea();
        thisObj.updateFieldfForLCLDetail();
        thisObj.updateFlag();

        thisObj.rebindEl();  
    
    }    

}
