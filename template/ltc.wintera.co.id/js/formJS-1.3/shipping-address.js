function ShippingAddress(opt) {
    var thisObj = this;
    var errMsg = opt.errMsg || {};
    var lang = opt.lang || {};

    this.hideInput = function hideInput(row){ 
      row.find(".label-show").show();
      row.find(".input-show").hide();
      //row.find(".label-show-add").show();
      //row.find(".input-show-add").hide();
    };
    
    this.loadOnReady = function loadOnReady() {
          $('.form-multiaddress').each(function () {
                const $formAddress = $(this);
              
                $formAddress.bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        'maName[]': {
                            validators: {
                                notEmpty: {
                                    message:  errMsg.name[1]
                                },
                            }
                        },
                        'maAddress[]': {
                            validators: {
                                notEmpty: {
                                    message:  errMsg.address[1] 
                                },
                            }
                        },
                        'maZipCode[]': {
                      validators: {
                        notEmpty: {
                          message: errMsg.zipcode[1] 
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
                .on('success.form.bv', function (e) {
                  // Prevent form submission
                    e.preventDefault();
                    // Get the form instance
                    var $form = $(e.target);
                    // Get the BootstrapValidator instance
                    var bv = $form.data('bootstrapValidator');
                    // Use Ajax to submit form data

                    disableButton($form.find("[name=btnEdit]"));

                    $.post($form.attr('action'), $form.serialize(), function(result) {

                        disableButton($form.find("[name=btnEdit]"),false);
                        var error = "";
                        for (i=0;i<result.length;i++) error=error + "<li>" + result[i].message + "</li>";
                            if (error != "")
                                error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";

                        //var notifObj = $form.find(".notification-msg");
                        //notifObj.html(error).hide().fadeToggle("fast");

                    if (!result[0].valid){
                        //setStatusColorNotification(notifObj,1);
                        $form.data('bootstrapValidator').resetForm();
                        //scrollToTopForm($form);
                        //grecaptcha.reset();
                    }else{
                        //setStatusColorNotification(notifObj,2);
                        
                        if($formAddress.find("[name=action]").val() == 'add-multi-address'){
                          location.href="/shipping-address";
                        }else{ 
                            thisObj.hideInput($formAddress.find(".address-item"));

                            $formAddress.find(".shipping-name").html( $formAddress.find("[name='maName[]']").val() );
                            $formAddress.find(".shipping-address").html( $formAddress.find("[name='maAddress[]']").val().replace(/\n/g, '<br>') );
                            $formAddress.find(".shipping-zipcode").html( $formAddress.find("[name='maZipCode[]']").val() );
                            $formAddress.find(".shipping-pic").html( $formAddress.find("[name='maPIC[]']").val() );
                            $formAddress.find(".shipping-phone").html( $formAddress.find("[name='maPhone[]']").val() );
                            $formAddress.find(".shipping-latlng").html( $formAddress.find("[name='maLatLng[]']").val() );

                        }
                        //alert(lang.dataHasBeenSuccessfullyUpdated);
                        //location.href="/";
                    }
                }, 'json');
            });
          });
        
    //$(".input-show-add").hide();


    $('[name=btnAddNewAddress]').on('click', function (e) {
      e.preventDefault();
      var row = $(this).closest('.new-address-panel');
              
      row.find(".label-show").hide();
      row.find(".input-show").show();

      //row.find(".label-show-add").hide();
      //row.find(".input-show-add").show();
    });
        
        
    $('[name=btnCancel]').on('click', function (e) {
      e.preventDefault();
      var row = $(this).closest('.new-address-panel');
      thisObj.hideInput(row);
    });
    
    $('.btn-edit').on('click', function (e) {
      e.preventDefault();
      var row = $(this).closest('.address-item');
      row.find(".label-show").hide();
      row.find(".input-show").show();
    });
    
    
    $('.btn-cancel').on('click', function (e) {
      e.preventDefault();
      var row = $(this).closest('.address-item');
      thisObj.hideInput(row);
    });
    $('.btn-delete').click(function (e) {
      e.preventDefault();
      var $button = $(this);
      var $addressItem = $button.closest('.address-item');
      var pkey = $addressItem.find('[name=\'hidDetailKey[]\']').val();
        
      if (confirm(lang.confirmDeleteAddress)) {
        $.ajax({
          type: 'POST',
          url: '/ajax-member.php',
          data: 'action=delete-multi-address&pkey=' + pkey,
          success: function (response) {
            location.reload();
          },
          error: function () {
            alert('Gagal menghapus alamat.');
          }
        });
      }
    });
};   
}