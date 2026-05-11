var _LOADING_ICON_ = "<div style=\"width:100%; text-align:center; margin-top:2em;\"><span class=\"loading-icon fas fa-spinner fa-spin\"></span></div>";  

// OVERLAY TEMPLATE
var _LOADING_TEMPLATE_ = '<div style="text-align:center"><div class=\"loading-page-icon fas fa-spinner fa-spin\"></div></div>';
 
jQuery(document).ready(function(){ 
        /*addEventListener('beforeunload', function(event) {
          event.returnValue = 'Apakah Anda yakin akan meninggalkan halaman ini ?'; 
        });
    */
        
    onLoadScript();   
    
    $(".on-click-dismiss" ).on( "click", function(e) {  
        if ($(e.target).hasClass('no-click')) { return; }
        if ($(e.target).closest('.no-click').length) { return; }
        hideOverlayScreen();
    });
    
});  

function disabledButton($obj,status){
    
    if (status == undefined)
        status = true;
      
    $obj.each(function(i) {     
        $(this).prop('disabled', status);
         
        if (status == true) 
            $(this).find(".loading-icon").show(); 
        else 
            $(this).find(".loading-icon").hide();  

    }); 
}

function hideOverlayScreen(){   
    $("html, body").css("overflow","inherit"); 
    $("#popup-panel").fadeOut("fast");   
    $(".hide-on-dismiss").hide();
} 


function loadOverlayScreen(arrParam){      
    $(':focus').blur();
    $("html, body").css("overflow","hidden");
    $("#popup-panel").fadeIn("fast");  
}

/*
function openQuickView (url,opt) { 
    // kalo mobile  
    var windowsWidth = $(document).width();
      
   if(windowsWidth < 720 && opt.hrefLink){ 
       window.location.href = opt.hrefLink; 
       return;
   }
    
    loadOverlayScreen();   
    $(".quick-view-panel .loading-progress").show(); 
    $(".quick-view-panel .content").html("");
    $(".quick-view-panel .content").load(url, function(){$(".quick-view-panel .loading-progress").hide();}); 
     
    $(".quick-view-panel").css("width","");
    $(".quick-view-panel").css("margin-left","");
    
    if(opt){
        if(opt.width){ 
            var width = opt.width;
            var marginLeft = -width / 2;
            $(".quick-view-panel").css("width",width+'px');
            $(".quick-view-panel").css("margin-left",marginLeft+'px');
        } 
    }
   
    $(".quick-view-panel").show(); 
}*/