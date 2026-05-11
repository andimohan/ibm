// hati2 ini masih global
jQuery(document).ready(function(){ 
   	    
		$('body').scrollToTop({
			distance: 200,
			speed: 1000,
			easing: 'linear',
			animation: 'fade', // fade, slide, none
			animationSpeed: 500,
			  
			trigger: null, // Set a custom triggering element. Can be an HTML string or jQuery object
			target: null, // Set a custom target element for <a href="http://www.jqueryscript.net/tags.php?/Scroll/">scrolling</a> to. Can be element or number
			text: '<div class="back-to-top"></div>', // Text for element, can contain HTML 
			skin: null,
			throttle: 250, 
			namespace: 'scrollToTop'
		}); 
     
        $(".input-date" ).datepicker({ 
                            currentText: 'Now', 
                            dateFormat:'dd / mm / yy',
                            changeMonth: true, 
                            changeYear: true,
                            showButtonPanel: true,
                            beforeShow : function(input, inst) {  
                                inst.dpDiv.removeClass('month-year-datepicker');
                            }
                            }); 
    
        //$(".input-month" ).datepicker({  changeMonth: true,  changeYear: true,  showButtonPanel: true,  dateFormat: 'MM yy', onClose: function(dateText, inst) { $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1)).change(); }});
        //$(".input-month" ).focus(function () {  $(".ui-datepicker-calendar").hide(); });
    
        $('.input-month').datepicker({
                        dateFormat: "MM yy",
                        changeMonth: true,  
                        changeYear: true,
                        showButtonPanel: true,
                        onClose: function(dateText, inst) {

                            function isDonePressed(){
                                return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
                            }

                            if (isDonePressed()){
                                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                $(this).datepicker('setDate', new Date(year, month, 1)).trigger('change');

                                 $('.input-month').focusout()//Added to remove focus from datepicker input box on selecting date
                            }
                        },
                        beforeShow : function(input, inst) {

                            inst.dpDiv.addClass('month-year-datepicker');

                            if ((datestr = $(this).val()).length > 0) { 
                                var d = new Date(datestr);  
                                year = d.getFullYear();
                                month = d.getMonth();
                                $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
                                $(this).datepicker('setDate', new Date(year, month, 1));  
                            }
                        }
             });
  
        $(".input-date").change(function() {   
             
            var prevDateObj;
            var currDateObj;
            
            $(this).closest('.mnv-date-range').find('.input-date').each(function() {     
                 if (prevDateObj){   
                     var prevDate = Date.parse(convertDateToStandartFormat(prevDateObj.val()));
                     var currDate = Date.parse(convertDateToStandartFormat($(this).val()));

                     if (prevDate > currDate) 
                        $(this).val(prevDateObj.val()); 

                 } 
                 prevDateObj = $(this);
            });  
		});

        $(".input-month").change(function() {   
            var prevDateObj;
            var currDateObj;
            
            $(this).closest('.mnv-date-range').find('.input-month').each(function() {     
                 //console.log("test");
                 if (prevDateObj){   
                     var prevDate = Date.parse(convertDateToStandartFormat(prevDateObj.val()));
                     var currDate = Date.parse(convertDateToStandartFormat($(this).val()));

                     if (prevDate > currDate) 
                        $(this).val(prevDateObj.val()); 

                 } 
                 prevDateObj = $(this);
            });  
		});

		$( ".report .toogle-criteria" ).on( "click", function() { 	$(".criteria-panel").toggle();  }); 
		$( ".report .show-filter-information" ).on( "click", function() { 	
            $(".filter-information").toggle(); 
            var rel = $(this).text();
             $(this).text($(this).attr("rel"));
             $(this).attr("rel",rel);
        });  
		$( "#popup-panel .closebutton" ).on( "click", function() {hideOverlayScreen()});  
		
        $('.multi-selectbox').searchableOptionList({
               maxHeight: '250px',
               showSelectAll: true,
               showSelectionBelowList: true
        });
		
        
		$(".sortable").bind( "click", sortableHandler);
		 
		$("#filterForm").submit(function(e) {  
				
            if($("[name=btnSubmit]").prop('disabled')) return true;
            
            $(".export-excel, .export-template").find(".download-icon").show();
            $(".export-excel, .export-template").find(".check-icon").hide();

            if ($("[name=hidExportExcel]").val() == 1){
                
            }else{  
                e.preventDefault(); 
                $("[name=hidExportExcel]").val(0);
				$("[name=hidRs]").val("");
                updateData(); 
            }
		 	
		}); 
		
		$(".print-report").click(function(e) {  
			 window.print();
		}); 
     
		$(".export-excel, .export-template").click(function(e) { 

            // STOP if disabled
            if ($(this).hasClass("disabled")) {
                e.preventDefault();
                return false;
            }

            $(this).find(".download-icon").hide();
            $(this).find(".check-icon").show();
            
            var exportType = $(this).attr("reltype");
            $("[name=hidExportExcel]").val(exportType);
            
            $("[name=btnSubmit]").prop('disabled', true);  
            $("#filterForm").attr("target","_blank");
            $("#filterForm").submit(); 
            $("[name=btnSubmit]").prop('disabled', false);  
            
            $("[name=hidExportExcel]").val(0);
		}); 
    
		$(".ai-assist").on( "click", function() {

            // STOP if disabled
            if ($(this).hasClass("disabled")) {
                e.preventDefault();
                return false;
            }

            
			// Create form dynamically
			var form = $('<form>', {
				action: 'https://wintera.co.id/ai-analyze',
				method: 'POST',
				target: '_blank'
			});
            
			// Add hidden input
			$('<input>').attr({ type: 'hidden', name: 'fileData', value: $("[name=hidFileData]").val() }).appendTo(form);
			
			// Append form to body, submit, then remove
			form.appendTo('body').submit().remove();

		}); 
		 
		  
		$(document).keyup(function (e){ 
			 try{	
					switch(e.keyCode || e.which) {
						 
						case 115: toggleAll(); 
								  e.preventDefault();	
								  break; 
						default:
							break; 
					}
			 } catch (err){
				 
			 }
				 
		});
		
		$('.sortable').attr("reltype",-1);
		$(".sortable" ).append('<div class="order-type"></div>');
	 
		$(".report .toogle-criteria" ).click();
     
        if (autoLoad == 1) updateData();
		
});   

