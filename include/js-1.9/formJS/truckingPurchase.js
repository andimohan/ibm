function TruckingPurchase(tabID, cashTOP, data, varConstant) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    // utk handle custom code    
    this.tabObj = tabObj;
    this.soDetailCol = {}; 

    var objAndValue = new Array;
    objAndValue.push({
        object: 'hidWOKey[]',
        value: 'pkey'
    });
    objAndValue.push({
        object: 'salesOrderDetailCode[]',
        value: 'socode'
    });
    objAndValue.push({
        object: 'workOrderDate[]',
        value: 'wodate',
        type: 'date'
    });
    objAndValue.push({
        object: 'detailSubtotal[]',
        value: 'total'
    });
    objAndValue.push({
        object: 'hidSODetailKey[]',
        value: 'sokey'
    });
    var objAndValueForDetailAutoComplete = objAndValue;

    var objAndValue = new Array;
    objAndValue.push({
        object: 'hidSOKey[]',
        value: 'pkey'
    });
    var objAndValueForJobOrderDetailAutoComplete = objAndValue;

    var  objAndValue = new Array;
    objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});
    objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'}); 
    var objAndValueForDPDetailAutoComplete  = objAndValue; 
    
    this.tabID = tabID;

    this.updateDetail = function updateDetail(target, objAndValue, ui) {
        var detailRow = $(target).closest(".transaction-detail-row");

        thisObj.updateRowInformation(detailRow, objAndValue, ui);
        thisObj.updateSODetail(detailRow);
        thisObj.calculateTotal();
        // thisObj.updateDefaultDownpayment();

    }

//    this.updateOnChange = function updateOnChange(target, objAndValue, ui) {
//        var detailRow = $(target).closest(".transaction-detail-row");
//        thisObj.disabledAmount(detailRow);
//    }


