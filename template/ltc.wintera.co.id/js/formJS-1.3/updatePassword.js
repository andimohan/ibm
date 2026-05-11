function UpdatePassword(opt){   
    var thisObj = this;   
    var errMsg = opt.errMsg;
    var lang = opt.lang;
 
    this.rebindEl = function rebindEl(){ 

    }
 
	
    this.loadOnReady = function loadOnReady(){   

        var rebindHandler = null;

        $(".mnv-show-password").click(function() {  showHidePassword($(this));  });
        
        $('#form-update-password')
        .bootstrapValidator({
        feedbackIcons: {
        valid: 'glyphicon glyphicon-ok',
        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
        currentPassword: {
        validators: {
        notEmpty: {
        message: errMsg.password[1]
        },
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        remote: {
        message:  errMsg.username[5],
        url: '/ajax-member.php',
        data: {
        type: 'check',
        fieldtype: 'checkPassword'
        },
        type: 'POST'
        }
        }
        },
        password: {
        validators: {
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        identical: {
        field: 'passwordConfirmation',
        message: errMsg.password[3]
        }
        }
        },
        passwordConfirmation: {
        validators: {
        stringLength: {
        min: 5,
        max: 30,
        message: errMsg.password[2]
        },
        identical: {
        field: 'password',
        message: errMsg.password[3]
        }
        }
        }, 
        }
        })
        .on('success.form.bv', function(e) {
            
            
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
            
        disableButton($form.find("[name=btnSave]"));
            
        // Use Ajax to submit form data
        $.post($form.attr('action'), $form.serialize(), function(result) {
            console.log(result);
        disableButton($form.find("[name=btnSave]"),false);
        var error = "";
        for (i=0;i<result.length;i++) error=error + "<li>" + result[i].message + "</li>";
        if (error != "")
        error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";
            
		var notifObj = $form.find(".notification-msg");
		notifObj.hide(); 

        if (!result[0].valid){
			notifObj.html(error).fadeToggle("fast");
			setStatusColorNotification(notifObj,1);
			$form.data('bootstrapValidator').resetForm();
			scrollToTopForm($form); 
        }else{
			//setStatusColorNotification(notifObj,2);
			alert(result[0].message);
			location.href="/profile";
        }
        }, 'json');
        }); 
	
    };

}
