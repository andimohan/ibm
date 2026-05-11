function AREmployee(tabID,data,varConstant){
    
    var thisObj = this;
    var tabObj = $("#" + tabID);   

    this.tabID = tabID;    

    this.calculateTotal = function calculateTotal(){

        var totalPayment = 0; 
        tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseFloat(unformatCurrency($(this).val())) || 0; })   
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();

    }

    this.resetDetails = function resetDetails(){ 
        clearAllRows(tabObj.find(".mnv-transaction"));
        clearAllRows(tabObj.find(".mnv-downpayment"));
        clearAllRows(tabObj.find(".mnv-payment-method"));

        addNewTemplateRow("payment-method-row-template");  

        thisObj.updateVoucher(); 
        thisObj.calculateTotal(); 
    }

    this.updateVoucher = function updateVoucher(){ 
		
		// kalo gk pake voucher, gk usah
		if(!varConstant.ADV_FINANCE) return;
		
        var employeekey = tabObj.find("[name=hidEmployeeKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name='selVoucher[]']");
            
        var ajaxData = "action=getAvailableVoucher&employeekey=" + employeekey;  
    
        $.ajax({
            type: "GET",
            url:  'ajax-cash-bank.php',
            async : false,
            beforeSend:function (xhr){
                selVoucherObj.each(function(){  $('option', $(this)).remove();  }) 
            },
            data: ajaxData,
            success: function(data){ 
                // update combobox voucher 
                if(!data) return; 
                data = JSON.parse(data); 
                var selectOpt = data;
                reInsertSelectBox(selVoucherObj,selectOpt, {"key" : "pkey", "label" : "voucherlabel", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 
    } 
    
    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
        thisObj.calculateTotal(); 
    }

     this.onChangeVoucher = function onChangeVoucher(obj){
        var amount = obj.find('option:selected').attr('rel-amount') || 0; 
        obj.closest(".transaction-detail-row").find("[name=\"paymentMethodValue[]\"]").val(amount).change().blur(); 
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
        thisObj.calculateTotal(); 
    }
    
    this.rebindEl = function rebindEl(){
        // utk voucher kepake, biar bisa auto add new row
        var tablePaymentMethodDetail = tabObj.find(".mnv-payment-method");   
        bindEl(tablePaymentMethodDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
        bindEl(tablePaymentMethodDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
    }

    this.loadOnReady = function loadOnReady(){ 
        
        thisObj.rebindEl(); 
        
        bindEl(tabObj.find("[name=\"selVoucher[]\"]"),'change',function(){thisObj.onChangeVoucher($(this));});

    }

}