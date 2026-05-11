function ProductsDetail(opt){   
    var thisObj = this;
    var lang = opt.lang;
  
    this.calculateSubtotal = function calculateSubtotal(){

        var qtyObj = $("[name=\'orderQty[]\']");
        var price = parseInt(unformatCurrency($(".product-price .price-value").text())) || 0;
        var qtyValue =  parseInt(unformatCurrency(qtyObj.val())) || 0;  
        
        var subtotal = price*qtyValue;
        $(".subtotal-value").text(subtotal).blur();
    };
    
    this.loadOnReady = function loadOnReady(){
       
      $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
         
     $('.read-more-expand').click(function() { 
        var descObj = $(".product-description");
        descObj.toggleClass("trim-height"); 
         
        var temp = $(this).text();
        $(this).text($(this).attr("attr"));
        $(this).attr("attr", temp); 
         
     });
        
     var productImageSlider = $('.product-image').slick({
		  autoplay:true,
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          fade: true,
          dots : false, 
          asNavFor: '.product-img-nav', 
        });
      
        $('.product-img-nav').slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          asNavFor: '.product-image', 
          focusOnSelect: true,
          //centerMode:true,
            
          nextArrow: '<div class="slick-arrow  next-arrow"><i class="fas fa-chevron-right"></i></div>',
          prevArrow: '<div class="slick-arrow  prev-arrow"><i class="fas fa-chevron-left"></i></div>',
        });
    
        $('#order-form')
                    .bootstrapValidator({ 
                        feedbackIcons: {
                            valid: 'glyphicon glyphicon-ok',
                            invalid: 'glyphicon glyphicon-remove',
                            validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        orderQty: {
                            validators: { 
                                greaterThan: {
                                    value: 0,
                                    inclusive: false,
                                    separator: ',', 
                                    message: '{{ ERRORMSG.qty[1] }}'
                                }
                            }
                        },

                    }
                })
                .on('success.form.bv', function(e) {
            
                    // Prevent form submission
                    e.preventDefault(); 
                    // Get the form instance
                    var $form = $(e.target);

                    // Get the BootstrapValidator instance
                    var bv = $form.data('bootstrapValidator');

                    // Use Ajax to submit form data
                    $.post($form.attr('action'), $form.serialize(), function(result) {
                        //console.log(result);
                        // alert("Your message has been sent and we will be in touch with you as soon as possible.");
                         //updateCartStatus();
                         location.href="/cart";
                    }, 'json');
                });

        
        $('.btn-ctr').on('click', function () {
            var ctr = parseInt($(this).attr('attr-ctr')) || 0;
            var qtyObj = $("[name=\'orderQty[]\']");
            var qtyValue =  parseInt(unformatCurrency(qtyObj.val())) || 0;  
            qtyValue += ctr; 
            if(qtyValue<1) qtyValue = 1; 
            qtyObj.val(qtyValue).blur();
             
            thisObj.calculateSubtotal();
            
        });
        
        $("[name=\'orderQty[]\']").on('blur', function () { 
            var qty = parseInt($(this).val()) || 0;
            if (qty < 1) {
                $(this).val(1).blur();
                thisObj.calculateSubtotal();
            }
        });
        
        $(".variant").on("click", function () {
            
            var price = parseInt($(this).attr("rel-price")) || 0;
            var refkey = $(this).attr("rel-refkey");
             
            var targetSlide = $('.variant-img-' +refkey ).closest(".index").attr("rel-index"); 
            $('.product-img-nav').slick('slickGoTo',targetSlide-1);
 
            //$(".total-price").text(Number(price));
            $("[name=\'hidItemVariantKey[]\']").val(refkey);
            $(".product-name").text($(this).attr("rel-name"));
            $(".product-price .price-value").text(price).blur();

            // Change active class
            $(".variant").removeClass("active");
            $(this).addClass("active");
 
            thisObj.calculateSubtotal();
            
        });
        
    
       if ($(".variant").length > 0) {
            $(".variant").first().click();
        } else {
            thisObj.calculateSubtotal();
        }
        
        $('.btn-share-wa').on('click', function (e) { 
             
            var msg = "";
            
             $.ajax({
                type: "GET",
                url:  '/ajax-item.php',
                async: false,
                data: "action=getCTA",  
            }).done(function( data ) {  
                if(data.length == 0) return;
                msg = data;
            });  
 
            sendWAMsg(msg);
        });
        
        
        $('.btn-fav').on('click', function (e) {  
            var itemKey = $('[name="hiditemkey[]"]').val();

            $.ajax({
                        type: "POST",
                        url:  '/ajax-item.php',  
                        data: 'action=updateFavoritProduct&itemkey=' + itemKey , 
                        success: function(data){    
                            $('.btn-fav').toggleClass("text-red-cardinal"); 
                            //$('.btn-fav').removeClass().addClass("text-red-cardinal"); 
                            $('.btn-fav i').toggleClass("fa-solid fa-regular"); 
                            //data = JSON.parse(data);
                        }  
                    }); 
       });
    };
   
}