var sortableHandler = function( event ) {  
  
    if($("[name=btnSubmit]").prop('disabled'))  return true;

    var ordertype = $(this).attr("reltype");
    var orderby = $(this).attr("relcol"); 
    $('#filterForm [name=hidOrderBy]').val(orderby); 
    $('#filterForm [name=hidOrderType]').val(ordertype); 

    $(".sortable").removeClass("sortable-active");
    $(".sortable .order-type").removeClass("arrow-up").removeClass("arrow-down").hide();

    $(this).addClass("sortable-active");

	var arrowClass = (ordertype == 1) ? "arrow-down" : "arrow-up";
	$(this).find(".order-type:first").addClass(arrowClass).show();
    $(this).attr("reltype",ordertype * -1); 

	//$('#filterForm').submit();
	
	// kalo ad hidFileData, sort di php saja  
	updateData(true);
}


function toggleAll(){
    $expandableRow = $(".expandable-report-row");
    if ($expandableRow.length <=0 )
        return;
    
	 var visibleRow = false;
	
	 $(".detail-row:visible").each(function(i) { 
			visibleRow = true;
	 });
	 
	 if (visibleRow)
	 	  $(".detail-row").hide("fast");
	 else
	 	  $(".detail-row").toggle("fast"); 
		 
}


