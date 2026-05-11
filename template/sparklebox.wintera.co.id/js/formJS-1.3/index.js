function Index(opt) {
	var thisObj = this;
	var errMsg = opt.errMsg;
	var lang = opt.lang;
	var city = opt.city;


	this.loadOnReady = function loadOnReady() {

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
								message: errMsg.email[3]
							}
						}
					},
				}
			})
			.on('success.form.bv', function (e) {

				$("[name=btnSave]").prop('disabled', true).addClass("btn-primary-loading");

				// Prevent form submission
				e.preventDefault();
				// Get the form instance
				var $form = $(e.target);

				// Get the BootstrapValidator instance
				var bv = $form.data('bootstrapValidator');

				// Use Ajax to submit form data
				$.post($form.attr('action'), $form.serialize(), function (result) {

					// selalu sukses kalo utk newsletter 

					$("[name=btnSave]").removeClass("btn-primary-loading");

					// selalu return sukses
					alert(lang.registrationSuccessful);


				}, 'json');
			});

		$('.slick-list').slick({ 
			lazyLoad: 'ondemand',
			dots: false, 
			autoplay: true,
			arrows: false,
			autoplaySpeed: 3000,
			slidesToShow: 5,
			responsive: [
				{
					breakpoint: 1280,
					settings: {
						slidesToShow: 4,
					}
				},
				{
					breakpoint: 680,
					settings: {
						slidesToShow: 3,
					}
				},
				{
					breakpoint: 400,
					settings: {
						slidesToShow: 2,
					}
				}
			]
		});
		$('.slick-list-hero').slick({ 
			lazyLoad: 'ondemand',
			dots: false, 
			autoplay: true,
			arrows: false,
			autoplaySpeed: 3000,
			slidesToShow: 1,
		});

		 $('.slick-list-testimonial').slick({
			lazyLoad: 'ondemand',
			//autoplay:true,
			autoplaySpeed:3000,
			infinite: true, 
			arrows: false,
			dots: true,
			slidesToShow: 3,
			slidesToScroll: 3,   
			adaptiveHeight: true,
            responsive: [
                 {
                  breakpoint: 1100,
                  settings: {
                    slidesToShow: 2,
                  }
                 },
				 {
                  breakpoint: 700,
                  settings: {
                    slidesToShow: 1,
                  }
                 }
             ]
		});

		$('.arrow-left').on('click', function() {
			$('.slick-list').closest('.slick-slider').slick('slickPrev');
		});

		$('.arrow-right').on('click', function() {
			$('.slick-list').closest('.slick-slider').slick('slickNext');
		});

	};

}