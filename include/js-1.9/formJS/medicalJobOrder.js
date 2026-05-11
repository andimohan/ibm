function MedicalJobOrder(tabID, data, opt) {
    var thisObj = this;
    var tabObj = $("#" + tabID);
    this.tabID = tabID;
    var id = tabObj.find("[name=hidId]").val();
 
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
 
    this.updateDataJO = function updateDataJO() {
		
        var medicalrequestclaimkey = tabObj.find("[name=hidMedicalRequestClaimKey]").val();
        if (!medicalrequestclaimkey) return;

        $.ajax({
            type: "GET",
            url: 'ajax-medical-request-claim.php',
            beforeSend: function (xhr) {  
                clearAllRows(tabObj.find(".service-detail"),false);
            },
            data: "action=searchData&pkey=" + medicalrequestclaimkey,
        }).done(function (data) {

            data = JSON.parse(data);
            data = data[0];
            tabObj.find("[name=refkey]").val(data.pkey);
            tabObj.find("[name=callerName]").val(data.callername);
            tabObj.find("[name=insuredName]").val(data.insuredname);
            tabObj.find("[name=relationToInsured]").val(data.relationtoinsured);
            tabObj.find("[name=mobile]").val(data.mobile);
            tabObj.find("[name=email]").val(data.email);
            tabObj.find("[name=insuredEmail]").val(data.insuredemail);
            tabObj.find("[name=insuredMobile]").val(data.insuredmobile);
            tabObj.find("[name=insuredPhone]").val(data.insuredphone);
            tabObj.find("[name=address]").val(data.caseaddress);
            tabObj.find("[name=passport]").val(data.passport);
            tabObj.find("[name=casePhone]").val(data.casephone);
            tabObj.find("[name=companyName]").val(data.companyname);
            tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
            tabObj.find("[name=dateOfBirth]").val(moment(data.dateofbirth).format(_DATE_FORMAT_));  
            tabObj.find("[name=cityName]").val(data.casecityandcategoryname);
            tabObj.find("[name=hidCityKey]").val(data.citykey);
            tabObj.find("[name=policyNumber]").val(data.policynumber);
            tabObj.find("[name=insuredID]").val(data.insuredid);
            tabObj.find("[name=insuranceCompanyName]").val(data.insurancecompanyname);
            tabObj.find("[name=countryName]").val(data.countryname);
            tabObj.find("[name=age]").val(data.age).blur();
            tabObj.find("[name=categoryName]").val(data.customercategoryname);
            tabObj.find("[name=codeLog]").val(data.codelog);
            tabObj.find("[name=trDesc]").val(data.casedescription);
        });

        thisObj.updateInitialDiagnose(); 
        thisObj.updateDetail(medicalrequestclaimkey);
	}

    this.updateDetail = function updateDetail(medicalrequestclaimkey) {
      
        $.ajax({
            type: "GET",
            url: 'ajax-medical-request-claim.php',
			beforeSend: function (xhr) { 
        		clearAllRows(tabObj.find('.service-detail'));
            },
            async: false,
            data: "action=getDetailById&pkey=" + medicalrequestclaimkey,
        }).done(function (data) {
			
            if (!data) return; 
            var data = JSON.parse(data);

			for(i=0;i<data.length;i++){  
                var arrPostValue = []; 
                arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                arrPostValue.push({"selector":"itemName", "value":data[i].itemname});
                arrPostValue.push({"selector":"qty", "value":data[i].qty});
                arrPostValue.push({"selector":"hidStatusKey", "value":data[i].statuskey});
                arrPostValue.push({"selector":"priceInUnit", "value":data[i].priceinunit});
                arrPostValue.push({"selector":"detailDescription", "value":data[i].trdesc});
                arrPostValue.push({"selector":"hidQuotationKey", "value":data[i].refquotationkey}); 
				
                $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));   
            }
			
			thisObj.rebindEl();
			tabObj.find(".inputnumber, .inputdecimal").blur();
			tabObj.find("[name='qty[]']").change();
        });
		
    }
	
    this.updateMedicalRequestClaim = function updateMedicalRequestClaim(event, ui) {
 
		reconfirmUpdateData(tabObj, ui,
							{"key" : "hidMedicalRequestClaimKey", "value": "medicalRequestClaimCode" }, 
							{"key" : "hidCurrentMedicalRequestClaimKey", "value": "hidCurrentMedicalRequestClaimCode"},
							{
							 "updateFunc": thisObj.updateDataJO,
							 "rebindEl": thisObj.rebindEl
							}
						   );
		 
    }

    this.updateInitialDiagnose = function updateInitialDiagnose() {
        var medicalrequestclaimkey = tabObj.find("[name=hidMedicalRequestClaimKey]").val();
        if (!medicalrequestclaimkey)  return;

        $.ajax({
            type: "GET",
            url: 'ajax-medical-request-claim.php',
			beforeSend: function (xhr) {  
                clearAllRows(tabObj.find(".diagnose-detail"));
            },
            async: false,
            data: "action=getDetailDiagnose&pkey=" + medicalrequestclaimkey,
        }).done(function (data) {

            if (!data) return; 
            var data = JSON.parse(data);
                          
            for(i=0;i<data.length;i++){   
                var arrPostValue = [];  
                arrPostValue.push({"selector":"hidInitialDiagnoseKey", "value":data[i].initialdiagnosekey});
                arrPostValue.push({"selector":"initialDiagnose", "value":data[i].codenameinitialdiagnose});
                $newRow = addNewTemplateRow("diagnose-row-template",JSON.stringify(arrPostValue),null,thisObj.rebindEl);
            }
			
			if(data.length ==0 )
			     $newRow = addNewTemplateRow("diagnose-row-template",JSON.stringify(arrPostValue),null,thisObj.rebindEl);
			 
        });
    }
 

    this.calculateDetail = function calculateDetail(obj) {

        var row = $(obj).closest(".transaction-detail-row");
        var itemkey = row.find("[name='hidItemKey[]']").val();

        var quantity = unformatCurrency(row.find("[name='qty[]']").val());
        var priceValue = unformatCurrency(row.find("[name='priceInUnit[]']").val());


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
