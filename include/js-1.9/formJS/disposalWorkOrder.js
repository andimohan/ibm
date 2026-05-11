function DisposalWorkOrder(tabID, opt, Details){    
    var thisObj = this;
    var tabObj = $("#" + tabID);      

    this.tabID = tabID;
    
    this.useStorage = opt.useStorage;  
    
    var id = tabObj.find("[name=hidId]").val();

    var objAndValue = new Array;
    objAndValue.push({object:'hidSupplierKey[]', value :'pkey'});  
    var objAndValueForSupplierDetailAutoComplete = objAndValue;

    var objAndValue = new Array;
    objAndValue.push({object:'hidWasteKey[]', value :'pkey'});  
    objAndValue.push({object:'waste[]', value :'value'});  
    var objAndValueForDisposalDetailAutoComplete = objAndValue;

    var arrDetails = Details;


    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = (opt.arrFile) ? opt.arrFile : Array();

this.updateWorkList = function updateWorkList() {
    var workListKey = tabObj.find("[name=hidWorkListKey]").val();
    if (!workListKey)
        return;

    // udpate detail container
    $.ajax({
        type: "GET",
        url: 'ajax-disposal-work-order-dispatcher.php',
        async: false,
        data: "action=getDetailWithRelatedInformation&pkey=" + workListKey,
    }).done(function(data) {

        data = JSON.parse(data);

        // udpate detail
        for (i = 0; i < data.length; i++) {
            var pkey = data[i].pkey;

            var arrTemp = {};
            arrTemp['customerkey'] = data[i].customerkey;
            arrTemp['customername'] = data[i].customername;

            arrDetails[pkey] = arrTemp;
        }

        // update combobox services
        var newOptions = {};
        for (i = 0; i < data.length; i++)
            newOptions[data[i].customerkey] = data[i].customername;

        var select = $("#" + tabID + " [name=hidCustomerKey]");
        if (select.prop)
            var options = select.prop('options');
        else
            var options = select.attr('options');

        $('option', select).remove();

        $.each(newOptions, function(val, text) {
            options[options.length] = new Option(text, val);
        });

        select.find('option:eq(0)').prop('selected', true).change();

        thisObj.updateCarAndDriver();
    });
}

this.updateCarAndDriver = function updateCarAndDriver() {
    var workListKey = tabObj.find("[name=hidWorkListKey]").val();
    if (!workListKey)
        return;

    $.ajax({
        type: "GET",
        url: 'ajax-disposal-work-order-dispatcher.php',
        async: false,
        data: "action=searchData&pkey=" + workListKey,
    }).done(function (data) {

        data = JSON.parse(data);
        data = data[0];

        tabObj.find("[name=policeNumber]").val(data.policenumber);
        tabObj.find("[name=driverName]").val(data.drivername);
    });
}

this.calculateTotal = function calculateTotal() {
    
    var total = 0;

    tabObj.find("[name='customerWeight[]']").each(function () {

        // total += $(this).val();
        total += parseFloat(unformatCurrency( $(this).val()));
    })


    tabObj.find("[name='totalDisposalWeight']").val(total).blur();

}


this.updateWorkDetailList = function updateWorkDetailList() {
    var workListKey = tabObj.find("[name=hidWorkListKey]").val();
    var customerKey = tabObj.find("[name=hidCustomerKey]").val();
    if (!workListKey || !customerKey)
        return;

    $.ajax({
        type: "GET",
        url: 'ajax-disposal-work-order-dispatcher.php',
        async: false,
        data: "action=getDetailWithRelatedInformation&pkey=" + workListKey+ '&customerKey=' + customerKey,
    }).done(function (data) {

        data = JSON.parse(data);
        data = data[0];
        if (!data) return;
        
        tabObj.find("[name=cityName]").val(data.cityandcategoryname);
        tabObj.find("[name=JOCode]").val(data.jobordercode);
        tabObj.find("[name=hidJobOrderKey]").val(data.joborderkey);
        tabObj.find("[name=serviceName]").val(data.servicename);
        tabObj.find("[name=maximumWeight]").val(data.quota).blur();
    });
}
     
    
    this.rebindEl = function rebindEl(){ 
        var JOKey = tabObj.find("[name=hidJobOrderKey]").val();
        bindAutoCompleteForTransactionDetail('supplierName[]', objAndValueForSupplierDetailAutoComplete, 'ajax-supplier.php?action=searchData');
        bindAutoCompleteForTransactionDetail('waste[]', objAndValueForDisposalDetailAutoComplete, 'ajax-disposal-job-order.php?action=getWasteDetail&pkey='+ JOKey);

        bindEl(tabObj.find("[name='customerWeight[]']"), 'change', function () {
            thisObj.calculateTotal();
        });
    }
        
    this.loadOnReady = function loadOnReady(){  

          
        if(thisObj.useStorage){
            
        }else{
           if (fileFolder) {

                if (id) {
                    createFileUploader(fileUploaderTarget, fileFolder, id, arrFile, true);
                } else {
                    createFileUploader(fileUploaderTarget, fileFolder, "", "", true);
                }

                tabObj.find(".file-list").sortable({
                    placeholder: "sortable-placeholder",
                    stop: function (event, ui) {
                        updateItemFileArray(opt.fileUploaderTarget);
                    }
                });
                tabObj.find(".file-list").disableSelection();

            }
        }


        tabObj.find("[name=hidCustomerKey]").change(function () {
            thisObj.updateWorkDetailList();
        });
         
        thisObj.rebindEl();
    }
}
