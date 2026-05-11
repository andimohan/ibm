function MedicalRequestClaim(tabID, data, opt) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    var id = tabObj.find("[name=hidId]").val(); 
    this.tabID = tabID;
	
    var objAndValue = new Array;
    objAndValue.push({object: 'hidItemKey[]',value: 'pkey'});
    objAndValue.push({object: 'detailDescription[]',value: 'shortdescription'});
    objAndValue.push({object: 'priceInUnit[]',value: 'sellingprice'});
    var objAndValueForDetailAutoComplete = objAndValue;

    var objAndValue = new Array;
    objAndValue.push({object: 'hidInitialDiagnoseKey[]',value: 'pkey'});
    var objAndValueForInitialDiagnoseDetailAutoComplete = objAndValue;

    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader"; 
    var arrFile =  (opt.arrFile) ? opt.arrFile : Array(); 


    this.updateCustomerInsurancePolicy = function updateCustomerInsurancePolicy() {
        var customerInsurancePolicy = tabObj.find("[name=hidCustomerInsurancePolicyKey]").val();
        if (!customerInsurancePolicy) return;

        $.ajax({
            type: "GET",
            url: 'ajax-customer-insurance-policy.php',
            async: false,
            data: "action=searchData&pkey=" + customerInsurancePolicy,
        }).done(function (data) {
		 
            data = JSON.parse(data);
			if(data.length == 0) return;
			
            data = data[0];
			
            tabObj.find("[name=policyNumber]").val(data.policynumber); 
            tabObj.find("[name=insuranceCompanyName]").val(data.suppliername);
            tabObj.find("[name=countryName]").val(data.countryname); 
            tabObj.find("[name=companyName]").val(data.companyname);    
			 
            tabObj.find("[name=insuredName]").val(data.name);    
            tabObj.find("[name=insuredID]").val(data.idnumber); 
            tabObj.find("[name=insuredEmail]").val(data.email);
            tabObj.find("[name=insuredPhone]").val(data.phone);
            tabObj.find("[name=insuredMobile]").val(data.mobile);
			
            tabObj.find("[name=dateOfBirth]").val(moment(data.dateofbirth).format(_DATE_FORMAT_)); 
            tabObj.find("[name=categoryName]").val(data.categoryname);
            tabObj.find("[name=age]").val(calculateAge(data.dateofbirth));

        });
    }

    this.calculateDetail = function calculateDetail(obj) {
        var row = $(obj).closest(".transaction-detail-row");
        var itemkey = row.find("[name='hidItemKey[]']").val();

        var quantity = unformatCurrency(row.find("[name='qty[]']").val());
        var priceInUnit = unformatCurrency(row.find("[name='priceInUnit[]']").val());

        var subtotal = quantity * priceInUnit;
        row.find("[name='detailSubtotal[]']").val(subtotal).blur();

       thisObj.calculateTotal();
    };

    this.calculateTotal = function calculateTotal(recalculateVoucher) {
        if (!recalculateVoucher) recalculateVoucher = true;

        var subtotal = 0;
        tabObj.find("[name='detailSubtotal[]']").each(function () {  subtotal += parseInt(unformatCurrency($(this).val())) || 0;  });
        tabObj.find("[name='subtotal']").val(subtotal).blur();

        var finalDiscount =  parseFloat( unformatCurrency(tabObj.find("[name='finalDiscount']").val()) ) || 0;
        var finalDiscountType = parseInt(  unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())  ) || 0; 
        var etcCost = parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0;
        var includeTax = tabObj.find("[name='chkIncludeTax']").val();
        var taxPercentage = parseFloat( unformatCurrency(tabObj.find("[name='taxPercentage']").val()) ) || 0;

        if (finalDiscount != 0 && finalDiscountType == 2)
            finalDiscount = (finalDiscount / 100) * subtotal;

        subtotal -= finalDiscount;

        tabObj.find("[name='beforeTaxTotal']").val(subtotal).blur();

        var taxValue = 0;
        if (includeTax == 0) {
            taxValue = (subtotal * taxPercentage) / 100;
            subtotal += taxValue;
        } else {
            taxValue = (taxPercentage / (100 + taxPercentage)) * subtotal;
            tabObj
                .find("[name='beforeTaxTotal']")
                .val(subtotal - taxValue)
                .blur();
        }

        tabObj.find("[name='taxValue']").val(taxValue).blur();

        var total = subtotal + etcCost;
        tabObj.find("[name='grandtotal']").val(total).blur();
    };

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    };
 
    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData');
        bindAutoCompleteForTransactionDetail('initialDiagnose[]',objAndValueForInitialDiagnoseDetailAutoComplete,'ajax-diagnose.php?action=searchData&isleaf=1&limit=25'); 
        bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]']"), 'change', function () {
            thisObj.calculateDetail(this)
        });
    }

    this.loadOnReady = function loadOnReady() {
       
        if(fileFolder){
            if(id){     
                createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,true);  
            }else{ 
                createFileUploader(fileUploaderTarget,fileFolder, "" , "",true); 
            }

            tabObj.find(".file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(opt.fileUploaderTarget); }});
            tabObj.find(".file-list"  ).disableSelection();  
        }
  

        if (!data['initialDiagnoseDetail'] || data['initialDiagnoseDetail'].length == 0)
            addNewTemplateRow("diagnose-row-template",null,null,thisObj.rebindEl);
 
		thisObj.rebindEl();
    }
}