function MedicalSalesOrderQuotation(tabID, data, opt) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    var id = tabObj.find("[name=hidId]").val();  
    this.tabID = tabID;
 
	var varConstant = opt.constant;
	
    var objAndValue = new Array;
    objAndValue.push({object: 'hidItemKey[]',value: 'pkey'});
    objAndValue.push({object: 'detailDescription[]',value: 'shortdescription'}); 
    var objAndValueForDetailAutoComplete = objAndValue;

    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader"; 
    var arrFile =  (opt.arrFile) ? opt.arrFile : Array(); 
 
    this.calculateDetail = function calculateDetail(obj) {
        var row = $(obj).closest(".transaction-detail-row"); 
		
        var itemkey = row.find("[name='hidItemKey[]']").val();

        var quantity = unformatCurrency(row.find("[name='qty[]']").val());
        var priceValue = unformatCurrency(row.find("[name='priceInUnit[]']").val());
 
        var subtotal = quantity * priceValue;
        row.find("[name='detailSubtotal[]']").val(subtotal).blur();

        thisObj.calculateTotal();
    };

    this.calculateTotal = function calculateTotal(recalculateVoucher) {
        if (!recalculateVoucher) recalculateVoucher = true;

        var subtotal = 0;
        tabObj.find("[name='detailSubtotal[]']").each(function () {
            subtotal += parseInt(unformatCurrency($(this).val())) || 0;
        });
        tabObj.find("[name='subtotal']").val(subtotal).blur();

        var finalDiscount =
            parseFloat(
                unformatCurrency(tabObj.find("[name='finalDiscount']").val())
            ) || 0;
        var finalDiscountType =
            parseInt(
                unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())
            ) || 0; 
        var etcCost =
            parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0;
        var includeTax = tabObj.find("[name='chkIncludeTax']").val();
        var taxPercentage =
            parseFloat(
                unformatCurrency(tabObj.find("[name='taxPercentage']").val())
            ) || 0;

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
        tabObj.find("[name='total']").val(total).blur();
    };

    this.updateMedicalJobOrder = function updateMedicalJobOrder() {
		
	   var medicalJobOrderKey = tabObj.find("[name=hidMedicalJobOrderkey]").val();
      if (!medicalJobOrderKey) return;

      $.ajax({
          type: "GET",
          url: "ajax-medical-job-order.php",
          // async: false,
          beforeSend: function (xhr) {
              tabObj.find(".transaction-detail-row").remove(); //remove all rows  
          },
          data: "action=getDataRowById&pkey=" + medicalJobOrderKey,
      }).done(function (data) {
          data = JSON.parse(data);
          data = data[0];

            tabObj.find("[name=codeLog]").val(data.codelog); 
            tabObj.find("[name=policyNumber]").val(data.policynumber);
            tabObj.find("[name=categoryName]").val(data.categoryname);
            tabObj.find("[name=insuredName]").val(data.insuredname);
            tabObj.find("[name=companyName]").val(data.customername);
            tabObj.find("[name=insuranceCompanyName]").val(data.insurancecompanyname);
            tabObj.find("[name=insuredID]").val(data.insuredid);
            tabObj.find("[name=countryName]").val(data.countryname);  
            tabObj.find("[name=dateOfBirth]").val(moment(data.dateofbirth).format(_DATE_FORMAT_));  
            tabObj.find("[name=insuredEmail]").val(data.insuredemail);
            tabObj.find("[name=insuredMobile]").val(data.insuredmobile);
            tabObj.find("[name=insuredPhone]").val(data.insuredphone);  
            tabObj.find("[name=age]").val(data.age).blur();

          tabObj.find("[name=caseAddress]").val(data.address);
          tabObj.find("[name=casePhone]").val(data.casephone);
          tabObj.find("[name=caseCityName]").val(data.casecityandcategoryname);  
          tabObj.find("[name=caseDesc]").val(data.trdesc);
          tabObj.find("[name=hidMedicalRequestClaimKey]").val(data.refkey);
          
          thisObj.updateInitialDiagnose(medicalJobOrderKey);
          thisObj.updateDetail(medicalJobOrderKey);
      });

    };

    this.updateMedicalRequestClaim = function updateMedicalRequestClaim() {
        var medicalRequestClaimKey = tabObj.find("[name=hidMedicalRequestClaimKey]").val();
        if (!medicalRequestClaimKey) return;

        $.ajax({
            type: "GET",
            url: "ajax-medical-request-claim.php",
            // async: false,
            beforeSend: function (xhr) {
				// gk perlu remove, karena quotation
                //tabObj.find(".transaction-detail-row").remove(); //remove all rows  
            },
            data: "action=getDataRowById&pkey=" + medicalRequestClaimKey,
        }).done(function (data) {
			
            if (!data) return; 
			data = JSON.parse(data);
            data = data[0];
			
			tabObj.find("[name=codeLog]").val(data.codelog); 
            tabObj.find("[name=policyNumber]").val(data.policynumber);
            tabObj.find("[name=categoryName]").val(data.categoryname);
            tabObj.find("[name=insuredName]").val(data.insuredname);
            tabObj.find("[name=companyName]").val(data.customername);
            tabObj.find("[name=insuranceCompanyName]").val(data.insurancecompanyname);
            tabObj.find("[name=insuredID]").val(data.insuredid);
            tabObj.find("[name=countryName]").val(data.countryname);  
            tabObj.find("[name=dateOfBirth]").val(moment(data.dateofbirth).format(_DATE_FORMAT_));  
            tabObj.find("[name=insuredEmail]").val(data.insuredemail);
            tabObj.find("[name=insuredMobile]").val(data.insuredmobile);
            tabObj.find("[name=insuredPhone]").val(data.insuredphone);  
            tabObj.find("[name=age]").val(data.age).blur();
			
			 
            tabObj.find("[name=caseAddress]").val(data.address);
            tabObj.find("[name=casePhone]").val(data.casephone);
            tabObj.find("[name=caseCityName]").val(data.cityandcategoryname);  
            tabObj.find("[name=caseDesc]").val(data.trdesc);
        });

        thisObj.updateInitialDiagnose(medicalRequestClaimKey);
        thisObj.updateDetail(medicalRequestClaimKey);
    };

    this.updateDetail = function updateDetail(pkey) {
        var JOType = tabObj.find("[name=selJOType]").val();
        var ajaxUrl = (JOType == varConstant.JOBTYPE.request) ? "ajax-medical-request-claim.php" :  "ajax-medical-job-order.php";
        
        $.ajax({
            type: "GET",
            url: ajaxUrl,
			beforeSend: function (xhr) { 
        		clearAllRows(tabObj.find('.service-detail'));
            },
            async: false,
            data: "action=getUnAprrovedDetail&pkey=" + pkey,
        }).done(function (data) {
			
            if (!data) return; 
            var data = JSON.parse(data);

			for(i=0;i<data.length;i++){  
                var arrPostValue = []; 
                arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                arrPostValue.push({"selector":"itemName", "value":data[i].itemname});
                arrPostValue.push({"selector":"qty", "value":data[i].qty});
                arrPostValue.push({"selector":"priceInUnit", "value":data[i].priceinunit});
                arrPostValue.push({"selector":"detailDescription", "value":data[i].trdesc});
			 
                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));   
            }
			
			thisObj.rebindEl();
			tabObj.find(".inputnumber, .inputdecimal").blur();
			tabObj.find("[name='qty[]']").change();
        });
		
    }
	
	this.updateInitialDiagnose = function updateInitialDiagnose(pkey) {
        var JOType = tabObj.find("[name=selJOType]").val();
        var ajaxUrl = (JOType == varConstant.JOBTYPE.request) ? "ajax-medical-request-claim.php" :  "ajax-medical-job-order.php";

        $.ajax({
            type: "GET",
            url: ajaxUrl,
			beforeSend: function (xhr) { 
        		tabObj.find(".diagnose-detail .transaction-detail-row").remove();
            },
            async: false,
            data: "action=getDetailDiagnose&pkey=" + pkey,
        }).done(function (data) { 
            if (!data) return; 
            var data = JSON.parse(data);

			for(i=0;i<data.length;i++){  
                var arrPostValue = []; 
                arrPostValue.push({"selector":"initialDiagnose", "value":data[i].codenameinitialdiagnose});
                $newRow = addNewTemplateRow("diagnose-row-template",JSON.stringify(arrPostValue));   
            }
        });
    }

    this.updateTransactionType = function updateTransactionType() {
        var selJOType = tabObj.find("[name=selJOType]");
        var requestObj = tabObj.find(".isrequest");
        var jobObj = tabObj.find(".isjob");

        var transactionType = selJOType.val();
		
        if (transactionType == varConstant.JOBTYPE.job) {
            requestObj.hide();
            jobObj.show();
        } else {
            requestObj.show();
            jobObj.hide();
        }
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    };
 
    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete, 'ajax-item.php?action=searchData');
        bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]']"), 'change', function () {
            thisObj.calculateDetail(this)
        });
    };

    this.loadOnReady = function loadOnReady() {
        tabObj.find(".form-detail-field").toggle();

        tabObj.find(".form-detail-button").click(function () {
            tabObj.find(".form-detail-field").toggle("highlight");
            var temp = tabObj.find(".form-detail-button").attr("relalt");
            tabObj
                .find(".form-detail-button")
                .attr("relalt", tabObj.find(".form-detail-button").text());
            tabObj.find(".form-detail-button").text(temp);
        });

        tabObj.find("[name=selJOType]").change(function () {
            thisObj.updateTransactionType();
        });

        tabObj.find("[name=selJOType]").change();

        tabObj
            .find( "[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax],  [name=etcCost], [name=taxPercentage]")
            .change(function () {
                thisObj.calculateTotal();
            });
        tabObj.find("[name=selFinalDiscountType]").change(function () {
            updateFinalDiscountDecimal(this);
        });
  
        if (id && data['rsDetail'].length == 0)
            addNewTemplateRow("detail-row-template",null,null,thisObj.rebindEl);
 
		if (data['initialDiagnoseDetail'].length == 0)  
			addNewTemplateRow("diagnose-row-template",null,null,thisObj.rebindEl);
	
		
        thisObj.rebindEl();
    };
}
