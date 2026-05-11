function Reminder(tabID) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    this.tabID = tabID;


    this.updateTransactionType = function updateTransactionType() {
        var selModule = tabObj.find("[name=selModule]");
        var requestObj = tabObj.find(".isrequest");
        var jobObj = tabObj.find(".isjob");

        var transactionType = selModule.val();
        tabObj.find(".isrequest").hide();
        tabObj.find(".isjob").hide();
        tabObj.find(".ispurchase").hide();
        tabObj.find(".isquotation").hide();
        tabObj.find(".isinvoice").hide();
        switch (transactionType) {
            case 'request':
                tabObj.find(".isrequest").show();
                break;
            case 'job':
                tabObj.find(".isjob").show();
                break;
            case 'quotation':
                tabObj.find(".isquotation").show();
                break;
            case 'guaranteeLetter':
                tabObj.find(".ispurchase").show();
                break;
            case 'invoice':
                tabObj.find(".isinvoice").show();
                break;
        }
    }


    this.rebindEl = function rebindEl() {

    }

    this.loadOnReady = function loadOnReady() {
        tabObj.find("[name=selModule]").change(function () {
            thisObj.updateTransactionType();
        });

        tabObj.find("[name=selModule]").change();


        thisObj.rebindEl();
    }
}
