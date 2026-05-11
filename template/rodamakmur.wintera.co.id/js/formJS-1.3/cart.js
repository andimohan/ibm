function Cart(opt){   
    var thisObj = this;   
    var errMsg = (opt.errMsg != undefined ) ? opt.errMsg : new Array;
    var lang = (opt.lang != undefined ) ? opt.lang : new Array;
    var shipmentService = (opt.shipmentService != undefined ) ? opt.shipmentService : new Array;
     
    DECIMAL = opt.decimal;
     

    this.removeNotEligibleVoucher = function removeNotEligibleVoucher(){ 

        var totalValue = parseFloat(unformatCurrency($(".mnv-cart-table-subtotal").text())) || 0;
        var shipping = parseFloat(unformatCurrency($("[name=shippingCost]").val())) || 0;
        var discount = parseFloat(unformatCurrency($(".mnv-cart-discount").text())) || 0;

		var voucherkey = $('[name=\'hidVoucherKey[]\']').val() || 0;
		var vouchertype = $('[name=\'hidVoucherType[]\']').val() || 0; 
        
        // cek ulang, voucher masih valid gk karena perubahan harga / ongkir 
              
        if(voucherkey != 0 ){
            $.ajax({
                type: "GET",
                url: "/ajax-voucher-transaction.php?action=calculateVoucherValue&voucherkey="+voucherkey+"&vouchertype="+vouchertype+"&totalsales="+totalValue+'&totalshipment='+shipping,  
                async: false, 
                beforeSend : function(){ 
                    $('.voucher-row').removeClass("show");
                } ,
                success: function(data){       
                    if(!data) return;   
                    data = JSON.parse(data);
 
                   var salesVoucherValue = (parseFloat(data['amount']) || 0); 
                   if (salesVoucherValue == 0) {
                       $('[name=\'hidVoucherKey[]\']').val(0); 
                       $('[name=\'hidVoucherType[]\']').val(0);
                       $('.voucher-row').removeClass("show");
                   } 
                } 
            }); 
        } 
    }
    
    this.recalculateCartTotal = function recalculateCartTotal(obj){  
    
        if (!obj) obj = $(".mnv-cart-table");

        var subtotalObj = obj.find(".mnv-cart-table-subtotal");
        var totalQtyObj = obj.find(".mnv-total-qty");
        var subtotal = 0;
        var totalQty = 0;
        
        obj.find(".transaction-row").each(function() {  
            
            var qtyObj = $(this).find("[name=\'qty[]\']");
            $itemPrice =  parseFloat($(this).find("[name=\'hidItemPrice[]\']").val()) || 0; 
            $itemQty =  parseFloat(qtyObj.val()) || 0; 
            
            $rowSubtotal = $itemQty * $itemPrice; 
            totalQty += $itemQty;
            
            $(this).find(".mnv-detail-subtotal").text($rowSubtotal).blur().formatCurrency({roundToDecimalPlace: DECIMAL});
            subtotal += $rowSubtotal;
             
            // update ke session
            thisObj.updateOrderQty(qtyObj);
            
        }); 

        var $priceRateElement = $('.price-rate');
        var isAddressChanged = $priceRateElement.attr('data-address-changed') === 'true';

        var shippingRate = isAddressChanged ? 0 : parseFloat($priceRateElement.attr('data-price')) || 0;

        if (isAddressChanged) {
            $priceRateElement.removeAttr('data-address-changed');
        }

        var grandTotal = subtotal + shippingRate;

        //subtotalObj.text(subtotal).formatCurrency({roundToDecimalPlace: DECIMAL });

        subtotalObj.text(grandTotal).formatCurrency({roundToDecimalPlace: DECIMAL });
        totalQtyObj.text(totalQty).formatCurrency({roundToDecimalPlace: DECIMAL });
        $(".mnv-cart-total-sales").text(subtotal).formatCurrency({roundToDecimalPlace: DECIMAL });

        //updateCartStatus();
        
        // recount shippingCost and weight 
        //if (typeof recalculateShippingCost === "function") 
        //thisObj.recalculateShippingCost(); 
		//thisObj.updateCartTotalSummary();
        //thisObj.removeNotEligibleVoucher();
        
		
		//thisObj.calculatePointNeeded();
		 
        // update available voucher
        //updateAvailableVoucher();

    }
    
    this.updateOrderQty = function updateOrderQty(obj){
        var transactionRow =  obj.closest(".transaction-row");
        var itemkey = transactionRow.find("[name=\'hidItemKey[]\']").val();
        var qty = parseInt(unformatCurrency(obj.val())) || 0;
        //var sellingPrice =  parseFloat(unformatCurrency(transactionRow.find("[name=\'hidItemPrice[]\']").val())) || 0;
  
        $.ajax({
            type: "POST",
            url: "/ajax-cart.php", 
            data : {action:'updateQty', itemkey: itemkey, qty: qty},
            success: function(data){    
                // sudah diupdate duluan
                //transactionRow.find(".mnv-detail-subtotal").text(qty * sellingPrice).formatCurrency({roundToDecimalPlace: DECIMAL });
                //thisObj.recalculateCartTotal();
            } 
        });
    };
    
    this.updateShipmentServices = function updateShipmentServices(){
       var arrServices = shipmentService[$("[name=selCourier]").val()] || [];  
       reInsertSelectBox($("[name=selShipmentService]"),arrServices, {"key" : "servicekey", "label" : "servicename"});  
         
       if(arrServices[0]['needlocation'] ==1)
           $(".dropoff-loc").show();
       else
           $(".dropoff-loc").hide();
    }
 
 
    this.loadOnReady = function loadOnReady(){   
         
        
        $('#mnv-form-cart').bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        recipientName: {
        validators: {
        notEmpty: {
        message:  errMsg.name[1]
        },
        }
        },
        recipientPhone: {
        validators: {
        notEmpty: {
        message: errMsg.phone[1]  
        },
        }
        },
        recipientEmail: {
        validators: {
        notEmpty: {
        message: errMsg.email[1]  
        },
        emailAddress: {
        message:errMsg.email[3]  
        }
        }
        },
        recipientAddress: {
        validators: {
        notEmpty: {
        message:errMsg.address[1] 
        },
        }
        },
        recipientZipcode: {
        validators: {
        notEmpty: {
        message:errMsg.zipcode[1] 
        },
        numeric: {
        message: 'Kode pos harus berupa angka'
        },
        stringLength: {
        max: 5,
        min: 5,
        message: 'Kode pos harus 5 angka'
        }
        }
        },
        }
        })
        .on('success.form.bv', function(e) {
        $("[name=btnSave]").prop('disabled', true).addClass("btn-primary-loading");
        // Prevent form submission
        e.preventDefault(); 
        var $form = $(e.target); 
        var bv = $form.data('bootstrapValidator'); 
        disableButton($form.find("[name=btnSave]"));
            
        // Use Ajax to submit form data
        $.post($form.attr('action'), $form.serialize(), function(result) {
        disableButton($form.find("[name=btnSave]"),false);
            
        $("[name=btnSave]").removeClass("btn-primary-loading");
        var error = "";
        for (i=0;i<result.length;i++) error=error + "<li>" + result[i].message + "</li>";
        if (error != "")
        error = "<ul class=\"message-dialog-ul\">" + error + "</ul>"; 
            
        var notifObj = $form.find(".notification-msg");
        notifObj.html(error).hide().fadeToggle("fast");
            
        if (!result[0].valid){ 
            setStatusColorNotification(notifObj,1); 
            $form.data('bootstrapValidator').resetForm();
            scrollToTopForm($form);
            //grecaptcha.reset();
        }else{ 
            setStatusColorNotification(notifObj,2); 
            location.href="/payment/sales-order/"+result[0]['data']['code']+"/"+result[0]['data']['checksum'];  
        }
        }, 'json');
        });
        
        $("#mnv-form-cart").bind('keyup keypress', function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
        var tagName = $(e.target);
        if (!tagName.is("textarea")) {
        e.preventDefault();
        return false;
        }
        }
        });
        
        $('.btn-ctr').on('click', function () {  
            var ctr = parseInt($(this).attr('attr-ctr')) || 0;
            var qtyObj = $(this).closest(".quantity").find("[name=\'qty[]\']"); 
            var qtyValue =  parseInt(unformatCurrency(qtyObj.val())) || 0;  
            qtyValue += ctr; 
            if(qtyValue<1) qtyValue = 1; 
            qtyObj.val(qtyValue).blur();
            thisObj.recalculateCartTotal();
             
        });
 
 
        $(".mnv-delete-cart-row, .mobile-delete-col").click(function() {
            
            $(this).closest(".transaction-row").remove();
            var itemkey = $(this).closest(".transaction-row").find("[name='hidItemKey[]']").val(); 
            
            $.ajax({
                type: "POST",
                url: "/ajax-cart.php", 
                data : {action:'delete', itemkey: itemkey},
                success: function(data){ 
                    thisObj.recalculateCartTotal();
                } 
            });

            renumbering($('.row-number'));
            
        });
  
        $("[name=\'qty[]\']").on('change', function () { 
            var qty = parseInt($(this).val()) || 0;
            if (qty < 1)  $(this).val(1).blur();
            
            thisObj.recalculateCartTotal();
        });
        
        $("[name=\'selInputAddress\']").on('change', function () { 
            var selectedAddessPkey = $(this).val();
            var addressPanel = $('.recipient-addresss');
            
            $.ajax({
                type: "POST",
                url: "/ajax-member.php", 
                async : false,
                data : {action:'get-shipment-address', pkey: selectedAddessPkey },
                success: function(data){ 
                    if(!data) return;   
                    data = JSON.parse(data);
                    
                    $('[name=recipientName]').val(data[0].pic);
                    $('[name=recipientEmail]').val(data[0].email);
                    
                    addressPanel.find('.pic').html(data[0].pic);
                    
                    addressPanel.find('.address').html(data[0].address.replace(/\n/g, '<br>'));
                    $('[name=recipientAddress]').val(data[0].address);
                    
                    addressPanel.find('.phone').html(data[0].phone);
                    $('[name=recipientPhone]').val(data[0].phone);
                    
                    addressPanel.find('.zipcode').html(data[0].zipcode);
                    $('[name=recipientZipcode]').val(data[0].zipcode); 
                    
                    addressPanel.find('.trdesc').html(data[0].trdesc.replace(/\n/g, '<br>'));
                    $('[name=recipientTrDesc]').val(data[0].trdesc);
                    
                    addressPanel.find('.hidLatLng').html(data[0].latlng);
                    $('[name=hidLatLng]').val(data[0].latlng);

                    $('[name="selShipmentService"]').prop('selectedIndex', 0);
                    $('.price-rate').text('0');
                    $('.price-rate').attr('data-price', 0).attr('data-address-changed', 'true');

                    // Recalculate dengan subtotal saja (tanpa shipping)
                    thisObj.recalculateCartTotal();
                    handleZipcodeChange();

                } 
            });
        });
        
        $("[name=\'selInputAddress\']").change();

            const $shipmentSelect = $('[name=selShipmentService]');
            const $btnCo = $('[name=btnSave]');

            function validateInput() {
                const $name = $('[name=recipientName]');
                const $phone = $('[name=recipientPhone]');
                const $address = $('[name=recipientAddress]');
                const $email = $('[name=recipientEmail]');

                if (!$name.val().trim() || !$phone.val().trim() || !$address.val().trim() || !$email.val().trim()) {
                    $btnCo.prop('disabled', true);
                    return false;
                }
                
                $btnCo.prop('disabled', false);
                return true;
            }

            $('[name=recipientName], [name=recipientPhone], [name=recipientAddress], [name=recipientEmail]').on('input change', validateInput);

            validateInput();
            $btnCo.prop('disabled', true);

            
            function updateShipmentOptions(selectedShippment) {
                if (selectedShippment) {
                    checkRate(selectedShippment);
                    $btnCo.prop('disabled', true);
                }   
            }


        function handleZipcodeChange() {            
            const recipientZipcode = $('[name="recipientZipcode"]').val();

            if (recipientZipcode && recipientZipcode.length === 5 && !isNaN(recipientZipcode)) {
                $shipmentSelect.prop('disabled', false);
                if ($shipmentSelect.val()) {
                    updateShipmentOptions($shipmentSelect.val());
                }
            } else {
                $shipmentSelect.prop('disabled', true);
                $btnCo.prop('disabled', true);
            }
        }

        handleZipcodeChange();
        $('[name="recipientZipcode"]').on('change input', handleZipcodeChange);

        function checkRate(serviceKey) {
                showLoading();

                const hidItemKey = [];
                const hidLatLng = $('[name="hidLatLng"]').val();
                const recipientZipcode = $('[name="recipientZipcode"]').val();
                // console.log('recipientZipcode:', recipientZipcode);
                
                $('[name="hidItemKey[]"]').each(function() {
                    hidItemKey.push($(this).val());
                });
                
                if (hidItemKey.length === 0 && typeof cartItems !== 'undefined') {
                    hidItemKey = cartItems;
                }
                
                $.ajax({
                    url: 'ajax-cart.php',
                    type: 'POST',
                    data: {
                        action: 'checkRate',
                        selShipmentService: serviceKey,
                        hidItemKey: hidItemKey,
                        hidLatLng: hidLatLng,
                        recipientZipcode: recipientZipcode
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log('CheckRate Response:', response);
                        
                    if (response.price !== undefined) {
                            updateRateDisplay(response);
                            $btnCo.prop('disabled', false);

                        } else {
                            // console.error('CheckRate Error:', response.message);
                            showError('Gagal mengecek tarif/pilih layanan pengiriman lain');
                            $btnCo.prop('disabled', true);
                            $('[name="selShipmentService"]').prop('selectedIndex', 0);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.error('AJAX Error:', error);
                        showError('Gagal menghubungi server');
                        $btnCo.prop('disabled', true);

                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }
            
        function updateRateDisplay(rateData) {

            // console.log('Rate Data:', rateData);
            // console.log('Rate Data:', rateData.price);
            
            if (rateData.price) {
                $('.price-rate').text(rateData.price).blur();
                $('.price-rate').attr('data-price', rateData.price).blur();
                $('.price-rate').formatCurrency({roundToDecimalPlace: DECIMAL});

                // console.log('Shipping rate updated, recalculating cart total...');
                thisObj.recalculateCartTotal();
            }
        }

        function showLoading() {
            $shipmentSelect.prop('disabled', true);
            $btnCo.prop('disabled', true);
            $('.price-rate').text('Menghitung...');
        }

        function hideLoading() {
            $shipmentSelect.prop('disabled', false);
        }
        
        function showError(message) {
            $('.price-rate').text(message);
        }

                
            $shipmentSelect.on('change', function() {
                const selectedShippment = $(this).val();
                if (selectedShippment == '') {
                    $('.price-rate').attr('data-price', 0);
                    showError('Pilih layanan pengiriman anda');
                    $btnCo.prop('disabled', true);
                }
                updateShipmentOptions(selectedShippment);
            });
            
            if ($shipmentSelect.val()) {
                updateShipmentOptions($shipmentSelect.val());
            }        
    };
}