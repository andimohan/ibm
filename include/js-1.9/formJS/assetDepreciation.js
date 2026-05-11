function AssetDepreciation(tabID, cashTOP, tablekey, varConstant) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
 
    this.tabID = tabID;
    this.tablekey = tablekey;

	var objAndValue = new Array;
	objAndValue.push({object:'hidAssetKey[]', value :'pkey'});    
	objAndValue.push({object:'assetName[]', value :'name'});   
	objAndValue.push({object:'depreciationValue[]', value :'depreciationvalue'});   
	var objAndValueForDetailAutoComplete = objAndValue;

   this.updateDetail = function updateDetail(target,objAndValue,ui){
 
		var detailRow = $(target).closest(".transaction-detail-row");   
		for(i=0;i<objAndValue.length;i++){     
			detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();    
		} 

		// harus handle manual utk obj autosearch
		detailRow.find("[name=\"assetCode[]\"]").first().val(ui.item['code']);  
 
  	}
   
    this.calculateDetail = function calculateDetail(obj) { 
        thisObj.calculateTotal();
    }


    this.calculateTotal = function calculateTotal() {
        var subtotal = 0;
        tabObj.find("[name='depreciationValue[]']").each(function () { subtotal += parseInt(unformatCurrency($(this).val())) || 0; })
        tabObj.find("[name='grandtotal']").val(subtotal).blur();
 
    }
  
    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    }

    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('assetCode[]', objAndValueForDetailAutoComplete, 'ajax-asset.php?action=searchData&hasBookValue=1',thisObj.updateDetail);
		bindEl(tabObj.find("[name='assetCode[]']" ), 'change',  function(){ thisObj.calculateDetail(this) });  
    }

    this.loadOnReady = function loadOnReady() { 
        thisObj.rebindEl();

    }
}