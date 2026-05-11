function AREmployeePayment(tabID, rs,varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;  
    this.tablekey = varConstant.tablekey;   
    this.rs = (rs.length > 0) ? rs[0] : null;
     
    var  objAndValue = new Array;
 	objAndValue.push({object:'hidARKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'}); 	
    objAndValue.push({object:'jobOrderCode[]', value :'reftranscode2'}); 	
    objAndValue.push({object:'customerName[]', value :'customername'}); 
    objAndValue.push({object:'refDate[]', value :'refdate', type : 'date'}); 	
    objAndValue.push({object:'arAmount[]', value :'amount'}); 	
	objAndValue.push({object:'outstanding[]', value :'outstanding'});
	objAndValue.push({object:'amount[]', value :'outstanding'});
    var objAndValueForDetailAutoComplete = objAndValue;
     
    this.resetDetails = function resetDetails(){  
       clearAllRows($("#"+tabID));  

        thisObj.updateVoucher(); 
       thisObj.calculateTotal(); 
    }

    this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);
 
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
                
            }

            detailRow.find("[name=\"arCode[]\"]").first().val(ui.item['code']); 
    }

    this.calculateTotal = function calculateTotal(){

        var amount = 0;

        tabObj.find("[name='chkPick[]']").not(":disabled").each(function(){   

            if ($(this).val() != 1 )  return;

            var objAmount = $(this).closest(".div-table-row").find("[name='amount[]']");

            amount += parseInt(unformatCurrency(objAmount.val())) || 0;

        })     

        var totalPaid = amount;
        tabObj.find("[name='totalRecieved']").val(totalPaid).blur(); 
 
        var total = amount; 
        tabObj.find("[name='total']").val(total).blur(); 



        var totalPayment = 0; 
        tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseInt(unformatCurrency($(this).val())) || 0; })   
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();
         
        var balance = totalPayment - total;  
        tabObj.find("[name='balance']").val(balance).blur(); 
        
    }

    this.importData = function importData(){ 

        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
 
        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
        
        var ajaxData = "action=searchData&employeekey=" + tabObj.find("[name=hidEmployeeKey]").val()+dateParam;  
        
        $.ajax({
            type: "GET",
            url:  'ajax-ar-employee.php',
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
                            arrPostValue.push({"selector":"hidARKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"arCode", "value":data[i].code}); 
                            arrPostValue.push({"selector":"refCode", "value":data[i].refcode});
                            arrPostValue.push({"selector":"jobOrderCode", "value":data[i].reftranscode2});  
                            arrPostValue.push({"selector":"customerName", "value":data[i].customername});   
                            arrPostValue.push({"selector":"refDate", "value":moment(data[i].refdate).format(_DATE_FORMAT_)}); 
                            arrPostValue.push({"selector":"arAmount", "value":data[i].amount}); 
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
                thisObj.updateVoucher(); 	

                 thisObj.rebindEl(); 
            } 	 

    }

     
    this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    }

     this.updateVoucher = function updateVoucher(){ 
		
		// kalo gk pake voucher, gk usah
		if(!varConstant.ADV_FINANCE) return;
		
        var customerkey = tabObj.find("[name=hidEmployeeKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name='selVoucher[]']");
            
        var ajaxData = "action=getAvailableVoucher&creditType=1&employeekey=" + customerkey;  
      
         $.ajax({
            type: "GET",
            url:  'ajax-cash-bank.php',
            async : false,
            beforeSend:function (xhr){
                  selVoucherObj.each(function(){  $('option', $(this)).remove();  }) 
            },
            data: ajaxData,
            success: function(data){ 
                        // update combobox services 
                        if(!data) return; 
                        data = JSON.parse(data); 
                        var selectOpt = data;
                        reInsertSelectBox(selVoucherObj,selectOpt, {"key" : "pkey", "label" : "voucherlabel", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 
    } 
     
    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
        thisObj.calculateTotal();  

        //thisObj.updateVoucher();
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
     thisObj.calculateTotal(); 
    }
  
    this.rebindEl = function rebindEl(){ 
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='amount[]']"),'change', function() { thisObj.calculateTotal(); });  
        bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete,'ajax-ar-employee.php?action=searchData&employeekey=' + tabObj.find("[name=hidEmployeeKey]").val(),thisObj.updateDetail); 
   
    }

    
    this.onChangeVoucher = function onChangeVoucher(obj){
        var amount = obj.find('option:selected').attr('rel-amount') || 0; 
        obj.closest(".transaction-detail-row").find("[name=\"paymentMethodValue[]\"]").val(amount).change().blur(); 
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});
    }
        this.loadOnReady = function loadOnReady(){ 
 
	   tabObj.find("[name=selTermOfPayment]" ).change();  

        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
        tabObj.find("[name=btnAddPayment]").bind( "click", function(event) {  thisObj.onCLickAddPayment(); }) 
         
        tabObj.find("[name='chkPick-master']").val(1).change();   
        thisObj.rebindEl(); 
         
    }
}
