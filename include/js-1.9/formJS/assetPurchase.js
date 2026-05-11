function AssetPurchase(tabID, cashTOP, tablekey, varConstant) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    var firstOpened = true;

    this.tabID = tabID;
    this.tablekey = tablekey;

    this.updateSupplier = function updateSupplier() {
        var purchasekey = tabObj.find("[name=hidPurchaseRequestKey]").val();

        $.ajax({
            type: "GET",
            url: 'ajax-purchase-request.php',
            data: "action=getDataRowById&pkey=" + purchasekey,
        }).done(function (data) {

            if (!data) return;

            data = JSON.parse(data);
            data = data[0];
            tabObj.find("[name=hidSupplierKey]").val(data.supplierkey);
            tabObj.find("[name=supplierName]").val(data.suppliername);

        });

    }

//
//    this.onChangePurchaseRequest = function onChangePurchaseRequest(event, ui) {
//        var obj = this;
//
//        if (tabObj.find("[name=hidCurrentPurchaseRequestKey]").val() != '') {
//            $("#dialog-message").html("Merubah pelanggan akan mereset detail transaksi.");
//            $("#dialog-message").dialog({
//                width: 300,
//                modal: true,
//                title: "Konfirmasi Perubahan Data Penawaran Pembelian",
//                open: function () {
//                    $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
//                },
//                close: function () {
//                    tabObj.find("[name=hidPurchaseRequestKey]").val(tabObj.find("[name=hidCurrentPurchaseRequestKey]").val());
//                    tabObj.find("[name=purchaseRequestCode]").val(tabObj.find("[name=hidCurrentPurchaseRequestCode]").val());
//                    $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
//                    thisObj.rebindEl();
//                },
//                buttons: {
//                    OK: function () {
//                        if (ui.item == null) {
//                            clearAutoCompleteInput(obj, 'hidPurchaseRequestKey');
//                            tabObj.find("[name=hidCurrentPurchaseRequestKey]").val('');
//                            tabObj.find("[name=hidCurrentPurchaseRequestCode]").val('');
//                        } else {
//                            tabObj.find("[name=hidCurrentPurchaseRequestKey]").val(ui.item.pkey);
//                            tabObj.find("[name=hidCurrentPurchaseRequestCode]").val(ui.item.value);
//                        }
//
//                        thisObj.importData();
//                        $(this).dialog("close");
//                    },
//                    Cancel: function () {
//                        $(this).dialog("close");
//                    }
//                },
//            });
//        } else {
//            if (ui.item == null) {
//                clearAutoCompleteInput(obj, 'hidPurchaseRequestKey');
//                tabObj.find("[name=hidCurrentPurchaseRequestKey]").val('');
//                tabObj.find("[name=hidCurrentPurchaseRequestCode]").val('');
//            } else {
//                tabObj.find("[name=hidCurrentPurchaseRequestKey]").val(ui.item.pkey);
//                tabObj.find("[name=hidCurrentPurchaseRequestCode]").val(ui.item.value);
//                thisObj.importData();
//
//            }
//
//            thisObj.rebindEl();
//        }
//    }

    this.calculateDetail = function calculateDetail(obj) {

        var row = $(obj).closest(".transaction-detail-row");
        var qty = 1; // unformatCurrency(row.find("[name='detailQty[]']").val());
        var priceInUnit = unformatCurrency(row.find("[name='priceInUnit[]']").val());
        var subtotal = qty * priceInUnit;
        row.find("[name='detailSubtotal[]']").val(subtotal).blur();

        thisObj.calculateTotal();
    }


    this.calculateTotal = function calculateTotal() {
        var subtotal = 0;
        tabObj.find("[name='detailSubtotal[]']").each(function () { subtotal += parseInt(unformatCurrency($(this).val())) || 0; })
        tabObj.find("[name='subtotal']").val(subtotal).blur();

        var finalDiscount = parseInt(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0;
        var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0;
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

        var total = subtotal + etcCost;
        tabObj.find("[name='total']").val(total).blur();

        var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

        var balance = totalPayment - total;
        tabObj.find("[name='balance']").val(balance).blur();
    }


    this.updateTOP = function updateTOP() {

        var selTermOfPaymentKey = tabObj.find("[name=selTermOfPaymentKey]").val();
        var supplierkey = tabObj.find("[name=hidSupplierKey]").val();

        $.ajax({
            type: "GET",
            url: 'ajax-supplier.php',
            data: "action=getDataRowById&pkey=" + supplierkey,
        }).done(function (data) {

            if (!data) return;

            data = JSON.parse(data);
            data = data[0];

            if (firstOpened == true) {
                firstOpened = !firstOpened;
                thisObj.updateSupplierInformation(data.termofpaymentkey);
            } else if (selTermOfPaymentKey != data.termofpaymentkey) {

                $("#dialog-message").html("Apakah Anda ingin mengganti data pembayaran dengan data default untuk pemasok ini ?");
                $("#dialog-message").dialog({
                    width: 300,
                    modal: true,
                    title: "Konfirmasi Perubahan Data Pembayaran",
                    open: function () {
                        $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                    },
                    buttons: {
                        OK: function () {
                            thisObj.updateSupplierInformation(data.termofpaymentkey);
                            $(this).dialog("close");
                        },
                        Cancel: function () {
                            $(this).dialog("close");
                        }
                    }

                });
            }

        });

    }

    this.updateSupplierInformation = function updateSupplierInformation(topkey) {
        if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + topkey + "']").length > 0)
            tabObj.find("[name=selTermOfPaymentKey]").val(topkey).change();
    }


    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler() {
        thisObj.calculateTotal();
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    }

    this.rebindEl = function rebindEl() { 
        bindEl(tabObj.find("  [name='selCategoryKey[]'], [name='detailQty[]'], [name='priceInUnit[]']"), 'change', function () { thisObj.calculateDetail(this) });
    }

    this.loadOnReady = function loadOnReady() {

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

        tabObj.find("[name=selTermOfPaymentKey]").change();
        tabObj.find(" [name=chkIsFullReceive]").change();
        tabObj.find(".form-detail-field").toggle();
        tabObj.find(".form-detail-button").click(function () {
            tabObj.find(".form-detail-field").toggle("highlight");
            var temp = tabObj.find(".form-detail-button").attr("relalt");
            $("#" + tabID + " .form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
            tabObj.find(".form-detail-button").text(temp);
        });
        tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost], [name=taxPercentage]").change(function () { thisObj.calculateTotal(this) })
        tabObj.find("[name=selFinalDiscountType]").change(function () { updateFinalDiscountDecimal(this) })

        thisObj.rebindEl();

    }
}