//    this.disabledAmount = function disabledAmount(detailRow) {
//
//        // var detailKey = detailRow.find("[name='selInvoiceType[]']").first().val();
//
//        // if (detailKey == 1) {
//        //     $readonly = true;
//        //     thisObj.calculateTotal();
//        // } else {
//        // }
//        detailRow.find("[name='amount[]']").val(0);
//        $readonly = false;
// 
//
//        if ($readonly)
//            detailRow.find("[name='amount[]']").attr("tabIndex", "-1");
//        else
//            detailRow.find("[name='amount[]']").removeAttr("tabIndex");
//
//    }

    
    this.updateSODetail = function updateSODetail(row) {
 
        var serviceSelector = 'service-row-template'; 
        row.find(".options-row .service-detail-row").remove();
        row.find(".options-row").show();
        
        var WOKey = row.find("[name=\"hidWOKey[]\"]").val();
        var data = thisObj.soDetailCol[WOKey];
        
        for (i = 0; i < data.length; i++) {
            var arrPostValue = [];
            var subtotal = data[i].outstandingqty * data[i].priceinunit;
            var taxValue = data[i].taxpercentage / 100 * subtotal; // asumsi semua exclude dulu
            var beforeTax = subtotal;
            var total = subtotal + taxValue;
                
            arrPostValue.push({
                "selector": "hidWODetailKey",
                "value": data[i].wodetailkey
            }); 
            arrPostValue.push({
                "selector": "qtyDetail",
                "value": data[i].outstandingqty
            });
            arrPostValue.push({
                "selector": "itemNameDetail",
                "value": data[i].itemname
            });
            arrPostValue.push({
                "selector": "hidItemDetailKey",
                "value": data[i].itemkey
            });
            arrPostValue.push({
                "selector": "remarkDetail",
                "value": data[i].remark
            });
            arrPostValue.push({
                "selector": "priceInUnitDetail",
                "value": data[i].priceinunit
            });
            arrPostValue.push({
                "selector": "taxDetail",
                "value": data[i].taxpercentage
            });
            arrPostValue.push({
                "selector": "taxValueDetail",
                "value": data[i].taxvalue
            });
            arrPostValue.push({
                "selector": "tax23PercentageDetail",
                "value": data[i].tax23percentage
            }); 
            arrPostValue.push({
                "selector": "detailType",
                "value": data[i].purchasetype
            }); 
            arrPostValue.push({
                "selector": "subtotalDetail",
                "value": subtotal
            });  
            
            if (varConstant.usePPNDetail == 1) {
                arrPostValue.push({
                    "selector": "afterTaxDetail",
                    "value": total
                });
                arrPostValue.push({
                    "selector": "beforeTaxDetail",
                    "value": beforeTax
                });
            }
            newrow = addNewTemplateRow(serviceSelector, JSON.stringify(arrPostValue),row);
            newrow.addClass("service-detail-row"); // utk perhitungan total detail
            newrow.find("[name='chkIsReimburse[]']").val(data[i].isreimburse).change();
             
            newrow.find("[name='chkService[]']").val(1).change();
            newrow.find("[name='chkIsTax23[]']").val(data[i].istax23).change();
            newrow.find("[name='chkIncludeTaxDetail[]']").val(data[i].ispriceincludetax).change();
        }
           

        tabObj.find(".inputnumber, .inputdecimal, .input-integer").blur(); 

        // harus hitung ulang, karena onchangenaya chkbox gk update total karena detail arraynya sudah beda 
//        thisObj.calculateTotal();
    }
     

    this.importData = function importData() {
 
        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;

        var arrSOKey = [];
        var supplierKey = tabObj.find("[name=hidSupplierKey]").val();
        tabObj.find("[name='hidSOKey[]']").each(function () {   
            if ( $(this).val() != '')  
                arrSOKey.push($(this).val());
        });

        $.ajax({
            type: "GET",
            url: 'ajax-trucking-service-work-order.php',
            beforeSend: function (xhr) {
                // thisObj.resetDetails();
                clearAllRows(tabObj.find(".invoice-detail"));
                thisObj.activeAjaxConnections++;
            },
            data: 'action=searchAvailableItemForPurchase&supplierkey=' + supplierKey + '&SOKey=' + arrSOKey,
            success: function (data) {
                
                if (!data) {return} ;
                var data = JSON.parse(data);
                if (data.length == 0) return ;
                var i;
                
                for (i = 0; i < data.length; i++) { 
                     
                    var arrPostValue = [];
                    arrPostValue.push({
                        "selector": "hidWOKey",
                        "value": data[i].pkey
                    });
                    arrPostValue.push({
                        "selector": "WOCode",
                        "value": data[i].code
                    });
                    arrPostValue.push({
                        "selector": "salesOrderDetailCode",
                        "value": data[i].socode
                    });
                    arrPostValue.push({
                        "selector": "hidSODetailKey",
                        "value": data[i].sokey
                    });
                    arrPostValue.push({
                        "selector": "detailSubtotal",
                        "value": data[i].total
                    });
                    arrPostValue.push({
                        "selector": "workOrderDate",
                        "value": moment(data[i].trdate).format(_DATE_FORMAT_)
                    });
                    
                    
                    var newrow = addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));
                    
                    thisObj.soDetailCol[data[i].pkey] = data[i].detail;
                    thisObj.updateSODetail(newrow);
                     
                }

                // cukup sekali, karena dalamnya sudah ad looping 
                thisObj.rebindEl();
                tabObj.find("[name='qtyDetail[]']").change(); // utk trigger hitung taxValue
                
                // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber

                tabObj.find(".inputnumber, .input-integer, .inputdecimal").blur(); 
                thisObj.calculateTotal();
 

                decreaseActiveAjaxConnections(thisObj);

                tabObj.find("[name='chkPick-master']").val(1).change();
                // thisObj.updateDefaultDownpayment();

            },
            error: function (xhr, errDesc, exception) {
                //decreaseActiveAjaxConnections(thisObj);
            },
            complete: function (xhr, errDesc, exception) {
                decreaseActiveAjaxConnections(thisObj);
            }
        });
    }
 
    this.resetDetails = function resetDetails() {

        // clearAllRows($("#" + tabID));
        clearAllRows(tabObj.find(".invoice-detail"));
		
        thisObj.updateVoucher();
        thisObj.calculateTotal();

    }

    this.resetDetails = function resetDetails() {
        // clearAllRows(tabObj.find(".mnv-transaction"));
        // clearAllRows(tabObj.find(".mnv-downpayment"));

        addNewTemplateRow("downpayment-row-template");
        thisObj.rebindDownpayment();

        thisObj.calculateTotal();
    }

    this.updateRowInformation = function updateRowInformation(detailRow, objAndValue, ui) {

        var i;
        for (i = 0; i < objAndValue.length; i++) {

            if (objAndValue[i].type == "date")
                ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

            detailRow.find("[name='" + objAndValue[i].object + "']").first().val(ui.item[objAndValue[i].value]).blur();
        }
        thisObj.soDetailCol[ui.item.pkey] = ui.item.detail;
        thisObj.calculateTotal();

        // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
        detailRow.find("[name='WOCode[]']").first().val(ui.item['value']);
    }

    this.updateSupplierInformation = function updateSupplierInformation(obj, event, ui) { 

        var topkey = 0;
        var companybankkey = 0;

        if (tabObj.find("[name=hidCurrentSupplierKey]").val() != '') {
            $("#dialog-message").html("Merubah pelanggan akan mereset detail transaksi.");
            $("#dialog-message").dialog({
                width: 300,
                modal: true,
                title: "Konfirmasi Perubahan Data Supplier",
                open: function () {
                    $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                },
                close: function () {
                    tabObj.find("[name=hidSupplierKey]").val(tabObj.find("[name=hidCurrentSupplierKey]").val());
                    tabObj.find("[name=supplierName]").val(tabObj.find("[name=hidCurrentSupplierName]").val());
                    //tabObj.find("[name=customerTaxId]" ).val(tabObj.find("[name=hidCurrentCustomerTax23]").blur();
                    $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));

                    thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah

                },
                buttons: {
                    OK: function () {
                        if (ui.item == null) {
                            clearAutoCompleteInput(obj, 'hidCustomerKey');
                            tabObj.find("[name=hidCurrentSupplierKey]").val('');
                            tabObj.find("[name=hidCurrentSupplierName]").val('');
                            //tabObj.find("[name=hidCurrentCustomerTax23]" ).val('');
                        } else {
                            tabObj.find("[name=hidCurrentSupplierKey]").val(ui.item.pkey);
                            tabObj.find("[name=hidCurrentSupplierName]").val(ui.item.value);
                            //tabObj.find("[name=hidCurrentCustomerTax23]" ).val(ui.item.taxid); 

                            var tax23Percentage = (ui.item.taxid) ? 2 : 4;
                            tabObj.find("[name='tax23Percentage']").val(tax23Percentage).change().blur();

                            topkey = ui.item.termofpaymentkey;
                            companybankkey = ui.item.companybankkey;
                        }

                        thisObj.resetDetails();
                        $(this).dialog("close");
                    },
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                },
            });
        } else {
            if (ui.item == null) {
                clearAutoCompleteInput(obj, 'hidCustomerKey');
                tabObj.find("[name=hidCurrentSupplierKey]").val('');
                tabObj.find("[name=hidCurrentSupplierName]").val('');
                //tabObj.find("[name=hidCurrentCustomerTax23]" ).val('');
            } else {
                tabObj.find("[name=hidCurrentSupplierKey]").val(ui.item.pkey);
                tabObj.find("[name=hidCurrentSupplierName]").val(ui.item.value);
                //tabObj.find("[name=hidCurrentCustomerTax23]" ).val(ui.item.taxid); 

                var tax23Percentage = (ui.item.taxid) ? 2 : 4;
                tabObj.find("[name='tax23Percentage']").val(tax23Percentage).change().blur();

                topkey = ui.item.termofpaymentkey;
                companybankkey = ui.item.companybankkey;
            }

			thisObj.updateVoucher();
            thisObj.rebindEl();
        }


        if (tabObj.find("[name=selTermOfPayment] option[value='" + topkey + "']").length > 0)
            tabObj.find("[name=selTermOfPayment]").val(topkey).change();


        if (tabObj.find("[name=selBank] option[value='" + companybankkey + "']").length > 0)
            tabObj.find("[name=selBank]").val(companybankkey);
        
        thisObj.rebindDownpayment();
        
    }

    this.calculateDetail = function calculateDetail(obj) {

        var rowObj = $(obj).closest(".transaction-detail-row");

        var qty = parseFloat(unformatCurrency(rowObj.find("[name='qty[]']").val())) || 0;
        var priceInUnit = parseFloat(unformatCurrency(rowObj.find("[name='priceInUnit[]']").val())) || 0;

        var subtotal = qty * priceInUnit;
        rowObj.find("[name='detailSubtotal[]']").val(subtotal).blur();

        thisObj.calculateTotal();
    }

    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler() {
        // fungsi ini dipanggil asal add new row utk detail DP, payment method dsb
        thisObj.calculateTotal();
        thisObj.rebindDownpayment();  
    }

        
    this.rebindDownpayment = function rebindDownpayment(){ 
//        var supplierkey = tabObj.find("[name=hidSupplierKey]").val() || 0;  
//        var currencykey = 1; // sementara tembak IDR
//        bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete,'ajax-supplier-downpayment.php?action=searchData&supplierkey='+supplierkey+'&currencykey='+ currencykey);  
    } 
    
    
    this.calculateTotal = function calculateTotal() {

        var amount = 0,
            qty, price, serviceRow;
        var tax23 = 0;
        var totalPPNDetail = 0; 

        var totalUsePPN = 0;

        tabObj.find("[name='chkPick[]']").each(function () {

            if ($(this).val() != 1) return;

            row = $(this).closest(".transaction-detail-row");
            objSubtotal = row.find("[name='detailSubtotal[]']");
//            objDownpayment = row.find("[name='salesOrderDownpayment[]']");
            objTotal = row.find("[name='amount[]']");

            invoiceType = row.find("[name='selInvoiceType[]']").val();

 
            var rowTotal = 0;

            row.find("[name='chkService[]']").each(function () {
                if ($(this).val() != 1) return;

                var serviceRow = $(this).closest(".service-detail-row");
                var beforeTaxDetail = parseInt(unformatCurrency(serviceRow.find("[name='beforeTaxDetail[]']").val())) || 0;
                var subtotalDetail = parseInt(unformatCurrency(serviceRow.find("[name='subtotalDetail[]']").val())) || 0;
                var ppnDetail = parseInt(unformatCurrency(serviceRow.find("[name='taxDetail[]']").val())) || 0;
                var ppnValueDetail = parseInt(unformatCurrency(serviceRow.find("[name='taxValueDetail[]']").val())) || 0;
                var tax23PercentageDetail = parseInt(unformatCurrency(serviceRow.find("[name='tax23PercentageDetail[]']").val())) || 0;

                if (varConstant.usePPNDetail == 1)
                    totalUsePPN += beforeTaxDetail;

                rowTotal += subtotalDetail;
 
                // kalo ad tax23
                // if (serviceRow.find("[name='chkIsTax23[]']").val() == 1)
                //     tax23 += (varConstant.usePPNDetail == 1) ? beforeTaxDetail : subtotalDetail;
                tax23 += (tax23PercentageDetail / 100) * beforeTaxDetail;


                if (ppnDetail > 0)
                    totalPPNDetail += ppnValueDetail;

            });

            objSubtotal.val(rowTotal).blur();
//            downpayment = parseInt(unformatCurrency(objDownpayment.val())); 
//            rowTotal = rowTotal - downpayment;
//            if (rowTotal < 0) rowTotal = 0;

            objTotal.val(rowTotal).blur();
     
            amount += rowTotal;
        })

        // HITUNG TOTAL
        var subtotal = amount;

        tabObj.find("[name='subtotal']").val(subtotal).blur();
        // var totalDP = thisObj.calculateTotalDownpayment();
        var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0;
        var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0;
        var includeTax = tabObj.find("[name='chkIncludeTax']").val();
        var taxPercentage = parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0;
        var stampFee = parseFloat(unformatCurrency(tabObj.find("[name='stampFee']").val())) || 0;

        //var hasTaxID =   tabObj.find("[name='customerTaxId']").val();

        if (finalDiscount != 0) {
            if (finalDiscountType == 2)
                finalDiscount = finalDiscount / 100 * subtotal;
        }

        subtotal -= finalDiscount;

        var beforeTaxTotal = subtotal;

        var taxValue = 0;

        if (varConstant.usePPNDetail == 1) {
            beforeTaxTotal = totalUsePPN;
            taxValue = totalPPNDetail;
            subtotal = beforeTaxTotal + taxValue;
        } else {

            if (includeTax == 0) {
                taxValue = subtotal * taxPercentage / 100;
                subtotal += taxValue;
            } else {
                taxValue = (taxPercentage / (100 + taxPercentage)) * subtotal;
                beforeTaxTotal = subtotal - taxValue;
            }
        }

        //var tax23Percentage =  (!hasTaxID) ? 4 :  2; 
        //tabObj.find("[name='tax23Percentage']").val(tax23Percentage).blur();

        tabObj.find("[name='tax23Value']").val(tax23).blur();
        // tabObj.find("[name='hidTotalBeforeTaxPPH23']").val(tax23).blur();
        tabObj.find("[name='beforeTaxTotal']").val(beforeTaxTotal).blur();
        tabObj.find("[name='taxValue']").val(taxValue).blur();

        var total = subtotal + stampFee;

        tabObj.find("[name='total']").val(total).blur();

        var totalPayment = 0;
        tabObj.find("[name='paymentMethodValue[]']").each(function () {
            totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
        })
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();


        // var balance = totalPayment + totalDP - total;
        var balance = totalPayment - total;
        tabObj.find("[name='balance']").val(balance).blur();

        // thisObj.calculateTax23();
        //thisObj.addPaymentRowIfNeeded(); 

    }

    //this.calculateTax23 = function calculateTax23() {
