function ProductCategories(opt){   
    var thisObj = this;    
    
    this.loadOnReady = function loadOnReady(){   
        $(window).bind("load resize", function() {    
            var menuHeight = parseFloat($(".header-menu").height()) || 0;
            var windowHeight = parseFloat($(window).height()) || 0;
            var bannerHeight = windowHeight - menuHeight;
            $(".hero").height(bannerHeight); 
        });
      
    };
    
      
 
}