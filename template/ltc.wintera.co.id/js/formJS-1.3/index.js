function Index(opt) {
	var thisObj = this;
	var errMsg = opt.errMsg;
	var lang = opt.lang;
	var city = opt.city;
 
	this.updateMarker = function updateMarker(){ 
 
		// cari perbandingan L dan W dalam %
		var defW = 1000;
		var defH = 370;

		var mapW = $('.map-indo').width();
		var mapH = $('.map-indo').height();

		var scaleW = mapW/defW;
		var scaleH = mapH/defH;


		$(".marker").remove();
		$.each( city, function( key, value ) {

			 adjX = 0;
			 adjY = 0;
			 var pos = value.maplocation.split(',');  
			 var posX = (parseFloat(pos[0].trim()) + adjX) * scaleW;
			 var posY = (parseFloat(pos[1].trim()) + adjY) * scaleH;

			 var templateName= 'marker-template';

			 var newMarker = $('.'+templateName).clone().appendTo(".map");
			 newMarker.removeClass(templateName).addClass("marker");
			 newMarker.css({top: (posY)+"px", left: (posX)+"px", position:'absolute', zIndex:999}).show(); 
			 newMarker.attr("rel-name",value.name );
 			 newMarker.attr("title",'<div><div><b>'+value.name+'</b></div><div>'+value.address.replace(/\r?\n/g, '<br>')+'</div></div>');
 			 newMarker.addClass("tooltipster");
		});
		
		
		$('.tooltipster').tooltipster({ contentAsHTML : true, 
									   theme : "minerva" 
									  });
	}

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

		var slider = $('.slick-list').slick({ 
			lazyLoad: 'ondemand',
			dots: true, 
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
  
    thisObj.updateMarker(); 
    $(window).resize(function(){ thisObj.updateMarker(); });
   
	};

}