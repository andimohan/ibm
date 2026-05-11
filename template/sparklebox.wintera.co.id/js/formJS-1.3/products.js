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
      
      //thisObj.updateMarker(); 
      //$(window).resize(function(){ thisObj.updateMarker(); });
        
      $('.btn-fav').on('click', function (e) {  
              var itemKey = $(this).attr("relkey");
              var favIcon = $(this);
              favIcon.toggleClass("text-red-cardinal");  
              favIcon.toggleClass("fa-solid fa-heart fa-regular fa-heart");
              
              $.ajax({
                  type: "POST",
                  url:  '/ajax-item.php',  
                  data: 'action=updateFavoritProduct&itemkey=' + itemKey , 
                  success: function(data){    

                  },
                  error: function(){
                    favIcon.removeClass("text-red-cardinal");
                    favIcon.removeClass("fa-solid fa-heart");
                    favIcon.addClass("fa-regular fa-heart");
                  }  
              }); 
         });
  
      var hideFilter = $('.filter-hide-panel');
      hideFilter.click(function(){
        $('.aside-panel').toggle();
        $(this).find('i').toggleClass('fa-chevron-up fa-chevron-down');
      });


      var expandable = $('.category-text.expandable');
      expandable.on("click", function() {
          var $parentLi = $(this).closest(".parent-category");
          
          var $childList = $parentLi.find(".child-category-list").first();
          
          var $toggleIcon = $(this).find(".toggle-icon");
          
          if ($childList.length) {
              if ($childList.is(":visible")) {
                  $childList.hide();
                  $toggleIcon.removeClass("fa-caret-down").addClass("fa-caret-right");
                  $toggleIcon.removeClass("expanded");
              } else {
                  $childList.show();
                  $toggleIcon.removeClass("fa-caret-right").addClass("fa-caret-down");
                  $toggleIcon.addClass("expanded");
              }
          }
      });

};
  

}