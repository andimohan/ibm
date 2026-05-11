function SalesOrderProperty(tabID, rs) {
	var thisObj = this;
	var tabObj = $("#" + tabID);

	this.tabObj = tabObj;
	var objAndValueAgenFee = new Array;
	objAndValueAgenFee.push({ object: 'hidAgentKey[]', value: 'pkey' });
	objAndValueAgenFee.push({ object: 'agentName[]', value: 'name' });
	objAndValueAgenFee.push({ object: 'commissionPercentage[]', value: 'commissionpercentage' });
	objAndValueAgenFee.push({ object: 'provisionPercentage[]', value: 'commissionpercentage' });
	var objAndValueForAgenFeeDetailAutoComplete = objAndValueAgenFee;


	this.rs = (rs.length > 0) ? rs[0] : null;

	var objAndValue = new Array;
	objAndValue.push({ object: 'hidDownpaymentKey[]', value: 'pkey' });
	objAndValue.push({ object: 'downpaymentAmount[]', value: 'outstanding' });
	var objAndValueForDPDetailAutoComplete = objAndValue;

	this.tabID = tabID;

	this.calculateTotalDownpayment = function calculateTotalDownpayment() {
		var totalDP = 0;
		tabObj.find("[name='downpaymentAmount[]']").each(function () { totalDP += parseInt(unformatCurrency($(this).val())) || 0; })
		tabObj.find("[name='totalDownpayment']").val(totalDP).blur();

		return totalDP;
	}

	this.calculateRowAgent = function calculateRowAgent(obj, recalculateTotal) {
		var row = obj.closest('.transaction-detail-row');
		
		var agencyFee = parseFloat(unformatCurrency(tabObj.find("[name=agencyFee]").val())) || 0;
		var bankProvision = parseFloat(unformatCurrency(tabObj.find("[name=bankProvision]").val())) || 0;
		var agentClosingFee = parseFloat(unformatCurrency(row.find("[name='agentClosingFee[]']").val())) || 0;
			
		var cobrokePercentage = parseFloat(unformatCurrency(row.find("[name='cobrokePercentage[]']").val())) / 100;
		var commissionPercentage = parseFloat(unformatCurrency(row.find("[name='commissionPercentage[]']").val()));
		var provisionPercentage = parseFloat(unformatCurrency(row.find("[name='provisionPercentage[]']").val()));

		var cobrokeFee = agencyFee * cobrokePercentage;
		var commissionFee = cobrokeFee * commissionPercentage / 100;
		
		var cobrokeProvisionFee = bankProvision * cobrokePercentage;
		var provisionnFee = cobrokeProvisionFee * provisionPercentage / 100; 
		 
		var agentTotal = commissionFee + provisionnFee + agentClosingFee;

		row.find("[name='cobrokeFee[]']").val(cobrokeFee).blur();
		row.find("[name='commissionFee[]']").val(commissionFee).blur();
		row.find("[name='agentBankProvision[]']").val(provisionnFee).blur();
		row.find("[name='agentTotal[]']").val(agentTotal).blur();
 
		var companyCommissionPercentage = 100 - commissionPercentage;
		var companyProvisionPercentage = 100 - provisionPercentage;
		
		row.find(".company-commission-percentage").html(companyCommissionPercentage);
		row.find(".company-provision-percentage").html(companyProvisionPercentage);
		
		
		if(recalculateTotal == true)
 			thisObj.calculateTotal();
		
	}

	this.calculateAllAgent = function calculateAllAgent(recalculateTotal) { 
		 
		tabObj.find("[name='cobrokePercentage[]']").each(function () {
				thisObj.calculateRowAgent( $(this), recalculateTotal); 
		})
		
		// gk bisa pake ini karena perlu paramteer recalculateTotal agar gk looping forever
		//tabObj.find("[name='cobrokePercentage[]']").change(); 
	}

	this.changeSelDiscountType = function changeSelDiscountType() {

		var selkey = tabObj.find("[name=selCommissionType]").val();

		tabObj.find(".field-percentage").hide();
		tabObj.find("[name=agencyFee]").prop('readonly', false);

		if (selkey == 2) {

			tabObj.find("[name=agencyFee]").prop('readonly', true);
			tabObj.find(".field-percentage").show();
		}



	}
	this.changeProvisionType = function changeProvisionType() {
		var selkey = tabObj.find("[name=selProvisionType]").val();

		tabObj.find(".field-percentage-provision").hide();
		tabObj.find("[name=bankProvision]").prop('readonly', false);

		if (selkey == 2) {

			tabObj.find("[name=bankProvision]").prop('readonly', true);
			tabObj.find(".field-percentage-provision").show();
		}



	}

	this.calculateTotal = function calculateTotal() {

		var transactionTotal = parseFloat(unformatCurrency(tabObj.find("[name='transactionTotal']").val())) || 0; 
		var totalDP = thisObj.calculateTotalDownpayment(); 
		var orLeadPercentage = parseFloat(unformatCurrency(tabObj.find("[name='orLeadPercentage']").val())) || 0;
		
		var commissionType = parseInt(unformatCurrency(tabObj.find("[name='selCommissionType']").val())) || 0;
  		if(commissionType == 2)  thisObj.calculateAgencyFee();
		
		var provisionType = parseInt(unformatCurrency(tabObj.find("[name='selProvisionType']").val())) || 0;
  		if(provisionType == 2) thisObj.calculateProvisionFee();

		var	agencyFee =  parseInt(unformatCurrency(tabObj.find("[name='agencyFee']").val())) || 0;
		var	bankProvision =  parseInt(unformatCurrency(tabObj.find("[name='bankProvision']").val())) || 0;
		var orLeadTotal = transactionTotal * orLeadPercentage / 100;

		
		tabObj.find("[name='orLead']").val(orLeadTotal).blur();

		thisObj.calculateAllAgent(false);
		
		var totalAgentCommision = 0; 
		tabObj.find("[name='commissionFee[]']").each(function () {
			totalAgentCommision +=  parseInt(unformatCurrency($(this).val())) || 0; 
		})
		
		tabObj.find("[name=agentFee]").val(totalAgentCommision).blur();
		
		var officeFeeCommTotal = agencyFee - totalAgentCommision;
		tabObj.find("[name=officeFee]").val(officeFeeCommTotal).blur();
		
		var totalAgentBankProvision = 0; 
		tabObj.find("[name='agentBankProvision[]']").each(function () {
			totalAgentBankProvision +=  parseInt(unformatCurrency($(this).val())) || 0; 
		})
		
		tabObj.find("[name=agentFeeBank]").val(totalAgentBankProvision).blur();
		
		
		var officeProvisionTotal = bankProvision - totalAgentBankProvision;
		tabObj.find("[name=officeFeeBank]").val(officeProvisionTotal).blur();
		
		tabObj.find("[name='totalCommissionCompany']").val(officeFeeCommTotal).blur();
		tabObj.find("[name='totalBankProvisionCompany']").val(officeProvisionTotal).blur();
		
		var totalCompanyRevenue = officeFeeCommTotal + officeProvisionTotal + orLeadTotal; 
		tabObj.find("[name='totalCompanyRevenue']").val(totalCompanyRevenue).blur();

		var closingFeeTotal = 0;
		tabObj.find("[name='agentClosingFee[]']").each(function () { closingFeeTotal += parseInt(unformatCurrency($(this).val())) || 0; })
		
		var downpaymentSettlement = (totalDP > 0) ? totalDP - (agencyFee + closingFeeTotal + orLeadTotal) : 0;
		
		//if (downpaymentSettlement < 0) downpaymentSettlement = 0;
		tabObj.find("[name='downpaymentSettlement']").val(downpaymentSettlement).blur();
 		var balance = transactionTotal - totalDP;
 		tabObj.find("[name='balance']").val(balance).blur();
		 
	}

	this.updatePercentageAgent = function updatePercentageAgent() {

		var agentkey = tabObj.find("[name=hidEmployeeKey]").val();

		$.ajax({
			type: "GET",
			url: 'ajax-employee.php',
			async: false,
			data: "action=getDataRowById&pkey=" + agentkey,
		}).done(function (data) {

			if (!data) return;

			data = JSON.parse(data);
			data = data[0];
			if (data.length == 0) return;

		});


	}

	this.updatePercentageType = function updatePercentageType() {
 
			var typekey = tabObj.find("[name=selType]").val();

			$.ajax({
				type: "GET",
				url: 'ajax-sales-order-property-type.php',
				async: false,
				data: "action=getDataRowById&pkey=" + typekey,
			}).done(function (data) {
				if (!data) return;

				data = JSON.parse(data);
				data = data[0];

				if (data.length == 0) return;

				tabObj.find("[name=agencyPercentage]").val(data.percentagevalue).change().blur();

			}); 

	}

	this.afterRemoveRowHandler = function afterRemoveRowHandler() {
		thisObj.calculateTotal();
	}

	this.onChangeBuyer = function onChangeBuyer() {
		thisObj.rebindDownpayment();
	}

	this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler() {
		thisObj.rebindDownpayment();
	}
 
		
	this.rebindDownpayment = function rebindDownpayment() {
		var customerkey = tabObj.find("[name=hidBuyerKey]").val() || 0;
		bindAutoCompleteForTransactionDetail('downpaymentCode[]', objAndValueForDPDetailAutoComplete, 'ajax-customer-downpayment.php?action=searchData&customerkey=' + customerkey);
		tabObj.find("[name=\"downpaymentAmount[]\"]").bind("change", function (event) { thisObj.calculateTotal(); })
	}

	this.rebindEl = function rebindEl() {

		var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");
		bindEl(tableDownPaymentDetail.find('.mnv-detail-field'), 'change', function () { onChangePaymentMethodHandler(thisObj, tableDownPaymentDetail, 'downpayment-row-template'); });
		bindEl(tableDownPaymentDetail.find('.remove-button'), 'click', function () { removeDetailRows(this); onChangePaymentMethodHandler(thisObj, tableDownPaymentDetail, 'downpayment-row-template'); });

		bindEl(tabObj.find("[name='cobrokePercentage[]'], [name='commissionPercentage[]'], [name='provisionPercentage[]'], [name='agentClosingFee[]']"), 'change', function () {thisObj.calculateRowAgent($(this),true); });
		
		thisObj.rebindDownpayment();

		bindAutoCompleteForTransactionDetail('agentName[]', objAndValueForAgenFeeDetailAutoComplete, 'ajax-employee.php?action=searchData' );

	}

	this.calculateAgencyFee = function calculateAgencyFee(){
		var transactionTotal = parseFloat(unformatCurrency(tabObj.find("[name='transactionTotal']").val())) || 0;
		var percentage = parseFloat(unformatCurrency(tabObj.find("[name='agencyPercentage']").val())) || 0;
		var commissionType = parseInt(unformatCurrency(tabObj.find("[name='selCommissionType']").val())) || 0; 
		if(commissionType == 2) 
			tabObj.find("[name='agencyFee']").val(transactionTotal * percentage / 100).blur(); 

	}
	
	this.calculateProvisionFee = function calculateProvisionFee(){
		var percentage = parseFloat(unformatCurrency(tabObj.find("[name='bankProvisionPercentage']").val())) || 0;
		var commissionType = parseInt(unformatCurrency(tabObj.find("[name='selProvisionType']").val())) || 0; 
		var bankTotal =  parseInt(unformatCurrency(tabObj.find("[name='bankTotal']").val())) || 0; 
		if(commissionType == 2) 
			tabObj.find("[name='bankProvision']").val(bankTotal * percentage / 100).blur(); 

	}
//	
	this.loadOnReady = function loadOnReady() {

		tabObj.find("[name=transactionTotal], [name=officeFeePercentage], [name=adminFeePercentage], [name=orLeadPercentage], [name=taxFeePercentage],[name=officeFeeBankPercentage],[name=officeFeeBank],[name=bankTotal],[name=totalCommissionCompany],[name=totalBankProvisionCompany],[name=totalCompanyRevenue],[name=totalCommissionAgent],[name=totalBankProvisionAgent],[name=closingFee],[name=cashReward],[name=totalAgentRevenue]").change(function () { thisObj.calculateTotal(this) });

//		tabObj.find("[name='cobrokePercentage[]'], [name='commissionPercentage[]'], [name='provisionPercentage[]'], [name='agentClosingFee[]']").change(function () {
//			thisObj.calculateRowAgent($(this));
//		});
		
		tabObj.find("[name=bankProvision], [name=agencyFee]").change(function () {
			thisObj.calculateAllAgent();
		});

		
		tabObj.find("[name=agencyPercentage] ").change(function () {
			thisObj.calculateAgencyFee();
			thisObj.calculateAllAgent();
		});
		
		tabObj.find("[name=bankProvisionPercentage] ").change(function () {
			thisObj.calculateProvisionFee(); 
			thisObj.calculateAllAgent();
		});

		tabObj.find("[name=selProvisionType]").change(function () {  thisObj.calculateTotal(this); thisObj.changeProvisionType(); });
		tabObj.find("[name=selCommissionType]").change(function () {  thisObj.calculateTotal(this); thisObj.changeSelDiscountType(); });
		
		//thisObj.changeProvisionType(); // untuk hide select

		tabObj.find("[name=selType]").change(function () { thisObj.updatePercentageType(); });
		tabObj.find("[name=officeFeePercentage]").change(function () {
			if ($(this).attr("dont-change") && $(this).attr("dont-change") == 1) {
				$(this).attr("dont-change", 0);
				return;
			}
			var thisPercentage = parseFloat(unformatCurrency($(this).val())) || 0;
			if (thisPercentage > 100) thisPercentage = 100;
			if (thisPercentage < 0) thisPercentage = 0;
		});


		tabObj.find("[name=officeFeeBankPercentage]").change(function () {
			if ($(this).attr("dont-change") && $(this).attr("dont-change") == 1) {
				$(this).attr("dont-change", 0);
				return;
			}
			var thisPercentage = parseFloat(unformatCurrency($(this).val())) || 0;
			if (thisPercentage > 100) thisPercentage = 100;
			if (thisPercentage < 0) thisPercentage = 0;
		});


		// kalo pas awal kechange, nilai komisi akan selalu load ulang, jd tidak sama dengan yg disave di DB
		if (!this.rs) tabObj.find("[name=selType]").change();

		tabObj.find("[name=selCommissionType]").change();

		if (tabObj.find(".downpayment-row").length == 0) addNewTemplateRow("downpayment-row-template");

		var firstCobrokePercentage = tabObj.find("[name='cobrokePercentage[]']").first();
		if(firstCobrokePercentage.val() == 0) firstCobrokePercentage.val(100);
		
		thisObj.rebindEl();

	}
}
