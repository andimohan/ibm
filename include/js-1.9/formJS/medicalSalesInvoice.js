function MedicalSalesInvoice(tabID, cashTOP, varConstant) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    // utk handle custom code    
    this.tabObj = tabObj;
    this.tablekey = varConstant.tablekey;

    this.customCodeCache = [];

    //var totalRecalculate = 0;

    var objAndValue = new Array;
    objAndValue.push({
        object: 'hidSalesOrderKey[]',
        value: 'pkey'
    });
    objAndValue.push({
        object: 'salesOrderSubtotal[]',
        value: 'grandtotal'
    });
    objAndValue.push({
        object: 'doNumberDetail[]',
        value: 'donumber'
    });
    objAndValue.push({
        object: 'insuredName[]',
        value: 'insuredname'
    });
    objAndValue.push({
        object: 'salesOrderDate[]',
        value: 'trdate',
        type: 'date'
    });
    var objAndValueForDetailAutoComplete = objAndValue;

    var objAndValue = new Array;
    objAndValue.push({
        object: 'hidItemKey[]',
        value: 'pkey'
    });
    objAndValue.push({
        object: 'selUnit[]',
        value: 'deftransunitkey'
    });
    var objAndValueForDetailItemAutoComplete = objAndValue;

    var objAndValue = new Array;
    objAndValue.push({
        object: 'hidDownpaymentKey[]',
        value: 'pkey'
    });
    objAndValue.push({
        object: 'downpaymentAmount[]',
        value: 'outstanding'
    });
    var objAndValueForDPDetailAutoComplete = objAndValue;

    this.tabID = tabID;

    this.getDownpaymentType = function getDownpaymentType() {
        return (tabObj.find("[name='chkDownpayment']").val() == 1) ? true : false;
    }


    this.updateDetail = function updateDetail(target, objAndValue, ui) {
        var detailRow = $(target).closest(".transaction-detail-row");

        thisObj.updateRowInformation(detailRow, objAndValue, ui);
        thisObj.updateSODetail(detailRow);
        thisObj.calculateTotal();
        thisObj.updateDefaultDownpayment();

    }

    this.updateRowInformation = function updateRowInformation(detailRow, objAndValue, ui) {

        var i;
        for (i = 0; i < objAndValue.length; i++) {

            if (objAndValue[i].type == "date")
                ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

            detailRow.find("[name='" + objAndValue[i].object + "']").first().val(ui.item[objAndValue[i].value]).blur();

            thisObj.updatePartialInvoiceOutstanding(detailRow);

        }

        thisObj.calculateTotal();

        // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
        detailRow.find("[name='salesOrderCode[]']").first().val(ui.item['value']);
    }


    this.updatePartialInvoiceOutstanding = function updatePartialInvoiceOutstanding(detailRow) {
        var headerInvoiceType = tabObj.find("[name=selCustomCode]").val();
        var soKey = detailRow.find("[name='hidSalesOrderKey[]']").first().val();

        if (soKey) {
            // update downpayment and amount
            $.ajax({
                type: "GET",
                url: 'ajax-medical-job-order.php',
                async: false,
                data: 'action=getTotalInvoicedAndOutstanding&pkey=' + soKey + '&invoiceType=' + headerInvoiceType,
                success: function (data) {
                    var data = JSON.parse(data);
                    detailRow.find("[name='salesOrderDownpayment[]']").first().val(data.outstanding).blur();
                }
            });
        }

    }


    this.checkDownpaymentExist = function checkDownpaymentExist(dpkey) {

        var found = false;

        tabObj.find("[name='hidDownpaymentKey[]']").each(function () {
            if ($(this).val() == dpkey) {
                found = true;
                return;
            }
        })

        return found;
    }


    this.updateDefaultDownpayment = function updateDefaultDownpayment() {

        var soKey = new Array();
        tabObj.find("[name='hidSalesOrderKey[]']").each(function () {
            if ($(this).val()) soKey.push($(this).val());
        })
        var currencykey = tabObj.find("[name='selCurrency']").val();

        $.ajax({
            type: "GET",
            url: 'ajax-customer-downpayment.php',
            data: 'action=getDownpaymentForTruckingServiceOrderInvoice&sokey=' + JSON.stringify(soKey) + "&currencykey=" + currencykey,
            success: function (data) {

                var data = JSON.parse(data);
                var i;
                for (i = 0; i < data.length; i++) {

                    // kalo blm ad DP yg sama
                    if (!thisObj.checkDownpaymentExist(data[i].pkey)) {

                        var arrPostValue = [];
                        arrPostValue.push({
                            "selector": "downpaymentCode",
                            "value": data[i].code
                        });
                        arrPostValue.push({
                            "selector": "hidDownpaymentKey",
                            "value": data[i].pkey
                        });
                        arrPostValue.push({
                            "selector": "downpaymentAmount",
                            "value": data[i].outstanding
                        });
                        updateTemplateRow("downpayment-row-template", JSON.stringify(arrPostValue))
                    }

                }

                //bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete[tabID],'ajax-customer-downpayment.php?action=searchData&customerkey='+customerkey); 
                thisObj.rebindDownpayment();

                tabObj.find("[name=\"downpaymentAmount[]\"]").change().blur();
                thisObj.calculateTotalDownpayment();
            }
        });

    }


    this.calculateTotalDownpayment = function calculateTotalDownpayment() {
        var totalDP = 0;
        tabObj.find("[name='downpaymentAmount[]']").each(function () {
            totalDP += parseInt(unformatCurrency($(this).val())) || 0;
        })
        tabObj.find("[name='totalDownpayment']").val(totalDP).blur();

        return totalDP;
    }


    this.updateSODetail = function updateSODetail(row) {

        //var totalRows = row.length;
        var arrPkey = [];
        row.each(function () {
            arrPkey.push($(this).find('[name="hidSalesOrderKey[]"]').val());
        });

        var downpaymentType = thisObj.getDownpaymentType();

        var serviceSelector = 'service-row-template';

        // if DP type, hide the options-row  
        // ini harus idipindhan kebawah kah ?
        row.find(".options-row").hide();

        $.ajax({
            type: "GET",
            url: 'ajax-medical-job-order.php',
            beforeSend: function (xhr) {
                row.find(".transaction-detail-row").remove(); //remove all rows  
            },
            async: false,
            data: {
                action: "getUnInvoicedItemDetail",
                pkey: arrPkey
            },
            success: function (data) {
                if (data == "") return;
                var dataSet = JSON.parse(data);



                row.each(function () {

                    var itrRow = $(this);
                    var key = itrRow.find('[name="hidSalesOrderKey[]"]').val();
                    if (dataSet[key] == undefined) return ;
                    var data = dataSet[key];

                    for (i = 0; i < data.length; i++) {
                        var arrPostValue = [];
                        arrPostValue.push({
                            "selector": "hidRefSODetailKey",
                            "value": data[i].pkey
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
                            "selector": "doNumberDetail",
                            "value": data[i].donumber
                        });
                        arrPostValue.push({
                            "selector": "itemNameAliasDetail",
                            "value": data[i].aliasname
                        });
                        arrPostValue.push({
                            "selector": "trDescDetail",
                            "value": data[i].trdesc
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
                            "selector": "subtotalDetail",
                            "value": data[i].total
                        });

                        if (varConstant.usePPNDetail == 1) {
                            arrPostValue.push({
                                "selector": "afterTaxDetail",
                                "value": data[i].total
                            });
                            arrPostValue.push({
                                "selector": "beforeTaxDetail",
                                "value": data[i].total
                            });
                        }
                        newrow = addNewTemplateRow(serviceSelector, JSON.stringify(arrPostValue), itrRow);
                        newrow.addClass("service-detail-row"); // utk perhitungan total detail

                        newrow.find("[name='chkService[]']").val(1).change();
                        newrow.find("[name='chkIsTax23[]']").val(data[i].istax23).change();
                        newrow.find("[name='chkIncludeTaxDetail[]']").val(data[i].ispriceincludetax).change();
                    }
                });

                tabObj.find(".inputnumber, .input-integer").blur();

                // harus hitung ulang, karena onchangenaya chkbox gk update total karena detail arraynya sudah beda 
                thisObj.calculateTotal();

                if (!downpaymentType && data.length > 0)
                    row.find(".options-row").show();

                thisObj.rebindEl();

                //untuk menghitung
                tabObj.find("[name='taxDetail[]']").change();
            }
        });

    }

    this.updateOnChange = function updateOnChange(target, objAndValue, ui) {
        var detailRow = $(target).closest(".transaction-detail-row");
        thisObj.disabledAmount(detailRow);
    }


    this.disabledAmount = function disabledAmount(detailRow) {

        var detailKey = detailRow.find("[name='selInvoiceType[]']").first().val();

        if (detailKey == 1) {
            $readonly = true;
            thisObj.calculateTotal();
        } else {
            detailRow.find("[name='amount[]']").val(0);
            $readonly = false;
        }

        if (!thisObj.getDownpaymentType())
            detailRow.find("[name='amount[]']").prop("readonly", $readonly);

        if ($readonly)
            detailRow.find("[name='amount[]']").attr("tabIndex", "-1");
        else
            detailRow.find("[name='amount[]']").removeAttr("tabIndex");

    }

    this.resetDetails = function resetDetails() {

        clearAllRows($("#" + tabID));
        thisObj.calculateTotal();

    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
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

            serviceRow.find("[name='beforeTaxDetail[]']").val(beforeTaxDetail)
            serviceRow.find("[name='afterTaxDetail[]']").val((beforeTaxDetail + taxValue))
            serviceRow.find("[name='taxValueDetail[]']").val(taxValue)
        }


        serviceRow.find("[name='subtotalDetail[]']").val(amount);

        serviceRow.find(".inputnumber, .input-integer").blur();

        thisObj.calculateTotal();
    }

    this.calculateTax23 = function calculateTax23() {

        var tax23Percentage = parseFloat(unformatCurrency(tabObj.find("[name='tax23Percentage']").val())) || 0;
        var beforeTaxTotal = parseFloat(unformatCurrency(tabObj.find("[name='hidTotalBeforeTaxPPH23']").val())) || 0;

        if (varConstant.usePPNDetail == 1) {
            tax23Value = (tax23Percentage / 100) * beforeTaxTotal;
        } else {
            // utk transaksi normal
            var useTax23 = 1;
            var tax23Value = 0;

            if (useTax23 != 0 && tax23Percentage > 0) {
                var includeTax = tabObj.find("[name='chkIncludeTax']").val();
                var taxPercentage = parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0;

                if (includeTax == 1)
                    beforeTaxTotal = beforeTaxTotal - (taxPercentage / (100 + taxPercentage)) * beforeTaxTotal;

                tax23Value = (tax23Percentage / 100) * beforeTaxTotal;
            }
        }


        //tabObj.find("[name='tax23Percentage']").val(tax23Percentage).blur();
        tabObj.find("[name='tax23Value']").val(tax23Value).blur();
    }

    this.calculateTotal = function calculateTotal() {

        var amount = 0,
            qty, price, serviceRow;
        var tax23 = 0;
        var totalPPNDetail = 0;
        var downpaymentType = thisObj.getDownpaymentType();

        var totalUsePPN = 0;

        tabObj.find("[name='chkPick[]']").each(function () {

            if ($(this).val() != 1) return;

            row = $(this).closest(".transaction-detail-row");
            objSubtotal = row.find("[name='salesOrderSubtotal[]']");
            objDownpayment = row.find("[name='salesOrderDownpayment[]']");
            objTotal = row.find("[name='amount[]']");

            invoiceType = row.find("[name='selInvoiceType[]']").val();


            if (invoiceType == 1 && !downpaymentType) {
                var rowTotal = 0;

                row.find("[name='chkService[]']").each(function () {
                    if ($(this).val() != 1) return;

                    var serviceRow = $(this).closest(".service-detail-row");
                    var beforeTaxDetail = parseInt(unformatCurrency(serviceRow.find("[name='beforeTaxDetail[]']").val())) || 0;
                    var subtotalDetail = parseInt(unformatCurrency(serviceRow.find("[name='subtotalDetail[]']").val())) || 0;
                    var ppnDetail = parseInt(unformatCurrency(serviceRow.find("[name='taxDetail[]']").val())) || 0;
                    var ppnValueDetail = parseInt(unformatCurrency(serviceRow.find("[name='taxValueDetail[]']").val())) || 0;

                    if (varConstant.usePPNDetail == 1)
                        totalUsePPN += beforeTaxDetail;

                    rowTotal += subtotalDetail;

                    // kalo ad tax23
                    if (serviceRow.find("[name='chkIsTax23[]']").val() == 1)
                        tax23 += (varConstant.usePPNDetail == 1) ? beforeTaxDetail : subtotalDetail;

                    if (ppnDetail > 0)
                        totalPPNDetail += ppnValueDetail;

                });

                objSubtotal.val(rowTotal).blur();
                downpayment = parseInt(unformatCurrency(objDownpayment.val()));

                rowTotal = rowTotal - downpayment;
                if (rowTotal < 0) rowTotal = 0;

                objTotal.val(rowTotal).blur();
            } else {
                rowTotal = parseInt(unformatCurrency(row.find("[name='amount[]']").val())) || 0;
            }

            amount += rowTotal;
        })

        // HITUNG TOTAL
        var subtotal = amount;

        tabObj.find("[name='subtotal']").val(subtotal).blur();
        var totalDP = thisObj.calculateTotalDownpayment();
        var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0;
        var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0;
        var includeTax = tabObj.find("[name='chkIncludeTax']").val();
        var taxPercentage = parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0;
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

        tabObj.find("[name='hidTotalBeforeTaxPPH23']").val(tax23).blur();
        tabObj.find("[name='beforeTaxTotal']").val(beforeTaxTotal).blur();
        tabObj.find("[name='taxValue']").val(taxValue).blur();

        var total = subtotal;
        tabObj.find("[name='total']").val(total).blur();

        var totalPayment = 0;
        tabObj.find("[name='paymentMethodValue[]']").each(function () {
            totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
        })
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();


        var balance = totalPayment + totalDP - total;
        tabObj.find("[name='balance']").val(balance).blur();

        thisObj.calculateTax23();
        //thisObj.addPaymentRowIfNeeded(); 

    }

    this.resetDetails = function resetDetails() {
        clearAllRows(tabObj.find(".mnv-transaction"));
        clearAllRows(tabObj.find(".mnv-downpayment"));

        addNewTemplateRow("downpayment-row-template");
        thisObj.rebindDownpayment();

        thisObj.calculateTotal();
    }

    this.importData = function importData() {

        loadOverlayScreen({
            content: _LOADING_TEMPLATE_
        });
        thisObj.activeAjaxConnections = 0;

        var customerkey = tabObj.find("[name=hidCustomerKey]").val();
        var statustype = (thisObj.getDownpaymentType()) ? 'downpayment' : 'sales';


        $.ajax({
            type: "GET",
            url: 'ajax-medical-job-order.php',
            beforeSend: function (xhr) {
                thisObj.resetDetails();
                thisObj.activeAjaxConnections++;
            },
            data: 'action=searchDataForInvoice&statustype=' + statustype + '&customerkey=' + customerkey,
            success: function (data) {

                var data = JSON.parse(data);
                var i;
                var newrow;


                for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];
                    arrPostValue.push({
                        "selector": "hidSalesOrderKey",
                        "value": data[i].pkey
                    });
                    arrPostValue.push({
                        "selector": "salesOrderCode",
                        "value": data[i].value
                    });
                    arrPostValue.push({
                        "selector": "salesOrderSubtotal",
                        "value": data[i].grandtotal
                    });
                    arrPostValue.push({
                        "selector": "doNumberDetail",
                        "value": data[i].donumber
                    });
                    arrPostValue.push({
                        "selector": "insuredName",
                        "value": data[i].insuredname
                    });
                    arrPostValue.push({
                        "selector": "codeLog",
                        "value": data[i].codelog
                    });
                    arrPostValue.push({
                        "selector": "salesOrderDate",
                        "value": moment(data[i].trdate).format(_DATE_FORMAT_)
                    });
                    newrow = addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));


                    thisObj.updatePartialInvoiceOutstanding(newrow);
                    //thisObj.updateSODetail(newrow); // => udah ad di updateFieldOnChangeInvoiveDownpayment 
                }

                // cukup sekali, karena dalamnya sudah ad looping   
                thisObj.updateFieldOnChangeInvoiveDownpayment();

                thisObj.rebindEl();

                // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber

                tabObj.find(".inputnumber, .input-integer, .inputdecimal").blur();
                thisObj.calculateTotal();

                decreaseActiveAjaxConnections(thisObj);

                tabObj.find("[name='chkPick-master']").val(1).change();
                thisObj.updateDefaultDownpayment();

            },
            error: function (xhr, errDesc, exception) {
                decreaseActiveAjaxConnections(thisObj);

            }
        });
    }

    this.onChangeChk = function onChangeChk() {
        thisObj.calculateTotal();
    }

    this.rebindDownpayment = function rebindDownpayment() {
        var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;
        bindAutoCompleteForTransactionDetail('downpaymentCode[]', objAndValueForDPDetailAutoComplete, 'ajax-customer-downpayment.php?action=searchData&customerkey=' + customerkey + '&currencykey=' + tabObj.find("[name=selCurrency]").val());

    }

    this.updateCustomerInformation = function updateCustomerInformation(obj, event, ui) {

        var topkey = 0;
        var companybankkey = 0;

        if (tabObj.find("[name=hidCurrentCustomerKey]").val() != '') {
            $("#dialog-message").html("Merubah pelanggan akan mereset detail transaksi.");
            $("#dialog-message").dialog({
                width: 300,
                modal: true,
                title: "Konfirmasi Perubahan Data Pelanggan",
                open: function () {
                    $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                },
                close: function () {
                    tabObj.find("[name=hidCustomerKey]").val(tabObj.find("[name=hidCurrentCustomerKey]").val());
                    tabObj.find("[name=customerName]").val(tabObj.find("[name=hidCurrentCustomerName]").val());
                    //tabObj.find("[name=customerTaxId]" ).val(tabObj.find("[name=hidCurrentCustomerTax23]").blur();
                    $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));

                    thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah

                },
                buttons: {
                    OK: function () {
                        if (ui.item == null) {
                            clearAutoCompleteInput(obj, 'hidCustomerKey');
                            tabObj.find("[name=hidCurrentCustomerKey]").val('');
                            tabObj.find("[name=hidCurrentCustomerName]").val('');
                            //tabObj.find("[name=hidCurrentCustomerTax23]" ).val('');
                        } else {
                            tabObj.find("[name=hidCurrentCustomerKey]").val(ui.item.pkey);
                            tabObj.find("[name=hidCurrentCustomerName]").val(ui.item.value);
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
                tabObj.find("[name=hidCurrentCustomerKey]").val('');
                tabObj.find("[name=hidCurrentCustomerName]").val('');
                //tabObj.find("[name=hidCurrentCustomerTax23]" ).val('');
            } else {
                tabObj.find("[name=hidCurrentCustomerKey]").val(ui.item.pkey);
                tabObj.find("[name=hidCurrentCustomerName]").val(ui.item.value);
                //tabObj.find("[name=hidCurrentCustomerTax23]" ).val(ui.item.taxid); 

                var tax23Percentage = (ui.item.taxid) ? 2 : 4;
                tabObj.find("[name='tax23Percentage']").val(tax23Percentage).change().blur();

                topkey = ui.item.termofpaymentkey;
                companybankkey = ui.item.companybankkey;
            }

            thisObj.rebindEl();
        }


        if (tabObj.find("[name=selTermOfPayment] option[value='" + topkey + "']").length > 0)
            tabObj.find("[name=selTermOfPayment]").val(topkey).change();


        if (tabObj.find("[name=selBank] option[value='" + companybankkey + "']").length > 0)
            tabObj.find("[name=selBank]").val(companybankkey);
    }

    this.changeInvoiceType = function changeInvoiceType(obj) {
        var invoiceType = $(obj).val();

        $row = $(obj).closest(".transaction-detail-row");
        var detailRows = $row.find(".service-detail-row"); //.not(".service-row-template");

        $row.find("[class*=type-]").hide();
        $row.find(".type-" + invoiceType).show();

        $row.find(".options-row").hide();

        if (invoiceType == 1 && detailRows.length > 0)
            $row.find(".options-row").show();

        thisObj.disabledAmount($row);
        thisObj.calculateTotal();
    }

    this.updateFieldOnChangeInvoiveDownpayment = function updateFieldOnChangeInvoiveDownpayment(rowObj) {


        var transactionRow = (rowObj) ? rowObj : tabObj.find(".invoice-detail > .transaction-detail-row");
        var downpaymentType = thisObj.getDownpaymentType();

        //gabungin sekali query saja agar lebih efisien
        if (!downpaymentType)
            thisObj.updateSODetail(transactionRow);


        transactionRow.each(function () {

            var detailInvoiceType = $(this).find("[name=\"selInvoiceType[]\"]");
            var detailAmount = $(this).find("[name=\"amount[]\"]");
            var optionsRow = $(this).find(".options-row");
            var notDownpaymentField = tabObj.find(".not-downpayment-field");
            var salesOrderCode = $(this).find("[name=\"salesOrderCode[]\"]");

            if (downpaymentType) {

                notDownpaymentField.hide();

                tabObj.find("[name=finalDiscount]").val(0);
                tabObj.find("[name=selFinalDiscountType]").val(1);

                if (detailInvoiceType.val() != 1) {
                    $(this).remove();
                } else {
                    detailInvoiceType.prop('disabled', true);
                    detailAmount.prop('readonly', false);
                    optionsRow.hide();
                }
            } else {
                notDownpaymentField.show();
                detailInvoiceType.prop('disabled', false);

                if (detailInvoiceType.val() == 1) {

                    //thisObj.updateSODetail($(this));

                    detailAmount.prop('readonly', true);

                    if ($(this).find(".options-row .transaction-detail-row").length > 0)
                        optionsRow.show();
                } else {
                    detailAmount.prop('readonly', false);
                }
            }

        });

    }

    this.updateInvoiceType = function updateInvoiceType(e, obj) {

        var cancelEvent = true;
        var prevValue = $(obj).val();

        // pada saat dialog kebuka, chkbox sudah keupdate karena sudah lost focus
        $("#dialog-message").html("Merubah jenis faktur akan mereset detail transaksi.");
        $("#dialog-message").dialog({
            width: 300,
            modal: true,
            title: "Konfirmasi Perubahan Jenis Faktur",
            open: function () {
                $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
            },
            close: function () {
                if (cancelEvent)
                    tabObj.find("[name=chkDownpayment]").val(prevValue).change(); // sementara pake nama langsung 
            },
            buttons: {
                OK: function () {
                    thisObj.updateFieldOnChangeInvoiveDownpayment();
                    thisObj.rebindEl();

                    cancelEvent = false;
                    $(this).dialog("close");
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            },
        });
    }

    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler() {
        // fungsi ini dipanggil asal add new row utk detail DP, payment method dsb
        thisObj.calculateTotal();
        thisObj.rebindDownpayment();
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
        thisObj.updateDefaultDownpayment();
    }

    this.showInvoiceRef = function showInvoiceRef(obj) {
        $obj = $(obj);
        if ($obj.val() == 1)
            tabObj.find(".invoice-ref").hide();
        else
            tabObj.find(".invoice-ref").show();
    }

    this.updateCurrency = function updateCurrency() {
        $("#dialog-message").html("Apakah Anda ingin mengganti mata uang untuk pelanggan ini ?<br>Semua detail transaksi akan dihapus jika Anda mengganti mata uang.");
        $("#dialog-message").dialog({
            width: 300,
            modal: true,
            title: "Konfirmasi Perubahan Data mata uang",
            close: function () {
                tabObj.find("[name=selCurrency]").val(tabObj.find("[name=hidCurrentCurrencyKey]").val());
            },
            open: function () {
                $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
            },
            buttons: {
                OK: function () {
                    tabObj.find("[name=hidCurrentCurrencyKey]").val(tabObj.find("[name=selCurrency]").val());
                    thisObj.resetDetails();
                    $(this).dialog("close");
                },
                Cancel: function () {
                    tabObj.find("[name=selCurrency]").val(tabObj.find("[name=hidCurrentCurrencyKey]").val());
                    $(this).dialog("close");
                }
            }

        });

    }

    this.checkVA = function checkVA(obj) {
        var isVA = $(obj).find('option:selected').attr('rel-va');

        if (isVA == 1)
            tabObj.find(".va-col").show();
        else
            tabObj.find(".va-col").hide();
    }

    this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler() {
        // thisObj.updateFieldOnChangeInvoiveDownpayment();
    }

    this.rebindEl = function rebindEl() {

        var handling = [];
        handling.onSelectFunction = 'getTabObj().updateDetail';
        handling.onChangeFunction = 'getTabObj().updateOnChange';

        var customerkey = tabObj.find("[name=hidCustomerKey]").val();

        var statustype = (thisObj.getDownpaymentType()) ? 'downpayment' : 'sales';

        bindAutoCompleteForTransactionDetail('salesOrderCode[]', objAndValueForDetailAutoComplete, 'ajax-medical-job-order.php?action=searchDataForInvoice&statustype=' + statustype + '&customerkey=' + customerkey, handling);
        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailItemAutoComplete, 'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');

        bindEl(tabObj.find("[name='selInvoiceType[]']"), 'change', function () {
            thisObj.changeInvoiceType(this);
        });
        bindEl(tabObj.find("[name='amount[]'], [name='chkService[]'],[name='taxDetail[]'], [name='chkIsTax23[]']"), 'change', function () {
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

        tabObj.find("[name=selCustomCode]").change(function () {
            thisObj.showInvoiceRef(this);

            tabObj.find("[name='salesOrderCode[]']").each(function () {
                detailRow = $(this).closest(".transaction-detail-row");
                thisObj.updatePartialInvoiceOutstanding(detailRow);
            })

            thisObj.calculateTotal();
        });

        tabObj.find("[name=selTermOfPayment]").change();
        tabObj.find("[name=btnImport]").on('click', function () {
            thisObj.importData();
        });
        tabObj.find("[name=chkDownpayment]").change(function () {
            thisObj.updateInvoiceType(this)
        })
        tabObj.find("[name=dummychkPick-master]").change(function () {
            updateChkPick(this, thisObj.onChangeChk)
        })
        tabObj.find("[name=chkTax23], [name=tax23Percentage]").change(function () {
            thisObj.calculateTax23()
        });
        tabObj.find("[name=beforeTaxTotal], [name=chkIncludeTax], [name=selFinalDiscountType], [name=taxPercentage], [name=finalDiscount]").change(function () {
            thisObj.calculateTotal(this)
        });
        tabObj.find("[name=selFinalDiscountType]").change(function () {
            updateFinalDiscountDecimal(this)
        });
        tabObj.find("[name=selCurrency]").change(function () {
            thisObj.updateCurrency();
        });

        // sudah ad di customCodeHandler
        //tabObj.find("[name=selCustomCode]").change();  

        tabObj.find("[name=selBank]").change(function () {
            thisObj.checkVA(this);
        });

        addNewTemplateRow("downpayment-row-template");

        tabObj.find("[name='chkPick-master']").val(1).change();

        customCodeHandler(thisObj);

        thisObj.rebindEl();

    }
}