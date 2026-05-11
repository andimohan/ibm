function News(opt){   
    var thisObj = this;    
     
    this.loadOnReady = function loadOnReady(){     
       
         $('.vid-popup').click(function(){ 
                var arrayPopup = new Array()
                arrayPopup['url'] = '/dialog-msg-popup.php?page=video&url='+ $(this).attr("rel"); 
                loadOverlayScreen(arrayPopup);  
         });
        
    };

}