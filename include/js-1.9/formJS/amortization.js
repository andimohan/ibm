function Amortization(tabID, uploadFolder, rsImage) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    var objAndValue = new Array;
	objAndValue.push({object:'hidPrepaidExpenseKey[]', value :'pkey'});  
	objAndValue.push({object:'hidItemKey[]', value :'costkey'});  
	objAndValue.push({object:'itemName[]', value :'servicename'});  
	objAndValue.push({object:'amount[]', value :'priceinunit'});  
    var objAndValueForDetailAutoComplete = objAndValue; 

    this.tabID = tabID;

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    }

    this.calculateTotal = function calculateTotal()
    {

        var total = 0;
        tabObj.find("[name='amount[]']").each(function () {
            total += parseInt(unformatCurrency($(this).val())) || 0;
        })

        tabObj.find("[name='total']").val(total).blur();

    }
    
    this.updateDetail = function updateDetail(target,objAndValue,ui){   
        var detailRow = $(target).closest(".transaction-detail-row"); 
            
        for(i=0;i<objAndValue.length;i++)   
            detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  

        // harus handle manual utk obj autosearch
        var row = detailRow.find("[name=\"prepaidExpenseCode[]\"]").first().val(ui.item['value']);  
        row.val(ui.item['value']);
    }  
    
    this.rebindEl = function rebindEl() {

        bindAutoCompleteForTransactionDetail('prepaidExpenseCode[]', objAndValueForDetailAutoComplete, 'ajax-prepaid-expense.php?action=getDataForExpenseAccrual', thisObj.updateDetail, thisObj.calculateTotal);
        
        bindEl(tabObj.find("[name='amount[]'],[name='prepaidExpenseCode[]']"), 'change', function () { thisObj.calculateTotal() });  
    }

    this.loadOnReady = function loadOnReady() {

        thisObj.rebindEl();
    }
}
