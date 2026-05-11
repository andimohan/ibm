function BuildingUnit(tabID, data) {
  var thisObj = this;
  var tabObj = $("#" + tabID);
  
  this.tabID = tabID;

  var objAndValue = new Array();
  objAndValue.push({ object: "hidTenantCustomerKey[]", value: "pkey" });
 
  var objAndValueHomeowner = new Array();
  objAndValueHomeowner.push({ object: "hidOwnerCustomerKey[]", value: "pkey" });
	 

  var objAndValueForDetailAutoComplete = objAndValue;
  var objAndValueForDetailAutoCompleteHomeowner = objAndValueHomeowner;

  this.rebindEl = function rebindEl() {
    bindAutoCompleteForTransactionDetail(  "ownerName[]",  objAndValueForDetailAutoCompleteHomeowner,  "ajax-customer.php?action=searchData" );
    bindAutoCompleteForTransactionDetail(  "tenantName[]",  objAndValueForDetailAutoComplete, "ajax-customer.php?action=searchData"  );
  };

  this.loadOnReady = function loadOnReady() {
	  
    
      if (data["rsTenant"] != undefined &&  data["rsTenant"].length < 1) 
          addNewTemplateRow( "house-tenant-detail-row-template",   null, null,   thisObj.rebindEl  );
   	  

		tabObj.find("[name=btnAddRowsHouseTenant]").on("click", function () {
			addNewTemplateRow(  "house-tenant-detail-row-template",    null,   null,  thisObj.rebindEl  );
		});

    thisObj.rebindEl();
  };
}
