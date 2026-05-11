function Index(opt){   
    var thisObj = this;    
//    var errMsg = opt.errMsg;
//    var lang = opt.lang;
    
    this.loadOnReady = function loadOnReady(){   
           
          $('.portfolio-list').slick({
          dots: true,
          autoplay:true,
          autoplaySpeed:1500,
          infinite: true,
          speed: 1000,
          slidesToShow: 5,
          slidesToScroll: 5, 
          nextArrow: '',
          prevArrow: '',
            responsive: [
                {
                  breakpoint: 1000,
                  settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4, 
                  }
                }, {
                  breakpoint: 900,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3, 
                  }
                },
                {
                  breakpoint: 700,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                  }
                },
                {
                  breakpoint: 550,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                } 
          ]
        });
 
        
        $(".btn-learn-more").click(function() {   
            var aTag = $(".product");
            $('html,body').animate({scrollTop: aTag.offset().top},'slow');
        }); 

        
         
    };

    
}