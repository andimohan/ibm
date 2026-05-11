function Products(opt){   
    var thisObj = this;  
    var history = opt.history;
	//var city = opt.city;
 
	//this.updateMarker = function updateMarker(){ 
 //
	//	// cari perbandingan L dan W dalam %
	//	var defW = 1000;
	//	var defH = 370;
//
	//	var mapW = $('.map-indo').width();
	//	var mapH = $('.map-indo').height();
//
	//	var scaleW = mapW/defW;
	//	var scaleH = mapH/defH;
//
//
	//	$(".marker").remove();
	//	$.each( city, function( key, value ) {
//
	//		 adjX = 0;
	//		 adjY = 0;
	//		 var pos = value.maplocation.split(',');  
	//		 var posX = (parseFloat(pos[0].trim()) + adjX) * scaleW;
	//		 var posY = (parseFloat(pos[1].trim()) + adjY) * scaleH;
//
	//		 var templateName= 'marker-template';
//
	//		 var newMarker = $('.'+templateName).clone().appendTo(".map");
	//		 newMarker.removeClass(templateName).addClass("marker");
	//		 newMarker.css({top: (posY)+"px", left: (posX)+"px", position:'absolute', zIndex:999}).show(); 
	//		 newMarker.attr("rel-name",value.name );
 //			 newMarker.attr("title",'<div><div><b>'+value.name+'</b></div><div>'+value.address.replace(/\r?\n/g, '<br>')+'</div></div>');
 //			 newMarker.addClass("tooltipster");
	//	});
	//	
	//	
	//	$('.tooltipster').tooltipster({ contentAsHTML : true, 
	//								   theme : "minerva" 
	//								  });
	//}
 //   
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
        
              $.ajax({
                          type: "POST",
                          url:  '/ajax-item.php',  
                          data: 'action=updateFavoritProduct&itemkey=' + itemKey , 
                          success: function(data){    
                              favIcon.toggleClass("text-red-cardinal");  
                          }  
                      }); 
         });
      };
  

}