function BankReconsiliation(tabID, data,varConstant){   
        var thisObj = this;
        var tabObj = $("#" + tabID);  

        this.tabID = tabID;    
        this.tablekey = varConstant.TABLEKEY;   

        var objAndValue = new Array; 
        objAndValue.push({object:'hidVoucherKey[]', value :'pkey'}); 
        objAndValue.push({object:'hidCurrencyKey[]', value :'currencykey'}); 	   
//        objAndValue.push({object:'voucherCode[]', value: 'code' }); 	   // gk boleh dimasukin, karena dihandle manual
        objAndValue.push({object:'refCode[]', value: 'refcode' }); 	   
        objAndValue.push({object:'trDetailDate[]', value :'trdate',  type: 'date'}); 	   
        objAndValue.push({object:'trDetailDesc[]', value :'trdesc'}); 	   
        objAndValue.push({object:'currency[]', value: 'currencyname' });
        objAndValue.push({object:'debit[]', value: 'amount', type: 'number' });  
        objAndValue.push({object:'credit[]', value: 'amount', type: 'number' }); 	
    

        var objAndValueForDetailAutoComplete = objAndValue;	
    
            
     this.updateDetail = function updateDetail(target,objAndValue,ui){ 
          
            var detailRow = $(target).closest(".transaction-detail-row");
  
            for(i=0;i<objAndValue.length;i++){    
                 
                if(objAndValue[i].object == 'debit[]' ){
                    var amount =  ui.item[objAndValue[i].value];
                    if (amount < 0 ) amount = 0; 
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(amount).blur(); 
                }else if(objAndValue[i].object == 'credit[]'){ 
                     var amount =  ui.item[objAndValue[i].value];
                     if (amount > 0 )  amount = 0; 
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(Math.abs(amount)).blur();  
                }else{
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();     
                }
                
            } 

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"voucherCode[]\"]").first().val(ui.item['value']);  
 
        }  
        
     
    this.calculateTotal = function calculateTotal(obj){
               
            var amountDebit = 0;
            var amountCredit = 0; 
		
            var objBeginingBalance = tabObj.find("[name='beginingBalance']");
            beginingBalance = parseFloat(unformatCurrency(objBeginingBalance.val()));
 
            tabObj.find("[name='chkPick[]']").not(":disabled").each(function (index) {

				var balanceDetail = $(this).closest(".div-table-row").find("[name='detailBalance[]']");
				
				if ($(this).val() != 1 ) {
					balanceDetail.val(0);
					return;	
				} 
                

                var objAmountDebit = $(this).closest(".div-table-row").find("[name='debit[]']");
                amountDebit += parseFloat(unformatCurrency(objAmountDebit.val())) || 0;
                
                var objAmountCredit = $(this).closest(".div-table-row").find("[name='credit[]']");
                amountCredit += parseFloat(unformatCurrency(objAmountCredit.val())) || 0;
				 
				
				balanceDetail.val(beginingBalance + amountDebit - amountCredit).blur();
            });
        
            
            tabObj.find("[name='debitAmount']").val(amountDebit).blur();   
            tabObj.find("[name='creditAmount']").val(amountCredit).blur();   
             
//            var toNegative = -Math.abs(amountCredit);//convert absolute to negative from val positive
        
            var totalDebitCredit = amountDebit + (amountCredit * -1); //total debit and credit
        
            var endingBalance = beginingBalance + totalDebitCredit; //total ending balance
        
            tabObj.find("[name='endingBalance']").val(endingBalance).blur();   
              
    }

    // this.changeBeginingBalance = function changeBeginingBalance(obj) {
    //     var beginingBalance = tabObj.find("[name='beginingBalance']").val();
    // }

     this.resetDetails = function resetDetails() {  
        clearAllRows(tabObj.find(".mnv-transaction"));
        thisObj.rebindEl();
    }

     this.afterRemoveRowHandler = function afterRemoveRowHandler(){ 
            thisObj.calculateTotal(); 
    }
    
    this.importData = function importData() {
//        loadOverlayScreen({ content: _LOADING_TEMPLATE_ });
        thisObj.activeAjaxConnections = 0;

        //get value priode
        var startdate = tabObj.find("[name=trStartDatePriode]").val();
        var dateParam = "";
        if (startdate ) {
            dateParam = "&startdate=" + startdate ;
        }

        var ajaxData = "action=searchDataForBankReconsiliation&coakey=" + tabObj.find("[name=hidCoaKey]").val() + dateParam;

        $.ajax({
            type: "GET",
            url: "ajax-cash-bank.php",
            beforeSend: function (xhr) {
                clearAllRows(tabObj.find(".mnv-transaction"),true,true);
                thisObj.activeAjaxConnection++;
            },
            data: ajaxData,
            success: function (data) {
                
                if (!data) {
                    decreaseActiveAjaxConnections(thisObj); 
                    return;
                }
              
                
                var data = JSON.parse(data);

                var i;
                for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];
                    arrPostValue.push({ "selector": "hidVoucherKey",  "value": data[i].pkey });
                    arrPostValue.push({ "selector": "hidCurrencyKey",  "value": data[i].currencykey });
                    arrPostValue.push({ "selector": "currency",  "value": data[i].currencyname });
                    arrPostValue.push({ "selector": "voucherCode",  "value": data[i].code });
                    arrPostValue.push({ "selector": "refCode",  "value": data[i].refcode });
                    arrPostValue.push({ "selector": "trDetailDate",  "value": moment(data[i].trdate).format(_DATE_FORMAT_) });
                    arrPostValue.push({ "selector": "trDetailDesc", "value": data[i].trdesc });
                     
                    if (data[i].credittype < 0 )  
                        arrPostValue.push({ "selector": "credit",  "value": data[i].amount });
                    else  
                         arrPostValue.push({ "selector": "debit",  "value": data[i].amount });
 
                
                    addNewTemplateRow("bank-reconsiliation-row-template", JSON.stringify(arrPostValue));  
                }

                 thisObj.rebindEl(); 

                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();
 
                decreaseActiveAjaxConnections(thisObj); 
                
                tabObj.find("[name='chkPick-master']").val(1).change();
            },
            error: function (xhr, errDesc, exception) { 
                 decreaseActiveAjaxConnections(thisObj); 
            }
        });
    }
    
      this.updateStartingBalance = function updateStartingBalance(){
   
            var ajaxData = "action=getLastedReconsile&coakey=" + tabObj.find("[name=hidCoaKey]").val();

            $.ajax({
                type: "GET",
                url:  'ajax-bank-reconsiliation.php',  
                asyn: false,
                data: ajaxData, 
                success: function(data){    

                    if (!data) return;

                    var data = JSON.parse(data);  
                    data = data[0];
                    var balance = (data == undefined ) ? 0 : data.endingbalance;
                    tabObj.find("[name='beginingBalance'] ").val(balance).change().blur();

                }  
            }); 
                
  }

     this.onChangeChk = function onChangeChk(){    
        thisObj.calculateTotal();
    }

     this.onChangeBeginingBalance = function onChangeBeginingBalance(){    
        thisObj.calculateTotal();
        thisObj.updateStartingBalance();
    }

    this.rebindEl = function rebindEl() {      
        bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        // bindEl(tabObj.find("[name='amount[]'] "), 'change', function () { thisObj.calculateTotal(); });     
        bindEl(tabObj.find("[name='beginingBalance']"), 'change', function () { thisObj.calculateTotal(); });     
        bindAutoCompleteForTransactionDetail('voucherCode[]', objAndValueForDetailAutoComplete,'ajax-cash-bank.php?action=searchData&statuskey=(2,3)&isreconsile=0&coakey='+tabObj.find("[name=hidCoaKey]").val()+'&currencykey='+tabObj.find("[name=selCurrency]").val(),thisObj.updateDetail); 
      }
         
    this.loadOnReady = function loadOnReady() {  
        
        if (!data['rsDetail'] || data['rsDetail'].length <= 0)
        addNewTemplateRow("bank-reconsiliation-row-template",null,null,thisObj.rebindEl);
        
//         tabObj.find("[name='beginingBalance'] ").change(function () { updateBeginingBalance(this,thisObj.onChangeBeginingBalance); }); 
//        tabObj.find("[name='trStartDatePriode'],[name='trEndDatePriode'] ").change(function () { thisObj.onChangeBeginingBalance }); 
        tabObj.find(".input-date").datepicker().on("change", function() {thisObj.onChangeBeginingBalance()});
        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
        tabObj.find("[name=btnImport]").on('click', function () { thisObj.importData(); });
        thisObj.calculateTotal();
                     tabObj.find("[name=selCurrency]").change(function() {
            thisObj.rebindEl()
        });
        

        tabObj.find("[name=hidCoaKey]").change(function() {
            thisObj.rebindEl()
        });
                   
        tabObj.find("[name='chkPick-master']").val(1).change(); 
        thisObj.rebindEl();
    
    }
}
