function EmployeeCommission(tabID,varConstant) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    var overrideCommission = varConstant.overrideCommission || false;

    this.tabID = tabID;

    var objAndValue = new Array;
    objAndValue.push({ object: 'hidJobOrderKey[]', value: 'pkey' });
    objAndValue.push({ object: 'jobOrderCode[]', value: 'value' });
    objAndValue.push({ object: 'totalBuying[]', value: 'totalbuying' });
    objAndValue.push({ object: 'totalSelling[]', value: 'totalselling' });
    objAndValue.push({ object: 'taxValue[]', value: 'taxvalue' });
    objAndValue.push({ object: 'purchaseRefund[]', value: 'totalcommission' });
    objAndValue.push({ object: 'creditNote[]', value: 'totalcreditnote' });
    objAndValue.push({ object: 'debitNote[]', value: 'totaldebitnote' });
    objAndValue.push({ object: 'profit[]', value: 'profit' });
    var objAndValueForDetailAutoComplete = objAndValue;


    this.updateEmployeeInformation = function updateEmployeeInformation() {
        var pkey = tabObj.find("[name=hidEmployeeKey]").val();
//        console.log(pkey, 'pkey')
        $.ajax({
            type: "GET",
            url: 'ajax-employee.php',
            async: false,
            data: "action=getDataRowById&pkey=" + pkey,
            success: function (data) {
                    
                if (!data) return;

                var data = parseJSON(data);
                                
                data = data[0];
                tabObj.find("[name=commissionPercentage]").val(data.commissionpercentage).blur();
                tabObj.find("[name=targetProfit]").val(data.targetprofit).blur();
                tabObj.find("[name=targetMonthPeriod]").val(data.targetmonthperiod);

            }
        });

    }

 this.importData = function importData() {
        loadOverlayScreen({ content: _LOADING_TEMPLATE_ });
        thisObj.activeAjaxConnections = 0;

        var employeeKey = tabObj.find("[name=hidEmployeeKey]").val();
        var period = tabObj.find("[name=periodDate]").val();
        var endPeriod  = tabObj.find("[name=endPeriodDate]").val();
        

        var ajaxData = "action=generateDataEmployeeCommission&employeekey=" + employeeKey + "&period=" + period + "&endperiod=" + endPeriod;

        $.ajax({
            type: "GET",
            url: "ajax-employee-commission.php",
            beforeSend: function (xhr) {
                thisObj.resetDetails();
                thisObj.activeAjaxConnections++;
            },
            data: ajaxData,
            success: function (data) {            
                var data = parseJSON(data);

            if (
                !data || 
                data.length === 0 || 
                !data[0].detail || 
                data[0].detail.length === 0
            ) {
                decreaseActiveAjaxConnections(thisObj);
                addNewTemplateRow("detail-row-template");
                thisObj.rebindEl();
                return;
            }

                var detail = data[0].detail;

               var i;
               for (i = 0; i < detail.length; i++) {
                   var arrPostValue = [];
                   arrPostValue.push({ "selector": "hidJobOrderKey", "value": detail[i].pkey });
                   arrPostValue.push({ "selector": "jobOrderCode", "value": detail[i].value });
                   arrPostValue.push({ "selector": "totalBuying", "value": detail[i].totalbuying });
                   arrPostValue.push({ "selector": "totalSelling", "value": detail[i].totalselling });
                   arrPostValue.push({ "selector": "taxValue", "value": detail[i].taxvalue });
                   arrPostValue.push({ "selector": "purchaseRefund", "value": detail[i].totalcommission });
                   arrPostValue.push({ "selector": "creditNote", "value": detail[i].totalcreditnote });
                   arrPostValue.push({ "selector": "debitNote", "value": detail[i].totaldebitnote });
                   arrPostValue.push({ "selector": "profit", "value": detail[i].profit });

                   addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));
               }
                   
               tabObj.find(".inputnumber").change().blur();
               tabObj.find(".inputdecimal").change().blur();

                thisObj.calculateCommission();
                thisObj.rebindEl();
                
            },
            error: function (xhr, errDesc, exception) {
                decreaseActiveAjaxConnections(thisObj);
            },
            complete: function () {
        decreaseActiveAjaxConnections(thisObj);
    }
        });

    }

    this.onChangeEmployee = function onChangeEmployee(obj,event, ui){
            
				if (tabObj.find("[name=hidCurrentEmployeeKey]" ).val() != ''){
					$( "#dialog-message" ).html("Merubah karyawan akan mereset detail transaksi.");
					$( "#dialog-message" ).dialog({
					  width: 300,
					  modal: true,
					  title:"Konfirmasi Perubahan Data Karyawan", 
					  open: function() {
						  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
					  },
					  close:function() {
							tabObj.find("[name=hidEmployeeKey]" ).val(tabObj.find("[name=hidCurrentEmployeeKey]" ).val());
							tabObj.find("[name=employeeName]" ).val(tabObj.find("[name=hidCurrentEmployeeName]" ).val());
                          	$(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));  
                            
					  },
					  buttons : {
						  OK : function (){  
						  		if (ui.item == null) { 
									clearAutoCompleteInput(obj,'hidEmployeeKey');	
									tabObj.find("[name=hidCurrentEmployeeKey]" ).val(''); 
									tabObj.find("[name=hidCurrentEmployeeName]" ).val(''); 
                                }else{
									tabObj.find("[name=hidCurrentEmployeeKey]" ).val(ui.item.pkey); 
									tabObj.find("[name=hidCurrentEmployeeName]" ).val(ui.item.value);   
								} 
								
                                thisObj.updateEmployeeInformation();
								thisObj.resetDetails(); 
                                thisObj.rebindEl();

                                addNewTemplateRow("detail-row-template");

								$( this ).dialog( "close" );
						  },
						  Cancel : function (){  
						  		$( this ).dialog( "close" );
						  }
					  },
					});	 
				}else{ 

					if (ui.item == null) {
						clearAutoCompleteInput(obj,'hidEmployeeKey');	
						tabObj.find("[name=hidCurrentEmployeeKey]" ).val(''); 
						tabObj.find("[name=hidCurrentEmployeeName]" ).val(''); 
					}else{ 
						tabObj.find("[name=hidCurrentEmployeeKey]" ).val(ui.item.pkey); 
						tabObj.find("[name=hidCurrentEmployeeName]" ).val(ui.item.value);     
					} 	
					  
                    thisObj.updateEmployeeInformation();
                    thisObj.rebindEl();
				} 	  
    }

    this.resetDetails = function resetDetails() {
        clearAllRows(tabObj.find(".mnv-transaction"));
    }

    this.calculateCommission = function calculateCommission() {
        var targetProfit = parseFloat(unformatCurrency(tabObj.find("[name=targetProfit]").val()));
        var commissionPercentage = parseFloat(unformatCurrency(tabObj.find("[name=commissionPercentage]").val()));

        var employeeProfit = 0;
        var commissionAmount = 0;
        var totalProfit = 0;
        tabObj.find("[name='profit[]']").each(function () {
            totalProfit += parseFloat(unformatCurrency($(this).val())) || 0;
        })

        employeeProfit = totalProfit - targetProfit;
        commissionAmount = employeeProfit * commissionPercentage / 100;

        if (totalProfit == 0) {
            employeeProfit = 0;
            commissionAmount = 0;
        }

        if (commissionAmount <= 0) {
            commissionAmount = 0;
        }

        tabObj.find("[name=totalProfit]").val(employeeProfit).blur();

        if (!overrideCommission) {
            tabObj.find("[name=totalCommission]").val(commissionAmount).blur();
        }
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateCommission();
    }

    this.updateDetail = function updateDetail(target, objAndValue, ui) {
        var detailRow = $(target).closest(".transaction-detail-row");
        for (i = 0; i < objAndValue.length; i++) {

            var field = objAndValue[i].object;
            var val = ui.item[objAndValue[i].value];

            // detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
            detailRow.find("[name='" + field + "']").val(val);
        }

        detailRow.find("[name=\"jobOrderCode[]\"]").first().val(ui.item['value']);
        thisObj.calculateCommission();

        tabObj.find(".inputnumber").change().blur();
        tabObj.find(".inputdecimal").change().blur();
    }

    this.rebindEl = function rebindEl() {
        
        var employeekey = tabObj.find("[name=hidEmployeeKey]").val();
        var period = tabObj.find("[name=periodDate]").val();
        var endPeriod = tabObj.find("[name=endPeriodDate]").val();

        bindAutoCompleteForTransactionDetail('jobOrderCode[]', objAndValueForDetailAutoComplete, 'ajax-employee-commission.php?action=searchJobOrderData&limit=25&employeekey=' + employeekey + '&period=' + period + '&endperiod=' + endPeriod);
    }

    this.loadOnReady = function loadOnReady(){
        
        tabObj.find("[name=periodDate],[name=endPeriodDate]").on('change', function () {
            thisObj.resetDetails(); 
            thisObj.rebindEl();
        });

        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });


        thisObj.rebindEl();


    }

}