function setFixedColumn(){   
	
      if (!FIXED_COLUMN)
          return; 
	
      var totalFreezeCol = parseInt($('[name=hidTotalFreezeCol]').val()) || 2; 

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
 
function updateData(){
     
        $("[name=btnSubmit]").prop('disabled', true);   
        $(".menu-item").addClass("disabled");
    
		//$(".rewrite-row").remove();
        $(".main-table tbody").html("");
		$('.loading-icon-panel').css("display","block"); 
		 
        $('.report-container').scrollTop(0);
        
		$.ajax({ 
				type: 'post',
				dataType: 'json',
				data: $("#filterForm").serialize(), 
				success: function(data) {  

						if(data.fileData)
							$('[name=hidFileData]').val(data.fileData);

						//var tableHeader = data.tableHeader; 
						$('.loading-icon-panel').hide();   

						if(data.header)
							$(".report-content .table-header-container").html(data.header);  

						var arrRS = {};
						arrRS['rs'] = data.rs;
						arrRS['arrFilterInformation'] = data.filterInformation;
					
						$("[name=hidRs]").val(JSON.stringify(arrRS));  
						$(".main-table tbody").append(data.content);
						$(".main-table > tbody").append("<div style=\"clear:both; height: 2em;\"></div>");

						var filter = "";
						for (i=0;i<data.filterInformation.length;i++){
							filter += '<div class="div-table-row"><div class="div-table-col">'+data.filterInformation[i].label+'</div><div style="padding:0 0.5em;">:</div><div class="div-table-col">' +data.filterInformation[i].filter  +'</div></div>';
						}

						if (filter != "") filter = '<div class="div-table">' + filter + '</div>';

						$(".filter-information").html(filter);

						if(data.footnote) $(".filter-information").append(data.footnote);

						if (filter.length == 0)
							$(".show-filter-information").hide();
						else 
							$(".show-filter-information").attr("display","inline-block").show(); 

						$(".expandable-report-row").bind( "click", function( event ) {   
								$(this).next('.detail-row').toggle("fast");
						}); 

						$("[name=btnSubmit]").prop('disabled', false);  

						if(data.header)
							$(".sortable").unbind('click').bind( "click", sortableHandler);

						setFixedColumn(); 
                    
                        $(".menu-item").removeClass("disabled");
				  }
			});		 	 
}
				
/*
function clearAutoCompleteInput(obj,hidKeyName){    
	$(obj).val("");   
	$(obj).closest('form').find("[name="+hidKeyName+"]").first().val(""); 
}
*/
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



function convertDateToStandartFormat($date){
    var parts = $date.split(' / ');
    var formatedDt = parts[2]+'-'+parts[1]+'-'+parts[0];
     
    return formatedDt;
}

function setAutoComplete(tabID, autoCompleteObj){
     
        var objName = autoCompleteObj.objName;
        var objValue = autoCompleteObj.objValue;
        var url = autoCompleteObj.url;
            
        $( "#" + tabID + " [name="+objName+"]" ).autocomplete({
		  source: url,
		  minLength: 1,
		  select: function( event, ui ) {      
		   		$("#"+ tabID + " [name="+objValue+"]" ).val(ui.item.pkey); 
			},  
		  search: function( event, ui ) { },
		  change: function( event, ui ) { 
		  		 //if (ui.item == null) 
				 //clearAutoCompleteInput(this,objValue);
				 
			},
		}).change(function() {
		  // if ($(this).val() == "") 
          // clearAutoCompleteInput(this,objValue); 
		});
    
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

function updateChkPick(obj,onChangeFunc){ 
    var obj = $(obj);  
    var container = obj.closest(".mnv-checkbox-group");
    
    if (obj.attr("relignore"))
        return; 

    var chkPick = container.find("[name='chkPick[]']:enabled"); 

    chkPick.prev().attr("relignore", true); 
    chkPick.val(obj.next().val()).change();
    chkPick.prev().removeAttr("relignore"); 
 
    // cukup sekali, gk perlu setiap klik detail dihitung ulang 
    if(onChangeFunc) onChangeFunc();
}