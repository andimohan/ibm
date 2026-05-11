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

/* template JS */
jQuery(document).ready(function(){ 
         
    
        $(window).bind("load resize", function() { columnConform('.auto-height'); }); 
        removeEventListenerByClass('prevent-form-submit', 'submit', preventSubmitBeforeJSLoaded);  
        onLoadScript(); 

        $(window).resize();  
 
        $.ajax({
			type: "POST",
			url: "/getPHPConfiguration.php",
			async: false, 
			success: function(data){  
					phpConfiguration = JSON.parse(data);   
			} 
		}); 
    
        $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
        $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),2); });
        $(".input-date" ).datepicker({ currentText: 'Now', dateFormat:'dd / mm / yy', changeMonth: true,  changeYear: true}); 
        $(".input-month" ).datepicker({  changeMonth: true,  changeYear: true,  showButtonPanel: true,  dateFormat: 'MM yy', onClose: function(dateText, inst) { $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1)).change(); }});
        $(".input-month" ).focus(function () {  $(".ui-datepicker-calendar").hide();  $("#ui-datepicker-div").position({  my: "center top",   at: "center bottom",  of: $(this) });  });
     
        $('.multi-selectbox').searchableOptionList({   maxHeight: '250px',  showSelectAll: true,   showSelectionBelowList: true  });
});    
   
var sortableHandler = function( event ) {  
    var ordertype = $(this).attr("reltype");
    var orderby = $(this).attr("relcol"); 
    $('[name=hidOrderBy]').val(orderby); 
    $('[name=hidOrderType]').val(ordertype * -1); 
     
    $(this).closest("form").submit();
}
 
function unformatCurrency(value){ 
	if (value == undefined)
		return 0;
		
	return value.replace(/,/g,"");
}	  	

function inputNumberOnBlur(obj,decimal){  	 
	  if(obj.val() == "" || !$.isNumeric(unformatCurrency(obj.val())) ) { 
		 obj.val(0);
		 
		 try {
			$(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));   
		 }
		 catch(err) {
			 
		}
 	  }
	  
	  if (decimal == undefined)
	  	decimal = 0 ;
	  obj.formatCurrency({roundToDecimalPlace: decimal });
}
   
function scrollToTopForm($form){
    var bodyTop = $('body').scrollTop();
    var formTop = $form.offset().top;
    var margin = 50;
     
    if (bodyTop > formTop - margin)
    $('body').scrollTop(formTop - margin); 
}

function convertToSlug(Text){
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
}

function reportOnLoad(){
    setFixedColumn();  
    $("[name=btnShowFilter]").click( function(){ 
        if ($(".filter-panel").is(":visible") == false) {  
            $(".filter-information").slideUp();
            $(".filter-panel").slideDown(); 
            $("[name=btnShowFilter]").hide();
            $("[name=btnUpdateFilter]").show();
        } 
    });
 
    $("[name=btnExportToExcel]").click( function(){ 
        $("[name=hidExportExcel]").val(1); 
        $("#form-criteria").prop("target","_blank"); 
        $("#form-criteria").submit();
        $("[name=hidExportExcel]").val(0); 
        $("#form-criteria").prop("target","_self"); 
    });

    $(".expandable-report-row").bind( "click", function( event ) {   
            $(this).next('.detail-row').toggle("fast");
    }); 
    
    //$('.sortable').attr("reltype",-1); 
    $(".sortable").bind( "click", sortableHandler);
}

function setFixedColumn(){   
      var totalFreezeCol = 2; 
        
      var reportContainer = document.querySelector('.report-container'); 
    
      var table = document.querySelector('.main-table');
      var leftHeaders = [].concat.apply([], document.querySelectorAll('tbody th'));
      var topHeaders = [].concat.apply([], document.querySelectorAll('thead th'));
      var dummyColHeaders = [].concat.apply([], document.querySelectorAll('.dummy-td'));
 
    
      var topLeft = document.createElement('div');
      var computed = window.getComputedStyle(topHeaders[0]);
 

      reportContainer.addEventListener('scroll', function (e) { 
        var x = reportContainer.scrollLeft;
        var y = reportContainer.scrollTop;
        var topHeaderH = new Array();
          
        leftHeaders.forEach(function (leftHeader,i) {  
          leftHeader.style.transform = translate(x, 0);
        });
        topHeaders.forEach(function (topHeader, i) {
          if (i < totalFreezeCol) {
            topHeader.style.transform = translate(x, y);
          } else {
            topHeader.style.transform = translate(0, y);
          } 
            
            
          topHeaderH[i] = topHeader.offsetHeight;
             
        });
           
       dummyColHeaders.forEach(function (dummyColHeader, i) {   
            dummyColHeader.style.transform = translate(x, y);
            dummyColHeader.style.height = topHeaderH[i] + "px";
       } );
       
 
        if(x > 5)
           $(".freeze-pane-border").addClass("freeze-pane-border-active");
        else
            $(".freeze-pane-border").removeClass("freeze-pane-border-active");
             
 
      });
     
    reportContainer.dispatchEvent(new Event('scroll'));
                   
}

function translate(x, y) {
    return 'translate(' + x + 'px, ' + y + 'px)';
}

