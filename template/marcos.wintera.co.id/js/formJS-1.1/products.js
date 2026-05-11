function Products(opt){   
    var thisObj = this;  
    var history = opt.history;
    
    
    this.getScrollInterval = function getScrollInterval(){    

      var docWidth = $( document ).width(); 
      return (docWidth < 500) ? '150px' : '336px';

    }
    
    this.loadOnReady = function loadOnReady(){     
        
            $('.horizon-prev').click(function() {
              event.preventDefault(); 
              $(this).closest(".scroll-scope").find(".ul-panel").stop().animate({
                scrollLeft: "-=" +  thisObj.getScrollInterval()
              }, "slow");
            });

            $('.horizon-next').click(function() { 
              event.preventDefault(); 
              $(this).closest(".scroll-scope").find(".ul-panel").stop().animate({
                scrollLeft: "+=" + thisObj.getScrollInterval() 
              }, "slow");
            });
 
    };

}