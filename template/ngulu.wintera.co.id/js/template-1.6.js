var subMenuPosition = {};
var currentActiveSideMenu = '';
var adjMenu = 70; 

/* columnConform */ 
function columnConform(obj) { 
    
	 var topPosition = 0;
	 var currentRowStart = -1;
	 var currentTallest = 0;
	 var rowDivs = new Array();

	 // reset height when resize
	 $(obj).each(function() { 
		$(this).height("auto"); 
	 });

		
	 $(obj).each(function(index) {
		  
		 topPosition =  $(this).offset().top;  
		 
		 // start
		 if(currentRowStart == -1) { currentRowStart = topPosition; currentTallest =  $(this).height();}
		 
		 // new row
		 if (currentRowStart != topPosition) {
		
			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) 
				rowDivs[currentDiv].height(currentTallest);    
			 
		
			//reset
			currentTallest =  $(this).height();
			rowDivs.length = 0; // empty the array
			
			// get the new position after re-arrange
			topPosition =  $(this).offset().top;
			currentRowStart = topPosition;
			
		 } 
		 
	
		 if (currentTallest < $(this).height())
			currentTallest = $(this).height();
 
		 rowDivs.push($(this)); 
		 
	 });
		
	 // do the last row
	 for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) 
		rowDivs[currentDiv].height(currentTallest);     
  

}
 


jQuery(document).ready(function(){ 
           
        $(".input-date" ).datepicker({ showButtonPanel: true, currentText: 'Now', dateFormat:'dd / mm / yy', changeMonth: true,  changeYear: true}); 
        removeEventListenerByClass('prevent-form-submit', 'submit', preventSubmitBeforeJSLoaded); 
    
        onLoadScript(); 
        
        $(window).bind("load resize", function() {       
            columnConform('.auto-height'); 
        });
     
        // harus resize dulu diatas agar side menu selectednya bener, karena ad perhitungan recalculateObjProportion
        $(window).resize(); 
    
    
        // update posisi left menu 
//        $.each( $(".side-menu-item"), function(index, item) {  
//            var addr = $(this).attr("rel");   
//            var obj = $("."+addr);
//
//            if(obj.length == 0) return;
//            
//            var divTopPos = obj.offset().top - adjMenu; 
//            subMenuPosition[index] = {"key" : addr ,"pos" : divTopPos };  
//        });
//    
//        $(".main-menu .dropdown").hover(
//              function() {  
//                 showSubmenu($(this).attr("rel"));
//              }, function() {  
//                 hideSubmenu($(this).attr("rel"));
//              }
//        ); 
//   
//        $(".submenu-bar").hover(
//              function() {
//                 showSubmenu($(this).attr("rel"));
//              }, function() {  
//                 hideSubmenu($(this).attr("rel"));
//              }
//        ); 
//    
//    
//        $(".side-menu-item").click(function(){ 
//                var addr = $(this).attr("rel");   
//                var obj = $("."+addr);
//            
//                if(typeof obj === 'undefined') return;
//             
//                var menuSticky = $(".submenu.menu-pills");
//                var menuStickyH =  (menuSticky.length == 0) ?  0 : menuSticky.height() + 50;
//             
//                $('html, body').stop().animate({scrollTop:obj.offset().top - adjMenu - menuStickyH},500);
//            }
//        ); 
   
       
        $(".show-hide-panel").click(function(){
            var obj = $(this).find(".content-panel");
            var visible = obj.is(":visible");
            
            $(this).find(".icon-arrow").toggleClass("hide");
             
            if(visible)
                obj.slideUp("fast");
            else
                obj.slideDown("fast");
        });
    
    
     
    
//       var featuredEvent =  $('.partners-slide').slick({ 
//              lazyLoad: 'ondemand',
//			  autoplay:true,
//			  autoplaySpeed:3000,
//			  infinite: true, 
//			  slidesToShow: 6,
//			  slidesToScroll: 6,   
////              variableWidth: true, 
//              nextArrow: '<i class="fa-regular fa-arrow-right arrow next-arrow"></i>',
//              prevArrow: '<i class="fa-regular fa-arrow-left arrow prev-arrow"></i>',
//              responsive: [
//                 {
//                  breakpoint: 1100,
//                  settings: {
//                    slidesToShow: 5,
//			        slidesToScroll: 5,   
//                  }
//                 },  
//                  {
//                  breakpoint: 900,
//                  settings: {
//                    slidesToShow: 4,
//			        slidesToScroll: 4,   
//                  }
//                 },
//                  {
//                  breakpoint: 700,
//                  settings: {
//                    slidesToShow: 3,
//			        slidesToScroll: 3,   
//                  }
//                 },
//                  {
//                  breakpoint: 600,
//                  settings: {
//                    slidesToShow: 2,
//			        slidesToScroll: 2,   
//                  }
//                 },
//                  {
//                  breakpoint: 400,
//                  settings: {
//                    slidesToShow: 1,
//			        slidesToScroll: 1,   
//                  }
//                 }
//             ]
//		}); 
   
        $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
        $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),2); });
        $(".inputnumber, .inputdecimal, .input-integer").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
