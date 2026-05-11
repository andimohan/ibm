var subMenuPosition = {};
var currentActiveSideMenu = '';
var stickyMenu = false;

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
     
       
        $(".show-hide-panel").click(function(){
            var obj = $(this).find(".content-panel");
            var visible = obj.is(":visible");
            
            $(this).find(".icon-arrow").toggleClass("hide");
             
            if(visible)
                obj.slideUp("fast");
            else
                obj.slideDown("fast");
        });
    
    
     
    
       var partnersSlide =  $('.partners-slide .slides').slick({ 
              lazyLoad: 'ondemand',
			  autoplay:true,
			  autoplaySpeed:3000,
			  infinite: true, 
			  slidesToShow: 6,
			  slidesToScroll: 6, 
              dots : true,
//              variableWidth: true, 
              nextArrow: '',
              prevArrow: '',
              responsive: [
                 {
                  breakpoint: 1100,
                  settings: {
                    slidesToShow: 5,
			        slidesToScroll: 5,   
                  }
                 },  
                  {
                  breakpoint: 900,
                  settings: {
                    slidesToShow: 4,
			        slidesToScroll: 4,   
                  }
                 },
                  {
                  breakpoint: 700,
                  settings: {
                    slidesToShow: 3,
			        slidesToScroll: 3,   
                  }
                 },
                  {
                  breakpoint: 600,
                  settings: {
                    slidesToShow: 2,
			        slidesToScroll: 2,   
                  }
                 },
                  {
                  breakpoint: 400,
                  settings: {
                    slidesToShow: 1,
			        slidesToScroll: 1,   
                  }
                 }
             ]
		}); 
    
        $('.partners-slide .loading-slide').remove();
        $('.partners-slide .slides').show();
   
        $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
        $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),2); });
        $(".inputnumber, .inputdecimal, .input-integer").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
 
        $(window).scroll(function(){
            var top = $(document).scrollTop(); 
            
            if(!stickyMenu && parseInt(top) > 130){
               $(".menu-bar").addClass("sticky");
               stickyMenu = true;
            }
            
            
            if(stickyMenu && parseInt(top) <= 10){
                  $(".menu-bar").removeClass("sticky");
                  stickyMenu = false;
            }
              
        });
       
        $(window).scroll();
});   


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
