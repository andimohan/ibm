function Newsletter(opt){   
    var thisObj = this; 
    var errMsg = opt.errMsg;
    var lang = opt.lang;

	function hideOverlayScreen(){ 
		$("html, body").css("overflow","inherit"); 
		$("#popup-panel").fadeOut("fast");   
		//$(".hide-on-dismiss").hide();
	}
	
    this.loadOnReady = function loadOnReady(){  
        
        $('#form-subscription')
				.bootstrapValidator({ 
					feedbackIcons: {
						valid: 'glyphicon glyphicon-ok',
						invalid: 'glyphicon glyphicon-remove',
						validating: 'glyphicon glyphicon-refresh'
				},
				fields: {
				 	email: { 
						 validators: {
							notEmpty: {
								message: errMsg.email[1]
							},  
							emailAddress: {
								message:  errMsg.email[3]
							}
						}
					}, 
				}
			})
			.on('success.form.bv', function(e) {
				
				$("[name=btnSave]").prop('disabled', true).addClass("btn-primary-loading");
				
				// Prevent form submission
				e.preventDefault(); 
				// Get the form instance
				var $form = $(e.target);
	
				// Get the BootstrapValidator instance
				var bv = $form.data('bootstrapValidator');
	
				// Use Ajax to submit form data
				$.post($form.attr('action'), $form.serialize(), function(result) {
		 
					// selalu sukses kalo utk newsletter
//					alert(lang.registrationSuccessful);
					
					$("[name=btnSave]").removeClass("btn-primary-loading");
					
//					var error = "";
//					for (i=0;i<result.length;i++)
//						error = error + "<li>" + result[i].message + "</li>";
//						
//					if (error != "")
//						error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";
//					
					// selalu return sukses
					alert(lang.registrationSuccessful);
					hideOverlayScreen();
//					location.href="/";
					
//					if (!result[0].valid){
//						$(".notification-msg").html(error).hide().fadeToggle("fast");
//						$(".notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal").addClass("show");
//						$form.data('bootstrapValidator').resetForm();
//                        scrollToTopForm($form);s
//						grecaptcha.reset(); 
//					}else{
//						alert(lang.subscriptionSuccessful);
//						location.href="/";
//					}
					
				}, 'json');
			}); 
         
    };

}