function APEmployeeCommissionPayment(tabID, rs){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
   
    var  objAndValue = new Array;
    objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
    var objAndValueForCostDetailAutoComplete  = objAndValue;  
    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidAPKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'});
    objAndValue.push({object:'refJOCode[]', value :'refcode2'});  
    objAndValue.push({object:'apAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    var objAndValueForDetailAutoComplete = objAndValue;
     
    this.resetDetails = function resetDetails(){  
       clearAllRows($("#"+tabID));  
       thisObj.calculateTotal(); 
    }

     
     this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);
 
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
                
            }

            detailRow.find("[name=\"apCode[]\"]").first().val(ui.item['code']); 
    }
   

     this.calculateTotalCost = function calculateTotalCost(){
        var totalCost = 0; 
        tabObj.find("[name='costAmount[]']").each(function() { totalCost += parseInt(unformatCurrency($(this).val())) || 0;   })
        tabObj.find("[name='totalCost']").val(totalCost).blur(); 

        return totalCost;
    } 

    this.calculateTotal = function calculateTotal(){

        var amount = 0;      
        var totalPPH = 0; 
        var totalDiscount = 0;
        var totalCost = 0;
        var totalAREmployee = 0;


        tabObj.find("[name='chkPick[]']").not(":disabled").each(function(){   

            if ($(this).val() != 1 )  return;

            var objAmount = $(this).closest(".div-table-row").find("[name='amount[]']");
            var objDiscount = $(this).closest(".div-table-row").find("[name='discount[]']");
            var objPph = $(this).closest(".div-table-row").find("[name='taxPPH[]']"); 

            amount += parseInt(unformatCurrency(objAmount.val())) || 0;
            totalDiscount += parseInt(unformatCurrency(objDiscount.val())) || 0;
            totalPPH += parseInt(unformatCurrency(objPph.val())) || 0;

        })     

        var totalPaid = amount + totalDiscount;
        tabObj.find("[name='totalPaid']").val(totalPaid).blur(); 


        tabObj.find("[name='totalDiscount']").val(totalDiscount).blur(); 
        tabObj.find("[name='pph23']").val(totalPPH).blur(); 
 
        var totalCost = thisObj.calculateTotalCost();
        var totalAREmployee = thisObj.calculateTotalAREmployee();
        var total = amount-totalPPH-totalAREmployee+totalCost; 
        tabObj.find("[name='total']").val(total).blur(); 



        var totalPayment = 0; 
        tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseInt(unformatCurrency($(this).val())) || 0; })   
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();
         
        var balance = totalPayment - total;  
        tabObj.find("[name='balance']").val(balance).blur(); 
        
        /*  if (thisObj.rs && thisObj.rs.statuskey > 1){ 
                autoAddNewRowTemplate(tabObj.find("[name='paymentMethodValue[]']"),"payment-method-row-template");
                autoAddNewRowTemplate(tabObj.find("[name='costAmount[]']"),"cost-row-template");
                thisObj.rebindCost();
                autoAddNewRowTemplate(tabObj.find("[name='downpaymentAmount[]']"),"downpayment-row-template");
                thisObj.rebindDownpayment();
          }*/
    }
     
        
    this.rebindCost = function rebindCost(){  
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForCostDetailAutoComplete,'ajax-cost-cash-out.php?action=searchData'); 
    } 

    this.importData = function importData(){ 

        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
          
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 

        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&datetype=jobsdate&startdate="+startdate+"&enddate="+enddate;
        }
        
        var ajaxData = "action=searchData&employeekey=" + tabObj.find("[name=hidEmployeeKey]").val()+dateParam;
        
        $.ajax({
            type: "GET",
            url:  'ajax-ap-employee-commission.php',
            beforeSend:function (xhr){ 
                // hanya reset yg di table transaksi,  cost dan payment method gk perlu direset
                clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++; 
            },
            data: ajaxData,
            success: function(data){  
                    var data = JSON.parse(data);  
                    var i;
                    for(i=0;i<data.length;i++){  
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidAPKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"apCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"refCode", "value":data[i].refcode}); 
                            arrPostValue.push({"selector":"refJOCode", "value":data[i].refcode2});  
                            arrPostValue.push({"selector":"apAmount", "value":data[i].amount}); 
                            arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding}); 
                            arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                          
                            //var tax = ( data[i].autotax == 1 && thisObj.employeeTax > 0) ?  data[i].amount *  thisObj.employeeTax / 100 : 0;  
                            //arrPostValue.push({"selector":"taxPPH", "value":tax}); 
                         
                            addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                    }

                   thisObj.rebindEl(); 

                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

                decreaseActiveAjaxConnections(thisObj); 
                tabObj.find("[name='chkPick-master']").val(1).change();  
            } ,
             error: function(xhr, errDesc, exception) {
                 decreaseActiveAjaxConnections(thisObj); 
            }
        }); 
    }
 

    this.updateEmployeeInformation = function updateEmployeeInformation(event, ui){
            var obj = this; 
            if (tabObj.find("[name=hidCurrentEmployeeKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah pemasok akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Pemasok", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidEmployeeKey]" ).val(tabObj.find("[name=hidCurrentEmployeeKey]" ).val());
                        tabObj.find("[name=employeeName]" ).val(tabObj.find("[name=hidCurrentEmployeeName]" ).val());
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        thisObj.rebindEl(); 
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
 
                            thisObj.resetDetails();
                            thisObj.updateTotalOutstandingAREmployee(); 
                            thisObj.updateAREmployee();
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

                thisObj.rebindEl(); 
                thisObj.updateTotalOutstandingAREmployee(); 
                thisObj.updateAREmployee();
            } 	 

        thisObj.updateTax();
    }

    this.updateTax = function updateTax(){
        /* $.ajax({
                type: "GET",
                url:  'ajax-employee.php', 
                async : false,
                data: 'action=getTaxInformation&pkey=' + tabObj.find("[name=hidEmployeeKey]").val() , 
                success: function(data){  
                    thisObj.employeeTax = 0;
                    
                    if (!data) return;
                    
                    var data = JSON.parse(data);  
                    thisObj.employeeTax = data.taxpercentage;      
                }  
            }); */
    }

    this.updateTotalOutstandingAREmployee = function updateTotalOutstandingAREmployee() { 
        
        var employeekey = tabObj.find("[name=hidEmployeeKey]").val();

        $.ajax({
            type: "GET",
            url:  'ajax-ar-employee.php',
            async : false,
            beforeSend:function (xhr){  
                tabObj.find(".outstanding-currency").text($("[name=selCurrency] option:selected").first().text()); 
                tabObj.find(".outstanding-ar-employee").text(0).blur();
            },
            data: "action=getTotalOutstanding&employeekey=" + employeekey, 
        }).done(function( data ) {
            
			data = parseJSON(data);
			if(data.length == 0) return;
			
            data = data[0]; 
            tabObj.find(".outstanding-ar-employee").text(data.totaloutstanding).blur();
        }); 
    }

    this.updateAREmployee = function updateAREmployee(){
    
        var employeekey = tabObj.find("[name=hidEmployeeKey]").val() || 0;  
        var selAREmployeeObj = tabObj.find("[name='selAREmployee[]']");

        var ajaxData = "action=getAvailableAR&employeekey=" + employeekey;

        $.ajax({
            type: "GET",
            url:  'ajax-ar-employee.php',
            async : false,
            beforeSend:function (xhr){
                selAREmployeeObj.each(function(){  $('option', $(this)).remove();  });
            },
            data: ajaxData,
            success: function(data){ 
                // update combobox services 
                if(!data) return; 
                data = JSON.parse(data); 
                var selectOpt = data;
                reInsertSelectBox(selAREmployeeObj,selectOpt, {"key" : "pkey", "label" : "value", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 

    }

     this.onChangeAREmployee = function onChangeAREmployee(obj){
        var amount = obj.find('option:selected').attr('rel-amount') || 0; 
        obj.closest(".transaction-detail-row").find("[name=\"arEmployeeAmount[]\"]").val(amount).change().blur(); 
        bindEl(tabObj.find("[name=\"selAREmployee[]\"]"),'change',function(){thisObj.onChangeAREmployee($(this));});
    }
     
    this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    }

    this.calculateTotalAREmployee = function calculateTotalAREmployee(){
        var totalAREmployee = 0; 
        tabObj.find("[name='arEmployeeAmount[]']").each(function() { totalAREmployee += parseFloat(unformatCurrency($(this).val())) || 0;   })
        tabObj.find("[name='totalAREmployee']").val(totalAREmployee).blur(); 

        return totalAREmployee;
     }

    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
        thisObj.calculateTotal();   
        thisObj.rebindCost();
        thisObj.calculateTotalAREmployee();  
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
        thisObj.calculateTotal();
        thisObj.calculateTotalAREmployee();   
    }
  
    this.rebindEl = function rebindEl(){ 
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='discount[]'], [name='amount[]'], [name='taxPPH[]']"),'change', function() { thisObj.calculateTotal(); });  
        bindAutoCompleteForTransactionDetail('apCode[]',  objAndValueForDetailAutoComplete,'ajax-ap-employee-commission.php?action=searchData&employeekey=' + tabObj.find("[name=hidEmployeeKey]").val(),thisObj.updateDetail); 
        
        var tableCostDetail = tabObj.find(".mnv-cost");   
        bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
        bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
   
        var tablePaymentMethodDetail = tabObj.find(".mnv-ar-employee");   
        bindEl(tablePaymentMethodDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'ar-employee-row-template'); });
        bindEl(tablePaymentMethodDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'ar-employee-row-template'); });

        thisObj.rebindCost();
    }

    this.loadOnReady = function loadOnReady(){ 
 
	   tabObj.find("[name=selTermOfPayment]" ).change();  

        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
        tabObj.find("[name=btnAddPayment]").bind( "click", function(event) {  thisObj.onCLickAddPayment(); }) 
         
        tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]");
            
            dateObj.removeClass("force-readonly");
             
            dateObj.datepicker((checked) ? "enable" : "disable"); 
            
            if(!checked) dateObj.addClass("force-readonly");
         })  


        tabObj.find("[name=chkDatePeriod]").change(); 
        
        tabObj.find("[name=trStartDate], [name=trEndDate]").bind( "change",function() { 
            var trStartDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val()));
            var trEndDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val()));
               
            if (trStartDate > trEndDate) 
                tabObj.find("[name=trEndDate]").val(tabObj.find("[name=trStartDate]").val()); 
            
		});

        bindEl(tabObj.find("[name=\"selAREmployee[]\"]"),'change',function(){thisObj.onChangeAREmployee($(this));});
         
        thisObj.updateTotalOutstandingAREmployee();
        $(".outstanding-ar-employee").blur();
        
        addNewTemplateRow("ar-employee-row-template");

        tabObj.find("[name='chkPick-master']").val(1).change();  
        addNewTemplateRow("cost-row-template");  
        thisObj.rebindEl(); 
         
    }
}
