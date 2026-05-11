function Shipment(tabID){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      

    var objAndValue = new Array;
    objAndValue.push({object:'hidMarketplaceLogisticKey[]', value :'pkey'});    
    var objAndValueForDetailAutoComplete = objAndValue;

    this.tabID = tabID; 

    var id = tabObj.find("[name=hidId]").val(); 
    var marketplaceKey = '';

    this.rebindEl = function rebindEl(){   
         bindAutoCompleteForTransactionDetail('marketplaceLogisticName[]',objAndValueForDetailAutoComplete,'ajax-marketplace.php?action=getMarketplaceLogistics&marketplaceKey=' + marketplaceKey);
        
        tabObj.find("[name='marketplaceLogisticName[]']").focus(function() {
          marketplaceKey = $(this).closest(".marketplace-logistic-row").find("[name='hidMarketplaceKey[]']").val();
          thisObj.rebindEl();
        });
        
     }

    this.loadOnReady = function loadOnReady(){ 
 
        tabObj.find("[name='marketplaceLogisticName[]']").focus(function() {
          marketplaceKey = $(this).closest(".marketplace-logistic-row").find("[name='hidMarketplaceKey[]']").val();
          thisObj.rebindEl();
        });
        
        thisObj.rebindEl();
    }
}