//   
//        updateCartStatus(); 
//    
//
//        function updateSideMenuPos(){
//            var top = $(document).scrollTop(); 
//            var hiddenHeight = 25; // utk page selain homepage, 
//            var height = $(".menu-bar").height() - hiddenHeight;
// 
//            var menuSticky = $(".submenu.menu-pills"); 
//            var menuStickyH =  (menuSticky.length == 0) ?  0 : menuSticky.height();
//            var adj = (menuStickyH == 0) ? 170 : 0;
//            
//            var size = Object.keys(subMenuPosition).length;
//            
//            var totalHeaderHeight = height + menuStickyH;
//            var selectedObj = '';
//            for(var i=0; i<size ; i++){ 
//                if(top + totalHeaderHeight + adj > subMenuPosition[i].pos)
//                    selectedObj = subMenuPosition[i].key;
//            }
//            
// 
//            if(selectedObj != '' && selectedObj != currentActiveSideMenu){ 
//                $(".side-menu-item").removeClass("selected");
//                $('.side-menu-item[rel="'+selectedObj+'"]').addClass("selected"); 
//                
//                currentActiveSideMenu = selectedObj;
//                
//                $("[name=selSideMenu]").val(selectedObj);
//            }
//        }
//    
//        function updateMobileMenu(){
//             var offset = $(".menu-bar").offset().top;
//                if(offset > 0){
//                    if(!$(".home-menu-mobile").is(":visible")){
//                        $(".home-menu-mobile").show();
//                        $(".home-menu").hide(); 
//                    }
//                }else{ 
//                    if($(".home-menu-mobile").is(":visible")){
//                        $(".home-menu-mobile").hide();
//                        $(".home-menu").show();
//                    }
//                }
//        }
    
        $(window).scroll(function(){
            
            // ambil submenu yg sebelah kiri dulu 
            if($('.side-menu-item').length > 0)
                updateSideMenuPos();
             
//            updateMobileMenu(); 
        });
      
//        updateMobileMenu();
    
});   


function updateCartStatus(){  
    
//		$.ajax({
//			type: "POST",
//			url: "/ajax-cart.php",
//			data: "action=cartStatus",   
//			success: function(data){  
//                    if (data != ""){ 
//                        var temp =  JSON.parse(data);  
//                        if (temp.totalqty <= 0 )
//                            $(".mnv-cart-qty").hide();
//                        else
//                            $(".mnv-cart-qty").show();
//                         
//                        $(".mnv-cart-qty").text(temp.totalqty); 
//                    }
//                        
//			} 
//		}); 
}


function scrollToTopForm($form){  
    var formTop = $form.offset().top;  
    $('html, body').animate({scrollTop:formTop - 150},500);
}

function updateChkBoxOnClick(obj){   
    var chkValue = $(obj).prop("checked") ? 1 : 0;
    $(obj).val(chkValue); 
    $(obj).next().val(chkValue); 
} 

function updateChkBoxOnChange(obj){   
    
    var checked = "",chkValue = 0;
    
    if($(obj).val() == 1){
        checked = "checked";
        chkValue = 1;
    } 
    
    $(obj).prev().prop("checked",checked).change(); // dont use click !
    $(obj).prev().val(chkValue);
}

function clearAutoCompleteInput(obj,hidKeyName,revalidateField){    
	$(obj).val("");    
    
    if (jQuery.type(hidKeyName) == 'string')
	   $(obj).closest('form').find("[name='"+hidKeyName+"']").first().val(""); 
    else
        hidKeyName.val("");
    
    if (revalidateField == undefined)
        revalidateField = true;
 
    if (revalidateField)
	   $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
}


function reInsertSelectBox(selectbox,selectOpt, opt){
     
    // update combobox services
    var newOptions = {};
    
    // add rel
    
    // harus dibedain kalo tipena bukan "key & label"
    if(opt != undefined && opt['key']){  
        
        for(i=0;i<selectOpt.length;i++) { 
            var attrRel = {};
            if(opt.rel != undefined) 
               $.each(opt.rel, function(index, item) {   attrRel[index] = selectOpt[i][item]; })

            newOptions[selectOpt[i][opt.key]] =  {"label" : selectOpt[i][opt.label],"rel" : attrRel };        
        }
    }else{  
         $.each(selectOpt, function(key, item) {  newOptions[key] = item; });   
    }
     
    var lastSelectBox;
    selectbox.each(function(){ lastSelectBox = $(this); updateSelectBox($(this),newOptions); }); 
     
    // gk boleh yg terakhir, karena terkadang terakhir itu ad di row-template
    selectbox.first().find('option:eq(0)').prop('selected', true).change(); 
    
}


function updateSelectBox(select,newOptions){
    var options = (select.prop) ? select.prop('options') : select.attr('options');  
    
    $('option', select).remove(); 
   
    $.each(newOptions, function(opIndex, opItem) {  
        options[options.length] = new Option(opItem.label, opIndex); 
        
         if(opItem.rel != undefined ){ 
            $.each(opItem.rel, function(relIndex, relItem) { 
                select.find('option:eq('+(options.length-1)+')').attr(relIndex,relItem);
            });  
        } 
    });
 
}

function renumbering(obj){
    var ctr = 1;
    obj.each(function() {
        $(this).text(ctr++ + '.');
    });
}
