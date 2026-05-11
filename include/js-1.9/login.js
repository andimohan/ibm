var browserTests = [
        //"audio",
        "availableScreenResolution",
        "canvas",
        "colorDepth",
        "cookies",
        "cpuClass",
        "deviceDpi",
        "doNotTrack",
        "indexedDb", 
        "language",
        //"localIp",
        "localStorage",
        "pixelRatio",
        "platform",
        "plugins",
        "processorCores",
        "screenResolution",
        "sessionStorage",
        "timezoneOffset",
        "touchSupport",
        "userAgent",
        "webGl"
      ];
 

function updateChkBoxOnClick(obj){   
    var chkValue = $(obj).prop("checked") ? 1 : 0;
    $(obj).val(chkValue); 
    $(obj).next().val(chkValue); 
} 

function updateChkBoxOnChange(obj){   

    var checked = "",chkValue = 0;

    if($(obj).val() == 1){
        checked = "checked";
        chkValue = 1;
    } 

    $(obj).prev().prop("checked",checked).change(); // dont use click !
    $(obj).prev().val(chkValue);
}

jQuery(document).ready(function(){ 

     $('#defaultForm')
        .bootstrapValidator({

            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            loginID: {
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.username['1'] 
                    },
                    stringLength: {
                        min: 5,
                        max: 30,
                        message: phpErrorMsg.username['3'] 
                    }, 
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message:  phpErrorMsg.username['4'] 
                    }
                }
            }, 
            loginPassword: {
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.password['1'] 
                    }
                }
            }
        }
    })
    .on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault(); 
        var $form = $(e.target); 
        var bv = $form.data('bootstrapValidator');

         var btnLogin = $form.find("[name=btnLogin]");  
         btnLogin.prop('disabled', true);
         btnLogin.find(".loading-icon").show();


        // Use Ajax to submit form data
        $.post($form.attr('action'), $form.serialize(), function(result) {

            $(".notification-msg").hide().fadeToggle("fast"); 

            if (!result.valid){
                $(".notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal"); 
                $(".notification-msg").html(result.message);

                if (result.useOTP)  
                    $(".login-slide-panel").animate({left: $(".login-slide-panel").width() / -2},500, function(){$("[name=authcode]").focus();});

            }else{

                $(".notification-msg").hide();
                $(".notification-msg").removeClass("bg-red-cardinal");
                $(".notification-msg").html("");
                if (result.message){ 
                    $(".notification-msg").addClass("bg-green-avocado"); 
                    $(".notification-msg").html(result.message);
                    $(".notification-msg").show();
                }

                // kalo login gk pake OTP
                if (result.useOTP) {
                    $(".login-slide-panel").animate({left: $(".login-slide-panel").width() / -2},500, function(){$("[name=authcode]").focus();});
                }else{ 
                    $form[0].action = "list";
                    $form[0].submit();  
                }
            }

             btnLogin.prop('disabled', false);
             btnLogin.find(".loading-icon").hide();

        }, 'json');
    });


    $( ".icon-back" ).on('click', function() { 
        $(".login-slide-panel").animate({left: '0'});
    }); 
 
    imprint.test(browserTests).then(function(result){ 
        $('[name=df]').val(result); 
    });
	

	
});