function LogisticSalesOrder(tabID, rs, cashTOP, opt) {
	var thisObj = this;
	var tabObj = $("#" + tabID);

	var transportationType = opt['transportationType'];
	this.tabID = tabID;
  
	this.updateSender = function updateSender() {
		var senderkey = tabObj.find("[name='hidSenderKey']").val();
 
		$.ajax({
			url: 'ajax-customer',
			type: 'GET',
			async: false,
			data: "action=getDataRowById&pkey=" + senderkey,
		}).done(function (a) {
			var data = JSON.parse(a);
			data = data[0];
			tabObj.find("[name='senderPhone']").val(data.phone);
			tabObj.find("[name='senderAddress']").val(data.address);
			tabObj.find("[name='senderCity']").val(data.cityandcategoryname);
			tabObj.find("[name='hidSenderCityKey']").val(data.citykey); 
			thisObj.calculate();
		})

	}

	this.updateRecipient = function updateRecipient() {
		var reciepentkey = tabObj.find("[name='hidRecipientKey']").val();
		$.ajax({
			url: 'ajax-customer',
			type: 'GET',
			async: false,
			data: "action=getDataRowById&pkey=" + reciepentkey,
		}).done(function (a) {
			var data = JSON.parse(a);
			data = data[0];
			tabObj.find("[name='recipientPhone']").val(data.phone);
			tabObj.find("[name='recipientCity']").val(data.cityandcategoryname);
			tabObj.find("[name='recipientAddress']").val(data.address);
			tabObj.find("[name='hidRecipientCityKey']").val(data.citykey); 
			thisObj.calculate();
		})

	}
 


	this.updatePriceUnit = function updatePriceUnit(priceInUnitObj,weight) {

		var hidSenderCityKey = tabObj.find("[name='hidSenderCityKey']").val();
		var hidRecipientCityKey = tabObj.find("[name='hidRecipientCityKey']").val();
		var selTransportation = tabObj.find("[name='selTransportation']").val(); 
		var totalWeight = 0;
		
		tabObj.find("[name='detailFinalWeight[]']").each(function () {
			totalWeight += parseFloat(unformatCurrency($(this).val())) || 0;
		});
		
		$.ajax({
			url: 'ajax-logistic-sales-order',
			type: 'GET',
			async: false,
			data: {
				action: 'calculateTotalShippingPrice',
				senderCityKey: hidSenderCityKey,
				recipientCityKey: hidRecipientCityKey,
				transportationkey: selTransportation,
				weightDetail: weight,
				totalWeight: totalWeight,
			},
			success : function (data) {   
				
				if(!data) {
					priceInUnitObj.val(0);
					return;		   
				}
			
				data = JSON.parse(data) ;    
				 
				priceInUnitObj.val(data.total); 
			},
			error : function() {
				priceInUnitObj.val(0);  
        	}
			 
		});
		
	}

 
	this.calculateDetail = function calculateDetail(obj) {
		var row = obj.closest('.transaction-detail-row');
		
		var transportationKey = $("[name='selTransportation']").val();
		var priceInUnitObj = row.find("[name='priceInUnit[]']");
			
		var detailWeight = Math.ceil(parseInt(unformatCurrency(row.find("[name='detailWeight[]']").val())));
		var length = parseInt(unformatCurrency(row.find("[name='detailLength[]']").val())) || 0;
		var width = parseInt(unformatCurrency(row.find("[name='detailWidth[]']").val())) || 0;
		var height = parseInt(unformatCurrency(row.find("[name='detailHeight[]']").val())) || 0;
		 
		var volume = length * width * height; 
 
		var division =  parseInt(transportationType[ transportationKey ]['division']);  
		var weightCBM = Math.ceil(volume / division);  
		
		var finalWeight = detailWeight;
		if (weightCBM > detailWeight) finalWeight = weightCBM;
   
		row.find("[name='detailFinalWeight[]']").val(finalWeight).blur();
		var price = 0;
            
        thisObj.updatePriceUnit(priceInUnitObj,finalWeight);
        price  = priceInUnitObj.val();
   
        
            
		row.find("[name='detailCBMWeight[]']").val(weightCBM).blur();
        row.find("[name='detailSubtotal[]']").val(price).blur().change();

	}

	this.calculateTotal = function calculateTotal() {
		var subtotal = 0;
		var totalWeight = 0;  
		var totalPrice = 0;
		var totalQty = parseInt(tabObj.find("[name='hidDetailKey[]']:enabled").length);
		
		var packingFee = parseFloat(unformatCurrency(tabObj.find("[name='packingFee']").val())) || 0;
		tabObj.find("[name='detailFinalWeight[]']").each(function () {
			totalWeight += parseFloat(unformatCurrency($(this).val())) || 0;
		});
		
		tabObj.find("[name='detailSubtotal[]']").each(function () {
			totalPrice += parseFloat(unformatCurrency($(this).val())) || 0;
		});
		
		
		totalWeight = Math.ceil(totalWeight);
		 
		tabObj.find("[name='totalWeight']").val(totalWeight).blur();
		tabObj.find("[name='totalQty']").val(totalQty).blur();


		var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0;
		var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0;
		var etcCost = parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0;//lain lain
		var includeTax = tabObj.find("[name='chkIncludeTax']").val();
		var taxPercentage = parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0;

		totalPrice = (totalPrice == 0) ? 0 : parseInt(totalPrice);
		subtotal = totalPrice;

		tabObj.find("[name='subtotal']").val(subtotal).blur();
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

		var total = subtotal + etcCost + packingFee;
		tabObj.find("[name='total'],[name='grandTotal']").val(total).blur();
 
		var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

		var balance = totalPayment - total;
		tabObj.find("[name='balance']").val(balance).blur(); 

	}

	this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler() {
		thisObj.calculateTotal();
	}

	this.afterRemoveRowHandler = function afterRemoveRowHandler() {
		thisObj.calculateTotal();

		//saat after remove atau di hapus dia calculateDetail lagi
		thisObj.calculate();
	}

	this.calculate = function calculate() {
		tabObj.find("[name='detailWeight[]']:enabled").each(function() { 
				thisObj.calculateDetail($(this)); 
		})
	}

	this.rebindEl = function rebindEl() {
		bindEl(tabObj.find("[name='detailWeight[]'],[name='detailLength[]'], [name='detailWidth[]'], [name='detailHeight[]']"), 'change', function () { thisObj.calculateDetail($(this)); });
		bindEl(tabObj.find("[name='selDiscountType[]']"), 'change', function () { updateDecimal(this); thisObj.calculateDetail(this) }); 
		bindEl(tabObj.find("[name='detailSubtotal[]']"), 'change', function () { thisObj.calculateTotal() }); 
	}


	this.loadOnReady = function loadOnReady() {
 
		// ganti kota knp gk ganti ?
		tabObj.find("[name=selTransportation],[name=senderCity],[name=recipientCity]").change(function () {
			thisObj.calculate();
		})

		tabObj.find("[name=price],[name=totalWeight],[name=grandTotal],[name=subtotal], [name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax], [name=etcCost], [name=taxPercentage],[name=packingFee]").change(function () { thisObj.calculateTotal() })

		tabObj.find(".form-detail-button").click(function () {
			tabObj.find(".form-detail-field").toggle("highlight");
			var temp = tabObj.find(".form-detail-button").attr("relalt");
			tabObj.find(".form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
			tabObj.find(".form-detail-button").text(temp);
		});

		tabObj.find("[name=selTermOfPaymentKey]").change(function () {

			for (i = 0; i < cashTOP.length; i++) {
				if ($(this).val() == cashTOP[i]) {
					tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function () { $(this).click() });
					tabObj.find(".cashTOP").hide();
					return;
				}
			}
			tabObj.find(".cashTOP").show();
		});
		tabObj.find(".form-detail-field").toggle();
		tabObj.find("[name=selFinalDiscountType]").change(function () { updateFinalDiscountDecimal(this) })
		thisObj.rebindEl();
	}
 
}
