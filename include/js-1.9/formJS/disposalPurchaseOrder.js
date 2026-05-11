function DisposalPurchaseOrder(tabID, opt, cashTOP, data) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    this.tabID = tabID;
    var id = tabObj.find("[name=hidId]").val();

    var objAndValue = new Array;
    objAndValue.push({ object: 'hidWasteKey[]', value: 'pkey' });
    objAndValue.push({ object: 'waste[]', value: 'value' });
    var objAndValueForDisposalDetailAutoComplete = objAndValue;

    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = (opt.arrFile) ? opt.arrFile : Array();


    this.calculateDetailRowSubtotal = function calculateDetailRowSubtotal(rowObj) {
        var rowObj = $(rowObj).closest(".transaction-detail-row");
        var weight = parseFloat(unformatCurrency(rowObj.find("[name='weightDetail[]']").val())) || 0;
        var priceInUnit = parseFloat(unformatCurrency(rowObj.find("[name='priceInUnit[]']").val())) || 0;
        var subtotal = weight * priceInUnit;

        var taxKey = rowObj.find("[name='taxDetailKey[]']").val();
        if (taxKey) {
            var taxPercentage = parseFloat(unformatCurrency(data['tax'][taxKey]['tax']));
        }
        var isInc = rowObj.find("[name='chkIncludeTaxDetail[]']").val() || 0;
        var taxValue = 0;
        var beforeTaxDetail = subtotal;
        if (taxPercentage > 0) {
            if (isInc == 0) {
                taxValue = subtotal * taxPercentage / 100;
            } else {
                taxValue = ((taxPercentage / (100 + taxPercentage)) * subtotal);
                beforeTaxDetail -= taxValue;
            }
        }
        rowObj.find("[name='subTotalDetailBeforeTax[]']").val(subtotal).blur();
        rowObj.find("[name='taxValueDetail[]']").val(taxValue);
        rowObj.find("[name='beforeTaxDetail[]']").val(beforeTaxDetail)
        rowObj.find("[name='subTotalDetail[]']").val(beforeTaxDetail + taxValue).change().blur();
        thisObj.calculateTotal();
    }

    this.calculateTotal = function calculateTotal() {

        var total = 0;
        var subTotalbeforeTax = 0;
        var taxValue = 0;
        var totalWeight = 0;

        tabObj.find("[name='subTotalDetail[]']").each(function () {
            if (!$(this).val()) return;
            row = $(this).closest(".transaction-detail-row");
            subTotalbeforeTax += parseFloat(unformatCurrency(row.find("[name='beforeTaxDetail[]']").val())) || 0;
            total += parseFloat(unformatCurrency(row.find("[name='subTotalDetail[]']").val())) || 0;
            taxValue += parseFloat(unformatCurrency(row.find("[name='taxValueDetail[]']").val())) || 0;
            totalWeight += parseFloat(unformatCurrency(row.find("[name='weightDetail[]']").val())) || 0;
        })

        tabObj.find("[name='subtotal']").val(subTotalbeforeTax).blur();
        tabObj.find("[name='beforeTaxTotal']").val(subTotalbeforeTax).blur();
        tabObj.find("[name='taxValue']").val(taxValue).blur();
        tabObj.find("[name='total']").val(total).blur();
        tabObj.find("[name='totalWeight']").val(totalWeight).blur();

        var totalPayment = 0;
        tabObj.find("[name='paymentMethodValue[]']").each(function () {
            totalPayment += parseFloat(unformatCurrency($(this).val())) || 0;
        })
        tabObj.find("[name='totalPayment']").val(totalPayment).blur();


        var balance = totalPayment - total;
        tabObj.find("[name='balance']").val(balance).blur();
    }

    this.updateWorkOrderDispatchInformation = function updateWorkOrderDispatchInformation(event, ui) {
        var obj = this;
        if (tabObj.find("[name=hidCurrentDispatchKey]").val() != '') {
            $("#dialog-message").html("Merubah Kode Dispatch akan mereset detail transaksi.");
            $("#dialog-message").dialog({
                width: 300,
                modal: true,
                title: "Konfirmasi Perubahan Data Dispatch",
                open: function () {
                    $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                },
                close: function () {
                    tabObj.find("[name=hidDispatchKey]").val(tabObj.find("[name=hidCurrentDispatchKey]").val());
                    tabObj.find("[name=dispatchCode]").val(tabObj.find("[name=hidCurrentDispatchCode]").val());
                    $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                    thisObj.rebindEl();

                },
                buttons: {
                    OK: function () {
                        if (ui.item == null) {
                            clearAutoCompleteInput(obj, 'hidDispatchKey');
                            tabObj.find("[name=hidCurrentDispatchKey]").val('');
                            tabObj.find("[name=hidCurrentDispatchCode]").val('');
                        } else {
                            tabObj.find("[name=hidCurrentDispatchKey]").val(ui.item.pkey);
                            tabObj.find("[name=hidCurrentDispatchCode]").val(ui.item.value);
                        }
                        // thisObj.resetDetails();
                        thisObj.updateDetailDispatchInformation();

                        $(this).dialog("close");
                    },
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                },
            });
        } else {
            if (ui.item == null) {
                clearAutoCompleteInput(obj, 'hidDispatchKey');
                tabObj.find("[name=hidCurrentDispatchKey]").val('');
                tabObj.find("[name=hidCurrentDispatchCode]").val('');
            } else {
                tabObj.find("[name=hidCurrentDispatchKey]").val(ui.item.pkey);
                tabObj.find("[name=hidCurrentDispatchCode]").val(ui.item.value);

            }

            thisObj.updateDetailDispatchInformation();
            thisObj.rebindEl();

        }

    }

    this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".work-order-row"));
        thisObj.calculateTotal();  
    }

    this.updateDetailDispatchInformation = function updateDetailDispatchInformation() {
        var dispatchKey = tabObj.find("[name='hidDispatchKey']").val();
        if (!dispatchKey) return;
        $.ajax({
            type: "GET",
            url: 'ajax-disposal-work-order.php',
            data: "action=getInformationForPurchase&dispatchkey=" + dispatchKey,
            beforeSend: function (xhr) {

                // bersihin baris dulu
                tabObj.find(".work-order-row").remove();
            },
            success: function (data) {
                if (data) {
                    if (!data) return;

                    var data = JSON.parse(data);

                    if (data.length <= 0) return;
                    // clone row
                    var totalDispatchWeight = 0;
                    $template = tabObj.find('.work-order-template');
                    for (i = 0; i < data.length; i++) {

                        $newRow = $template.clone().addClass('work-order-row').show().insertBefore($template.first());
                        // isi row hasil clone

                        $newRow.find(".wo-code").text(data[i].workordercode);
                        $newRow.find(".waste").text(data[i].waste);
                        $newRow.find(".customer-name").text(data[i].customername);
                        $newRow.find(".customer-weight").text(parseFloat(unformatCurrency(data[i].customerweight))).formatCurrency({ roundToDecimalPlace: 2 }); // anti buat otomatis
                        totalDispatchWeight += parseFloat(data[i].customerweight) || 0;
                    }

                    tabObj.find(".total-dispatch-weight").html(totalDispatchWeight).formatCurrency({roundToDecimalPlace: 2 });
                }
            }
        });
    }


    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler() {
        // fungsi ini dipanggil asal add new row utk detail DP, payment method dsb
        thisObj.calculateTotal();
    }

    this.afterRemoveRowHandler = function afterRemoveRowHandler() {
        thisObj.calculateTotal();
    }


    this.rebindEl = function rebindEl() {
        bindAutoCompleteForTransactionDetail('waste[]', objAndValueForDisposalDetailAutoComplete, 'ajax-waste.php?action=searchData');

        bindEl(tabObj.find("[name='weightDetail[]'], [name='priceInUnit[]'],[name='taxDetailKey[]'],[name='chkIncludeTaxDetail[]']"), 'change', function () { thisObj.calculateDetailRowSubtotal(this) });
        // bindEl(tabObj.find("[name='detailRowSubtotal[]']"),'change',function(){ thisObj.calculateTotal() });
    }

    this.loadOnReady = function loadOnReady() {

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

        tabObj.find("[name=selTermOfPayment]").change(function () {

            for (i = 0; i < cashTOP.length; i++) {
                if ($(this).val() == cashTOP[i]) {
                    tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function () { $(this).click() });
                    tabObj.find(".cashTOP").hide();
                    return;
                }
            }

            tabObj.find(".cashTOP").show();
        });

        tabObj.find("[name=selTermOfPayment]").change();

        thisObj.rebindEl();
    }
}
