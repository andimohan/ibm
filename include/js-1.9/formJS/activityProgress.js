function ActivityProgress(tabID, varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     

    var id = tabObj.find("[name=hidId]").val();

    var objAndValue = new Array;
	objAndValue.push({object:'hidActivityKey[]', value :'pkey'});    
	objAndValue.push({object:'activityName[]', value :'name'});  
    var objAndValueForDetailAutoComplete = objAndValue; 

    this.onChangeDetail = function onChangeDetail() 
    {
        $( "#dialog-message" ).html("Apakah Anda ingin import data aktivitas ?<br>Semua detail transaksi akan dihapus jika Anda import data aktvitas.");
        $("#dialog-message").dialog({
             width: 300,
			modal: true,
			title:"Konfirmasi import data aktivitas", 
			close:function() {
				
			}, 
			open: function() {
				$(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
			}, 
			buttons : {
			    OK : function (){     
                    thisObj.resetDetails(); 
                    thisObj.importData();
					$( this ).dialog( "close" );
				},
				Cancel : function (){  
					$( this ).dialog( "close" );
				}
			} 
        });
    
    }

    this.importData = function importData()
    {
        thisObj.activeAjaxConnections = 0;

        $.ajax({
            type: "GET",
            url: 'ajax-template-activity.php',
            beforeSend: function (xhr) {
                // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++;
            },
            data: "action=searchData&statuskey=1",
            success: function (data) {
                var data = JSON.parse(data);  
                var i;
                for (i = 0; i < data.length; i++) {
                    var arrPostValue = []; 

                    arrPostValue.push({"selector":"hidActivityKey", "value":data[i].pkey});
                    arrPostValue.push({"selector":"activityName", "value":data[i].value});

                    addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue)); 
                }

                thisObj.rebindEl(); 
            },
            error: function(xhr, errDesc, exception) {
                decreaseActiveAjaxConnections(thisObj); 
            }
        });

    }

    this.updateJobType = function updateJobType(){
           // kalo LCL gk ad supplier dan conginee 
            var selContainerObj = tabObj.find("[name=selContainerType]");  
            var fclOnlyObj = tabObj.find(".fcl-only");
            var lclOnlyObj = tabObj.find(".lcl-only");
            var supplierDetailRow = tabObj.find(".supplier-row").not(".row-template");
            
            var containerType = selContainerObj.val();  
            if (containerType == varConstant.EMKL.container.lcl ){ 
                lclOnlyObj.show();
                fclOnlyObj.hide(); 
                
                $(".fcl-readonly").attr("readonly", false);
            }else{
                lclOnlyObj.hide();
                fclOnlyObj.show();
                
                $(".fcl-readonly").attr("readonly", true);
            }  
    }

    this.updateFromJobOrder = function updateFromJobOrder(){   
                var pkey = tabObj.find("[name=hidJobOrderKey]").val();
                 
                $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-job-order.php', 
                    data: "action=getDataRowById&pkey=" + pkey ,  
                }).done(function( data ) { 
                      
                    data = JSON.parse(data) ; 
                     
                    if(data.length == 0){ 
                        alert(phpErrorMsg[213])
                        return;
                    }
                     
                    data = data[0];
                    
                    //tabObj.find("[name=trDate]").val(moment(data.trdate).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=selTypeOfJob]").val(data.jobtypekey);
                    tabObj.find("[name=selAirSea]").val(data.transportationtypekey);
                    tabObj.find("[name=selContainerType]").val(data.loadcontainertypekey).change();  
                    tabObj.find("[name=containerName]").val(decodeHTMLEntities(data.containername));
                    tabObj.find("[name=bookingNumber]").val(data.bookingnumber);
                    tabObj.find("[name=shipperName]").val(data.customername);
                    tabObj.find("[name=poNumber]").val(data.ponumber);
                    tabObj.find("[name=mblNumber]").val(data.mblnumber); 
                    tabObj.find("[name=containerNumber]").val(data.containernumber); 
                    tabObj.find("[name=etdPol]").val(moment(data.etdpol).format(_DATE_FORMAT_)); 
                    tabObj.find("[name=etaPod]").val(moment(data.etapod).format(_DATE_FORMAT_));   
                    tabObj.find("[name=pol]").val(data.polname); 
                    tabObj.find("[name=pod]").val(data.podname); 
                    tabObj.find("[name=terminal]").val(data.terminalname);                     
                    tabObj.find("[name=depot]").val(data.depotname);                  
//                    tabObj.find("[name=location]").val(data.locationname);                    
//                    tabObj.find("[name=hidLocationKey]").val(data.locationkey);                                      
                    tabObj.find("[name=location]").val(data.stuffinglocation);           
                    updateComboboxReadonly(tabObj.find("[name=selTypeOfJob]"));
                    tabObj.find("[name=selTypeOfJob]").change();
                     
                });  
        }

    this.resetDetails = function resetDetails(){          
        clearAllRows($("#"+tabID)); 
    }
    
    this.updateDetail = function updateDetail(target,objAndValue,ui){
 
		var detailRow = $(target).closest(".transaction-detail-row");   
		for(i=0;i<objAndValue.length;i++){     
			//detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();    
		} 
    
		detailRow.find("[name=\"hidActivityKey[]\"]").first().val(ui.item['pkey']);  
		// harus handle manual utk obj autosearch
		detailRow.find("[name=\"activityName[]\"]").first().val(ui.item['value']);  

  	}

    this.rebindEl = function rebindEl(){   
        bindAutoCompleteForTransactionDetail('activityName[]', objAndValueForDetailAutoComplete, 'ajax-template-activity.php?action=searchData&statuskey=1&searchField=name',thisObj.updateDetail);
    }

    this.loadOnReady = function loadOnReady() {

        tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJobType(); }); 

        tabObj.find("[name=btnImport]").on('click', function() { thisObj.onChangeDetail(); });
        
        
        thisObj.updateJobType();
        thisObj.rebindEl();
    }
}