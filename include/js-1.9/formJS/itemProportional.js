function ItemProportional(tabID, data) {
  var thisObj = this;
  var tabObj = $("#" + tabID);
  
  this.tabID = tabID;

  var objAndValueForDetailAutoComplete = new Array();
  objAndValueForDetailAutoComplete.push({ object: "hidItemDetailKey[]", value: "pkey" });
	 
  var objAndValueForDetailAutoComplete = objAndValueForDetailAutoComplete;

  this.rebindEl = function rebindEl() {
    bindAutoCompleteForTransactionDetail("itemDetailName[]",  objAndValueForDetailAutoComplete,  "ajax-item.php?&itemtype=1&action=searchData" );
  };

  this.loadOnReady = function loadOnReady() {

    thisObj.rebindEl();
  };
}
