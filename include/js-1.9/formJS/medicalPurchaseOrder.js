function MedicalPurchaseOrder(tabID, cashTOP, data, opt) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    var id = tabObj.find("[name=hidId]").val(); 
    this.tabID = tabID;

    var objAndValue = new Array;
    objAndValue.push({
        object: 'hidItemKey[]',
        value: 'pkey'
    });
    objAndValue.push({
        object: 'selUnit[]',
        value: 'deftransunitkey'
    });
    objAndValue.push({object: 'detailDescription[]',value: 'shortdescription'});
    objAndValue.push({object: 'priceValue[]',value: 'sellingprice'});
    var objAndValueForDetailAutoComplete = objAndValue;

    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader"; 
    var arrFile =  (opt.arrFile) ? opt.arrFile : Array(); 

    this.updateMedicalJobOrder = function updateMedicalJobOrder() {
        var medicalJobOrderKey = tabObj.find("[name=hidMedicalJobOrderkey]").val();
        if (!medicalJobOrderKey)
            return;

        $.ajax({
            type: "GET",
            url: 'ajax-medical-job-order.php',
            async: false,
            data: "action=searchData&pkey=" + medicalJobOrderKey,
        }).done(function (data) {

            data = JSON.parse(data);
            data = data[0];
            
            tabObj.find("[name=hidMedicalRequestClaimKey]").val(data.refkey);

            tabObj.find("[name=caseAddress]").val(data.caseaddress);
            tabObj.find("[name=casePhone]").val(data.casephone);
            tabObj.find("[name=caseCityName]").val(data.casecityandcategoryname);  
            tabObj.find("[name=caseDesc]").val(data.casedescription);

            thisObj.updateMedicalRequestClaim(data.refkey);
            thisObj.updateInitialDiagnose(medicalJobOrderKey);
            thisObj.updateDetail(medicalJobOrderKey);
        });
    }

    this.updateExcessFee = function updateExcessFee(customerInsurancePolicyKey) {
        if (!customerInsurancePolicyKey)
            return;

        $.ajax({
            type: "GET",
            url: 'ajax-customer-insurance-policy.php',
            async: false,
            data: "action=searchData&pkey=" + customerInsurancePolicyKey,
        }).done(function (data) {

            data = JSON.parse(data);
			
            data = data[0];
            tabObj.find("[name=excessFee]").val(data.excessfee).blur();
        });
    }

    this.updateInitialDiagnose = function updateInitialDiagnose(medicalJobOrderKey) {
      
        $.ajax({
            type: "GET",
            url: "ajax-medical-job-order.php",
			beforeSend: function (xhr) { 
        		tabObj.find(".diagnose-detail .transaction-detail-row").remove();
            },
            async: false,
            data: "action=getDetailDiagnose&pkey=" + medicalJobOrderKey,
        }).done(function (data) { 
            if (!data) return; 
            var data = JSON.parse(data);

			for(i=0;i<data.length;i++){  
                var arrPostValue = []; 
                arrPostValue.push({"selector":"initialDiagnose", "value":data[i].initialdiagnose});
                $newRow = addNewTemplateRow("diagnose-row-template",JSON.stringify(arrPostValue));   
            }
        });
    }

    this.updateMedicalRequestClaim = function updateMedicalRequestClaim(medicalRequestClaimKey) {
        if (!medicalRequestClaimKey) return;

        $.ajax({
            type: "GET",
            url: 'ajax-medical-request-claim.php',
            // async: false,
            beforeSend: function (xhr) {
				// gk perlu remove, karena quotation
                //tabObj.find(".transaction-detail-row").remove(); //remove all rows  
            },
            data: "action=searchData&pkey=" + medicalRequestClaimKey,
        }).done(function (data) {
			
            if (!data) return; 
			data = JSON.parse(data);
            data = data[0];
			
			tabObj.find("[name=codeLog]").val(data.codelog); 
            tabObj.find("[name=policyNumber]").val(data.policynumber);
            tabObj.find("[name=categoryName]").val(data.customercategoryname);
            tabObj.find("[name=insuredName]").val(data.insuredname);
            tabObj.find("[name=companyName]").val(data.companyname);
            tabObj.find("[name=insuranceCompanyName]").val(data.insurancecompanyname);
            tabObj.find("[name=insuredID]").val(data.insuredid);
            tabObj.find("[name=countryName]").val(data.countryname);  
            tabObj.find("[name=dateOfBirth]").val(moment(data.dateofbirth).format(_DATE_FORMAT_));  
            tabObj.find("[name=insuredEmail]").val(data.insuredemail);
            tabObj.find("[name=insuredMobile]").val(data.insuredmobile);
            tabObj.find("[name=insuredPhone]").val(data.insuredphone);  
            tabObj.find("[name=age]").val(data.age).blur();
			
            thisObj.updateExcessFee(data.customerinsurancepolicykey);
        });

    };
    
    this.updateDetail = function updateDetail(medicalJobOrderKey) {
      
        $.ajax({
            type: "GET",
            url: 'ajax-medical-job-order.php',
			beforeSend: function (xhr) { 
        		clearAllRows(tabObj.find('.service-detail'));
            },
            async: false,
            data: "action=getDetailById&pkey=" + medicalJobOrderKey,
        }).done(function (data) {
			
            if (!data) return; 
            var data = JSON.parse(data); 
			for(i=0;i<data.length;i++){  
                var arrPostValue = []; 
                arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                arrPostValue.push({"selector":"itemName", "value":data[i].itemname});
                arrPostValue.push({"selector":"quantityValue", "value":data[i].qty});
                arrPostValue.push({"selector":"priceValue", "value":data[i].priceinunit});
                arrPostValue.push({"selector":"detailDescription", "value":data[i].trdesc});
			 
                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));   
            }
			
			thisObj.rebindEl();
			tabObj.find(".inputnumber, .inputdecimal").blur();
			tabObj.find("[name='quantityValue[]']").change();
        });
		
    }

    this.calculateDetail = function calculateDetail(obj) {

        var row = $(obj).closest(".transaction-detail-row");
        var itemkey = row.find("[name='hidItemKey[]']").val();

        var quantity = unformatCurrency(row.find("[name='quantityValue[]']").val());
        var priceValue = unformatCurrency(row.find("[name='priceValue[]']").val());


        var subtotal = quantity * priceValue;
        row.find("[name='detailSubtotal[]']").val(subtotal).blur();

        thisObj.calculateTotal();
    }

    this.calculateTotal = function calculateTotal() {
        var subtotal = 0;
        tabObj.find("[name='detailSubtotal[]']").each(function () {
            subtotal += parseInt(unformatCurrency($(this).val())) || 0;
        })
        tabObj.find("[name='subtotal']").val(subtotal).blur();

        var finalDiscount = parseInt(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0;
        var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0;
        var shipmentFee = parseInt(unformatCurrency(tabObj.find("[name='shipmentFee']").val())) || 0;
        var etcCost = parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0;
        var includeTax = tabObj.find("[name='chkIncludeTax']").val();
        var taxPercentage = parseInt(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0;

         if (finalDiscount != 0 && finalDiscountType == 2) finalDiscount = finalDiscount / 100 * subtotal;

         subtotal -= finalDiscount;
         tabObj.find("[name='beforeTaxTotal']").val(subtotal).blur();

         var taxValue = 0;
         if (includeTax == 0) {
             taxValue = subtotal * taxPercentage / 100;
             subtotal += taxValue;
         } else {
             taxValue = (taxPercentage / (100 + taxPercentage)) * subtotal;
             tabObj.find("[name='beforeTaxTotal']").val(subtotal - taxValue).blur();
         }

         tabObj.find("[name='taxValue']").val(taxValue).blur();

         var total = subtotal + shipmentFee + etcCost;
         tabObj.find("[name='total']").val(total).blur();

         var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

         var balance = totalPayment - total;
         tabObj.find("[name='balance']").val(balance).blur();

    }

    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler() {
        thisObj.calculateTotal(); 
    }
 
	this.afterRemoveRowHandler = function afterRemoveRowHandler(){
	 thisObj.calculateTotal(); 
	}


    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData');
        bindEl(tabObj.find("[name='quantityValue[]'], [name='priceValue[]']"), 'change', function () {
            thisObj.calculateDetail(this)
        });
        bindEl(tabObj.find("[name='selDiscountType[]']"), 'change', function () {
            updateDecimal(this);
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

        tabObj.find("[name=selTermOfPaymentKey]").change(function () {

            for (i = 0; i < cashTOP.length; i++) {
                if ($(this).val() == cashTOP[i]) {
                    tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function () {
                        $(this).click()
                    });
                    tabObj.find(".cashTOP").hide();
                    return;
                }
            }

            tabObj.find(".cashTOP").show();
        });

        tabObj.find("[name=selTermOfPaymentKey]").change();


        tabObj.find(".form-detail-field").toggle();

        tabObj.find(".form-detail-button").click(function () {
            tabObj.find(".form-detail-field").toggle("highlight");
            var temp = tabObj.find(".form-detail-button").attr("relalt");
            $("#" + tabID + " .form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
            tabObj.find(".form-detail-button").text(temp);
        });

        tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost], [name=taxPercentage]").change(function () {
            thisObj.calculateTotal(this)
        })
        tabObj.find("[name=selFinalDiscountType]").change(function () {
            updateFinalDiscountDecimal(this)
        })

        if (data['initialDiagnoseDetail'].length == 0)  
			addNewTemplateRow("diagnose-row-template",null,null,thisObj.rebindEl);

        thisObj.rebindEl();
    }
}
