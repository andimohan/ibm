function Index(opt){   
    var thisObj = this; 
    var errMsg = opt.errMsg;
    var lang = opt.lang;
 
    
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
					
					$("[name=btnSave]").removeClass("btn-primary-loading");
 
					// selalu return sukses
					alert(lang.registrationSuccessful); 
 
					
				}, 'json');
			}); 
		 
        var slider =  $('.slick-list').slick({ 
              lazyLoad: 'ondemand',
			  autoplay:true,
			  autoplaySpeed:3000,
			  infinite: true, 
			  slidesToShow: 1,
			  slidesToScroll: 1,   
		}); 
        
 
        
//        slider.on('afterChange', function(event, slick, currentSlide) {
//            var vid = $(slick.$slides[currentSlide]).find('video');
//            if (vid.length > 0) {
//              slider.slick('slickPause');
//
//              var vidBanner = $('#vid');
//
//              vidBanner.stop();
//              vidBanner.currentTime = '0';
//              vidBanner.get(0).play(); 
//            }
//
//          });
//
//           slider.find('video').on('ended', function() { 
//            slider.slick('slickPlay'); 
//          });

    };
     
 
}