//
    //    var tax23Percentage = parseFloat(unformatCurrency(tabObj.find("[name='tax23Percentage']").val())) || 0;
    //    var beforeTaxTotal = parseFloat(unformatCurrency(tabObj.find("[name='hidTotalBeforeTaxPPH23']").val())) || 0;
    //    
    //    if (varConstant.usePPNDetail == 1) {
    //        tax23Value = (tax23Percentage / 100) * beforeTaxTotal;
    //    } else {     
    //        // utk transaksi normal
    //        var useTax23 = 1;
    //        var tax23Value = 0;
//
    //        if (useTax23 != 0 && tax23Percentage > 0) {
    //            var includeTax = tabObj.find("[name='chkIncludeTax']").val();
    //            var taxPercentage = parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0;
//
    //            if (includeTax == 1)
    //                beforeTaxTotal = beforeTaxTotal - (taxPercentage / (100 + taxPercentage)) * beforeTaxTotal;
//
    //            tax23Value = (tax23Percentage / 100) * beforeTaxTotal;
    //        }
    //    }
//
//
    //    //tabObj.find("[name='tax23Percentage']").val(tax23Percentage).blur();
    //    tabObj.find("[name='tax23Value']").val(tax23Value).blur();
    //}

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    }

    this.onChangeChk = function onChangeChk() {
        thisObj.calculateTotal();
    }

    this.calculateServiceDetail = function calculateServiceDetail(obj) {
        var serviceRow = $(obj).closest(".service-detail-row");

        var qty = parseFloat(unformatCurrency(serviceRow.find("[name='qtyDetail[]']").val())) || 0;
        var price = parseFloat(unformatCurrency(serviceRow.find("[name='priceInUnitDetail[]']").val())) || 0;
        var amount = qty * price;

        if (varConstant.usePPNDetail == 1) {
            var taxValueDetail = parseFloat(unformatCurrency(serviceRow.find("[name='taxDetail[]']").val())) || 0;
            var isInc = serviceRow.find("[name='chkIncludeTaxDetail[]']").val() || 0;

            var taxValue = 0;
            var beforeTaxDetail = amount;
            if (taxValueDetail > 0) {
                if (isInc == 0) {
                    taxValue = amount * taxValueDetail / 100;
                } else {
                    taxValue = (taxValueDetail / (100 + taxValueDetail)) * amount;
                    beforeTaxDetail -= taxValue;
                }
            }

            serviceRow.find("[name='beforeTaxDetail[]']").val(beforeTaxDetail);
            serviceRow.find("[name='afterTaxDetail[]']").val((beforeTaxDetail + taxValue));
            serviceRow.find("[name='taxValueDetail[]']").val(taxValue); 
        }


        serviceRow.find("[name='subtotalDetail[]']").val(amount);

        serviceRow.find(".inputnumber, .inputdecimal, .input-integer").blur();

        thisObj.calculateTotal();
    }
	
	this.updateVoucher = function updateVoucher(){ 
		// kalo gk pake voucher, gk usah
		if(!varConstant.ADV_FINANCE) return;
		
        var supplierkey = tabObj.find("[name=hidSupplierKey]").val() || 0;  
        var selVoucherObj = tabObj.find("[name='selVoucher[]']");
            
        var ajaxData = "action=getAvailableVoucher&supplierkey=" + tabObj.find("[name=hidSupplierKey]").val(); 
         $.ajax({
            type: "GET",
            url:  'ajax-cash-bank.php',
            async : false,
            beforeSend:function (xhr){
                  selVoucherObj.each(function(){  $('option', $(this)).remove();  }) 
            },
            data: ajaxData,
            success: function(data){ 
                        // update combobox services 
                        if(!data) return; 
                        data = JSON.parse(data); 
                        var selectOpt = data;
                        reInsertSelectBox(selVoucherObj,selectOpt, {"key" : "pkey", "label" : "voucherlabel", "rel" : {"rel-amount" : "outstanding"}} );  
            }  
        }); 
    }

    this.rebindEl = function rebindEl() {
 
        var handling = [];
        handling.onSelectFunction = 'getTabObj().updateDetail';
//        handling.onChangeFunction = 'getTabObj().updateOnChange';

        var supplierKey = tabObj.find("[name=hidSupplierKey]").val();
        var arrSODetailKey = [];
        tabObj.find("[name='hidSOKey[]']").each(function () { 
            if ($(this).val() != '')  
                arrSODetailKey.push($(this).val());
        });
        
        bindAutoCompleteForTransactionDetail('WOCode[]', objAndValueForDetailAutoComplete, 'ajax-trucking-service-work-order.php?action=searchAvailableItemForPurchase&supplierkey=' + supplierKey + '&SOKey=' + arrSODetailKey, handling);
        bindAutoCompleteForTransactionDetail('SOCode[]', objAndValueForJobOrderDetailAutoComplete, 'ajax-trucking-service-order.php?action=searchAvailableJobOrderForPurchase&supplierkey=' + supplierKey);
       
        
        bindEl(tabObj.find("[name='hidSOKey[]']"), 'change', function () {
            thisObj.rebindEl();
        });
     
        
        bindEl(tabObj.find("[name='amount[]'], [name='chkService[]'],[name='taxDetail[]'], [name='tax23PercentageDetail[]']"), 'change', function () {
            thisObj.calculateTotal();
        });
        bindEl(tabObj.find("[name='qtyDetail[]'],[name='taxDetail[]'],[name='chkIncludeTaxDetail[]']"), 'change', function () { 
            thisObj.calculateServiceDetail(this);
        });
        bindEl(tabObj.find("[name='dummychkPick[]']"), 'change', function () {
            updateChkMaster(this, thisObj.onChangeChk);
        });
        
        var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");
        bindEl(tableDownPaymentDetail.find('.mnv-detail-field'), 'change', function () {
            onChangePaymentMethodHandler(thisObj, tableDownPaymentDetail, 'downpayment-row-template');
        });
        bindEl(tableDownPaymentDetail.find('.remove-button'), 'click', function () {
            removeDetailRows(this);
            onChangePaymentMethodHandler(thisObj, tableDownPaymentDetail, 'downpayment-row-template');
        });

        
        thisObj.rebindDownpayment();
        
    }

    this.loadOnReady = function loadOnReady() {
        tabObj.find("[name=selTermOfPayment]").change(function () {
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

        tabObj.find("[name=selTermOfPayment]").change();

        tabObj.find(".form-detail-field").toggle();

        tabObj.find(".form-detail-button").click(function () {
            tabObj.find(".form-detail-field").toggle("highlight");
            var temp = tabObj.find(".form-detail-button").attr("relalt");
            $("#" + tabID + " .form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
            tabObj.find(".form-detail-button").text(temp);
        });

        tabObj.find("[name=btnImport]").on('click', function () {
            thisObj.importData();
        });

        tabObj.find("[name=dummychkPick-master]").change(function () {
            updateChkPick(this, thisObj.onChangeChk)
        });

        tabObj.find("[name=selFinalDiscountType]").change(function () {
            updateFinalDiscountDecimal(this)
        });

        if (!data['rsSODetail'] || data['rsSODetail'].length == 0)
            addNewTemplateRow("job-order-row-template", null, null, thisObj.rebindEl);

        tabObj.find("[name='chkPick-master']").val(1).change();

        // tabObj.find("[name=chkTax23], [name=tax23Percentage]").change(function () {
        //     thisObj.calculateTax23()
        // });
        tabObj.find("[name=beforeTaxTotal], [name=chkIncludeTax], [name=selFinalDiscountType], [name=taxPercentage], [name=finalDiscount], [name=stampFee]").change(function () {
            thisObj.calculateTotal(this)
        });
        tabObj.find("[name=selFinalDiscountType]").change(function () {
            updateFinalDiscountDecimal(this)
        });
        
        
        addNewTemplateRow("downpayment-row-template");  

        thisObj.rebindEl();

    }
}