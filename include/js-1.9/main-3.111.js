var _LOADING_ICON_ = "<div style=\"width:100%; text-align:center; margin:1em 0;\"><span class=\"loading-icon fas fa-spinner fa-spin\"></span></div>";  
var _LOADING_ICON_SMALL_ = "<div style=\"width:100%; text-align:center; margin:1em 0; font-size: 0.8em\"><span class=\"loading-icon fas fa-spinner fa-spin\"></span></div>";  
var _DATE_FORMAT_ = "DD / MM / YYYY";
var _CONTROL_LABEL_HEIGHT_ = 20;

// OVERLAY TEMPLATE
var _LOADING_TEMPLATE_ = '<div style="text-align:center"><div class=\"loading-page-icon fas fa-spinner fa-spin\"></div></div>';

var selectedTab; // berguna untuk mengetahui tab yg sedang aktif. digunakan jg sebagai patokan fungsi QuickView 
var $tabs;
var tabParam = {};
var uploadedImage = {}; 
var uploadedFile = {};   
var objAndValueForDetailAutoComplete = {};    
var ckeditorList = {};
var autoSaveIntervalId = {}; 
var dashboardRedrawFunc = Array();
const EPSILON = 1e-10;  // tolerance threshold
var localSnapshotDB = null;

//var itemCache = {};

jQuery(document).ready(function(){
    
        //openSnapshotDB();  
     
        $("[name=restore]").click(function() {  
            restoreSnapshot();
        });
    
    
        var internetConnectedStatus = '';
        var firstConnection = true;
        function isInternetConnected(callbackhandler){  
         
            if(internetConnectedStatus != navigator.onLine){
                internetConnectedStatus = navigator.onLine;
                
                if (internetConnectedStatus) {
                    if (!firstConnection) // kalo koneksi berhasil, hanya muncul notif kalo pernah disconnect
                     mnvPushPopUpNotification(phpLang.internetConencted,"bg-green-avocado"); 
                } else { 
                    mnvPushPopUpNotification(phpLang.internetFailToConnect, "bg-red-cardinal"); 
                }
            }
            
            firstConnection = false; 
        } 
        setInterval(isInternetConnected, 10000); 
 
   
        $("#mnv-popup-notification .close-all").click(function() { 
            $("#mnv-popup-notification").find(".close-icon").click();
            $(this).hide();
        });
        
        // ============== fixed action bar
        $(window).resize( function() {  
            // katanya mengganggu
            //if ($(window).width() <= 1200) {
            //    $(".left-menu-col").addClass("collapsed");
            //} else {
            //    $(".left-menu-col").removeClass("collapsed");
            //    toggleLeftPanels();
            //}
            adjustVisibleColumn();  
            adjustActionBarPosition();  
            updateControlLabelPadding();
            adjustScrollableTable();
        }).trigger('resize');
    
        $(window).scroll(function (event) { 
            var pos = 75;
            var scrollpos = $(window).scrollTop();   
            var headerMenu = $(".action-bar-fixed");
            if (scrollpos > pos && !headerMenu.hasClass('fixed')){  
                 headerMenu.addClass('fixed'); 
            }else if (scrollpos <= pos && headerMenu.hasClass('fixed')){ 
                 headerMenu.removeClass('fixed');   
            }  
			
			// agar keupdate ketika scroll
			updateStickyStyle();
             
        }); 
	
        // ============== fixed action bar
    
        addEventListener('beforeunload', function(event) {
          event.returnValue = 'Apakah Anda yakin akan meninggalkan halaman ini ?'; 
        });

        function toggleDropdown(rootElem, submenuPanel, isOpen) {
            $(rootElem).toggleClass("open root-active", isOpen);
            if ($(rootElem).is(".left-menu-col.collapsed :hover")) {
            $(rootElem).removeClass("root-active");
            }

            if (isOpen) {
            $(submenuPanel).stop().slideDown("fast");
            } else {
            $(submenuPanel).stop().slideUp("fast");
            }
        } 
        
        function closeAllDropdowns() {
            $("#main-menu .root.open").each(function () {
            var rel = $(this).attr("rel");
            var submenu = $("#main-menu .submenu-panel-" + rel);
            toggleDropdown(this, submenu, false);
            });
        }


        function toggleLeftPanels() {
            var $statusInformation = $('.div-table-status-information');

            var $statusTab = $('[tabindex="0"][aria-labelledby="ui-id-2"]');
            var isStatusActive = $statusInformation.length > 0 && $statusTab.hasClass('ui-tabs-active'); 
          

            if (isStatusActive) {
                $('#left-status-panel').addClass('panel-visible');
                // console.log("status active");
            }
            if (!isStatusActive) {
                $('#left-status-panel').removeClass('panel-visible');
                // console.log("status not active");
            }
          }
          
        $(document).on('click', '.ui-tabs-tab', toggleLeftPanels);
        
        
        // EVENT LISTENER
        $("#main-menu .root").on("click", function () {
            var isCurrentlyOpen = $(this).hasClass("open");
            var rel = $(this).attr("rel");
            var submenu = $("#main-menu .submenu-panel-" + rel);
          
            if (isCurrentlyOpen) {
              toggleDropdown(this, submenu, false);
            } else {
              closeAllDropdowns();
              toggleDropdown(this, submenu, true);
            }
          });

 
        $(".show-left-menu-icon").on("click", function () { 
            $("#tabs-menu").tabs("option", "active", 0); // maksa utk klik tab menu
            closeAllDropdowns();
            toggleLeftPanels();
            setTimeout(function() {
                adjustVisibleColumn();
                adjustActionBarPosition();  
                updateControlLabelPadding();
                adjustScrollableTable();
            }, 500);
			$(".left-menu-col").toggleClass("collapsed"); 
		});

        $("#main-menu .submenu-panel").on("click", function (e) {
            e.stopPropagation();
        });



        // problem karena script php gk jalan di js !
    	//CKFinder.setupCKEditor(null, '<?php echo $class->defaultJsPath; ?>ckfinder/');   
    
     
 	   $(".menu-child").click(function(){   
			var reladdr = $(this).attr("reladdr");
			var reltarget = $(this).attr("reltarget");
			 
			if (reltarget == '_blank'){ 
					var win=window.open(reladdr, reltarget);
					win.focus();  
			}else {    
					addTab($(this).text(),reladdr,true,true);  
			}  
		});
			
		$(".menu-parent").click(function(){   
			 collapseAllMenu();	  
			 $(this).removeClass('inactive').addClass('active');
			 $(this).next('ul').show(500);   
		});
		
		$(".menu-setting").click(function(){   
			 	addTab($(this).text(),$(this).attr("reladdr"),true,true); 
		}); 
       
		collapseAllMenu();	  
			
        $tabs = $("#tabs").tabs({ 
		  activate: function(event, ui){ 
				selectedTab = ui;  
                adjustVisibleColumn();
                adjustScrollableTable();
				updateStatusPanel();   
              
                // redraw function graph 
                if (getSelectedTabIndex() == 0)  
                    $.each( dashboardRedrawFunc, function( key, value ) {value();});    
              
                updateStickyStyle();
		  },
		  load: function( event, ui ) {
              adjustVisibleColumn();
              adjustScrollableTable();
          },
		  beforeLoad: function( event, ui ) {  
				if ( ui.tab.data( "loaded" ) ) {
					event.preventDefault();
					return;
				}else{
                    ui.panel.html(_LOADING_ICON_).fadeIn();
                }
		 
				ui.jqXHR.done(function() {
                    ui.panel.fadeIn();
					ui.tab.data( "loaded", true );
				});
		 }
			
		});
		
		var $tabsMenu = $("#tabs-menu").tabs({
		  activate: function(event, ui){
		 		var newp = ui.newPanel.hide().attr('id'),
				oldp = ui.oldPanel.attr('id');
				$('#' + oldp).fadeOut(500);
				$('#' + newp).fadeIn(500);	
		  }, 
		});  
	
		addTab('Dashboard','summaryDashboard',false);   
                      
		$tabs.delegate("span.ui-icon-close","click",function(){
			 var panelID = $(this).closest("li").remove().attr("aria-controls");
			 $("#" + panelID).remove();   
			 $tabs.tabs("refresh");   

             if(autoSaveIntervalId[panelID])  clearInterval(autoSaveIntervalId[panelID]); 
		}); 

		$(document).keyup(function (e){
			
			 try{	
					switch(e.keyCode || e.which) {
						
						case 46:  $("#btn-delete-"+selectedTab.newPanel[0].id).click();
								  e.preventDefault();	
								  break;
								  
						case 13: if ($("[name=quick-search-" + selectedTab.newPanel[0].id + "]").is(":focus")) {
                                        
                                        /*var quickSearchObj = $("[name=quick-search-" + selectedTab.newPanel[0].id + "]"); 
                                        if (trim(quickSearchObj.val() == "")
                                            return;*/
                            
										//$("[name=btn-quick-search-" + selectedTab.newPanel[0].id +"]").click();
                                        $("[name=selPage-"+selectedTab.newPanel[0].id +"] option:first").attr('selected','selected');
                                        updateData(false);
								  }
								  e.preventDefault();	
								  break;
						case 113: $("#btn-add-new-"+selectedTab.newPanel[0].id).click();
								  e.preventDefault();	
								  break;
						case 114: $("[name=quick-search-" + selectedTab.newPanel[0].id +"]").select();
								  e.preventDefault();	
								  break;
						case 115: toggleAllSelectedDataDetail();   
								  e.preventDefault();	
								  break;
						case 116:
								 $("#btn-refresh-" + selectedTab.newPanel[0].id).click();
								 e.preventDefault();	
								 break;
						default:
							break; 
					}
			 } catch (err){
				 
			 }
				 
		}); 
		
		/*$('body').scrollToTop({
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
		});  */ 
		
		$("#popup-panel .closebutton" ).on( "click", function() { hideOverlayScreen() });
        //updateDiskUsage(); 
    
        //cachingData();
    
        $(window).resize();
});  

/*function cachingData(){
    // item
     $.ajax({ 
          url: 'ajax-item.php',  
          method : 'GET',
          data: 'action=getCache',  
          success: function(data){   
               data = JSON.parse(data); 
               itemCache = data;
           } 
        });
}*/

function updateThemeSettings(settingkey,value){
     $.ajax({ 
          url: 'ajax-theme-settings.php',  
          method : 'POST',
         data: {action:'update',
                settingkey:settingkey,
				value : value
               } 
        });
}


function updateStickyStyle(){  
	if (typeof selectedTab === 'undefined') return;
	
	var selectedTabId = selectedTab.newPanel[0].id; 
    var offsetSticky = 0;
    var currOffsetStickey = 0; 
    var parentObj  = $("#"+selectedTabId); 
    var formHeight = $("#defaultForm-" + selectedTabId).height(); 
   
    var adj = 50;
    
    parentObj.find('.form-button-panel').each(function() {   // idealnya cuma 1 baris 
        butttonPanelHeight =  parseInt($(this).outerHeight()); 
        offsetSticky = parseInt($(this).position().top); 
        
        if(formHeight - butttonPanelHeight + adj < offsetSticky )
             $(this).removeClass("sticky");
        else
             $(this).addClass("sticky"); 
    });
}

function updateControlLabelPadding(){ 
    var elName = '.control-label';
    $(elName).each(function() { 
    var paddingTop = ($(this).height() > _CONTROL_LABEL_HEIGHT_)  ? 0 : "7px";
    $(this).css("padding-top",paddingTop); 
    }); 
}

/*function updateDiskUsage(){
      $.ajax({ 
          url: 'ajax-notification.php',  
          method : 'POST',
          data: 'action=getDiskUsage&unit=gb',  
          success: function(data){   
               data = JSON.parse(data); 
               var fileSize = parseFloat(data.filesize); 
		       $("#left-notification-panel .file-disk-usage").html(fileSize).formatCurrency({roundToDecimalPlace: 2 });
           } 
        });
}*/

function adjustScrollableTable(){
    
     var tableCostObj = $(".table-scrollable"); 
     if(tableCostObj.length == 0) return;  

     var scrollPanelObj = tableCostObj.closest(".scroll-panel"); 

     var tableCostWidth = tableCostObj.width();
     var parentWidth =  tableCostObj.closest("form").find(".table-scroll").width(); // nanti perlu adjustment utk menyesuaikan table lain

     if(parentWidth == 0) return; // kalo dari form lain, selalu 0, kemungkinan karena dihide

     //var scrollPanelObj = tabObj.find(".scroll-panel"); 
     //var tableCostWidth = tabObj.find(".table-cost").width();
     //var parentWidth = scrollPanelObj.closest(".table-scroll").width(); 

     var panelWidth = tableCostWidth;
     var widthAdj = 0; 
     if (tableCostWidth > (parentWidth-20)) {
         panelWidth = parentWidth;
         widthAdj = -40;
     }

     panelWidth += widthAdj; // adj

     scrollPanelObj.css("width",panelWidth+"px");
}

function adjustVisibleColumn(){
     
  // hilangkan kolom terakhir kalo gk muat
  // perlu tau harus berapa byk kolom yg dihide
    
  var minWidth = 200;    
    
    $(".panel-data-list .container").each(function(){     
        var containerWidth = parseInt($(this).width());
        if (containerWidth == 0) return; 
        
        var totalColWidth = 0;
        var tableHeader = $(this).find(".table-data-list");
        var tableData =  $(this).find(".table-data-record-header");
        if (tableHeader == undefined ||  tableHeader.width() == undefined) return; 
                                    
        tableHeader.find(".col-header").each(function(index){    
            var colWidth = parseInt($(this).attr("relwidth")) || 0;
            var colName = $(this).attr('relcol');
            
            // khusus kolom yg autowidth sendiri
            if(colName != undefined && colWidth == 0)  colWidth = minWidth; 
                
            totalColWidth += colWidth; 
            
            if (totalColWidth > containerWidth) {
                $(this).addClass("hide");
                tableData.find(".div-table-col:nth-child("+(index+1)+")").addClass("hide");
            } else {
                $(this).removeClass("hide");   
                tableData.find(".div-table-col:nth-child("+(index+1)+")").removeClass("hide");
            }
             
        })
        
        // khusus col terkhir
        tableHeader.find(".col-header:last-child").removeClass("hide");
        tableData.find(".div-table-col:last-child").removeClass("hide");
         
    });  
    
    // utk table costRateForm, adjsut scrollbar 
    //$(window).resize(); // gk boleh panggil, looping forever

}

function adjustActionBarPosition(){
//      $(".action-bar-fixed.fixed").each(function(){    
//                var windowsLeft = $(window).scrollLeft();
//                var containerLeft = $(this).closest(".container").offset().left; 
//                $(this).css('left', containerLeft - windowsLeft); 
//        });
}

function mnvPushPopUpNotification(content,className){
      
    if(!className)
        className = '';
    
    var el = $("#mnv-popup-notification");
    
    var $template = el.find(".template"),
          $newRow   = $template.clone().removeClass("template").addClass(className).insertBefore($template.first());

    $newRow.find(".content").html(content);  
    $newRow.find(".close-icon").bind("click", function(event){
                    var row = $(this).closest(".list"); 
                    row.fadeOut(500, function(){ row.remove(); })
            })
    $newRow.show();
    mnvUpdateNotificationSettings()
}

function mnvUpdateNotificationSettings(){
    
    var el = $("#mnv-popup-notification");
    
    //count total notification 
    var totalNotif = el.find(".list").length;    
    
    if (totalNotif > 2 )
        el.find(".close-all").show();
    else
        el.find(".close-all").hide(); 
}

function editTagList(obj){
     $("#tag-filter .save-tag-button, #tag-filter .edit-tag-button, #tag-filter .tag-input, #tag-filter .tag-list").toggle(); 
}

function saveTagList(obj){ 
     $("#tag-filter .save-tag-button, #tag-filter .edit-tag-button, #tag-filter .tag-input, #tag-filter .tag-list").toggle(); 
    
    // Get the form instance
     var $form =  $(obj).closest("form");
    
     $.ajax({
      type: "POST",
      url: 'ajax-tag.php',  
      data: $form.serialize(),  
      dataType: 'json'
    }); 
     
    updateData(false);
    //success :  selectedTab.newTab[0].remove(),
       
}

function setOnDocumentReady(tabID,obj){
    
    if(obj != undefined)
        tabParam[tabID].obj = obj;
    
    $("#" + tabID + " #defaultForm").attr("id","defaultForm-"+tabID); 
 
    if (!$("#" + tabID + " [name=btnSave]").is(":visible")){ 
        disableFormSaveOnEnter($("#defaultForm-"+tabID));  
    }
    
    $("#" + tabID + " [name=btnSaveAndProceed]").click(function(){ $("#" + tabID + " [name=hidSaveAndProceed]").val(1) ; });   
    $("#" + tabID + " [name=btnSave]").click(function(){ $("#" + tabID + " [name=hidSaveAndProceed]").val(0) ; });   
     
    $("#" + tabID + " .remove-button").click(function() {removeDetailRows(this);});   
    
    
    var groupName = '.transaction-detail';
    var newRowClass = 'transaction-detail-row';
    $("#" + tabID + " .add-row-button").unbind('click').bind( "click", function(event) {    
        // sementara parameter keempat, obj.rebindEl , diset null karena adanya di function baru 
        $row = addNewTemplateRow($(this).attr("attr-template"),null,$(this).closest(groupName),null,$(this).closest("." + newRowClass)); 
        if (typeof getTabObj() !== 'undefined' && $.isFunction(getTabObj().afterAddNewTemplateRowHandler))  
            getTabObj().afterAddNewTemplateRowHandler($row) 
    });

    
    
    
    $("#" + tabID + " .input-integer").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),0); });
    $("#" + tabID + " .inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
    $("#" + tabID + " .inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),2); });
    $("#" + tabID).find(".inputnumber, .inputdecimal, .input-integer").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
    
    $("#" + tabID + " .input-date" ).datepicker({ showButtonPanel: true, 
                                                  currentText: 'Now', 
                                                  dateFormat:'dd / mm / yy', 
                                                  changeMonth: true,  
                                                  changeYear: true,
                                                  beforeShow : function(input, inst) {  
                                                            inst.dpDiv.removeClass('month-year-datepicker');
                                                        }
                                                    }
                                               );
    
    $("#" + tabID + " .input-datetime" ).datetimepicker({ currentText: 'Now', dateFormat:'dd / mm / yy', changeMonth: true,  changeYear: true}); 
    $("#" + tabID + " .input-month").datepicker({
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

    $("#" + tabID ).find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
   
    $("#" + tabID + " .mnv-barcode-input").keydown(function (e) {barcodeHandler(this,e); });
    updateRowNumber($("#" + tabID + " .transaction-detail")); 
      
    $(".mnv-total-group-detail").hide(); //kalo langsung hide dr css, stylenya rusak
    $(".mnv-total-field").focus(function() {  
      //$(this).removeCLass("readonly-enable-style");
      $(this).closest("div").find(".collapsible-icon").hide();
      $(this).closest(".mnv-total-group").find(".mnv-total-group-detail").slideDown();
    });
      
    $(".mnv-total-group-hide-detail").click(function() {  
       var objGroup =  $(this).closest(".mnv-total-group");
       //objGroup.find(".mnv-total-field").addCLass("readonly-enable-style");
       objGroup.find(".mnv-total-group-detail").slideUp();
       objGroup.find(".collapsible-icon").show();
    });
    
    // update max days
    $("#" + tabID ).find(".input-date, .input-datetime").each(function() {     
        var maxDays = $(this).attr("max-days") || '';
        var minDays = $(this).attr("min-days") || '';
        if(maxDays != '') $(this).datepicker('option', 'maxDate', "+"+maxDays+"D" );
        if(minDays != '') $(this).datepicker('option', 'minDate', "+"+minDays+"D" );
    });
      
    
    // kalo ad input file
    // cek ad template utk input-file tdk, kalo ad dan masih blm ad row, maka add new
    var fileRowTemplateClass = 'file-row-template';
    var inputFileObj =  $("#" + tabID ).find('.'+fileRowTemplateClass);
    if(inputFileObj.length > 0) {
        var inputFileRow = inputFileObj.first().closest(".file-upload-detail").find(".transaction-detail-row");
        if (inputFileRow.length == 0)
            addNewTemplateRow(fileRowTemplateClass); 
    }
    
    updateStickyStyle();
    bindFormPaging(tabID);
    loadMoreHistory(tabID);
}

function prepareHandler(obj){
    var tabID = obj.tabID;  
    var tabObj = $("#" + tabID);
    
    var groupName = '.transaction-detail';
    var newRowClass = 'transaction-detail-row';
    
    if (!tabParam[tabID])
        tabParam[tabID] =  { href:"", tabID: tabID};
     
    tabParam[tabID].obj = obj;
    
    tabObj.find("#defaultForm").attr("id","defaultForm-"+tabID); 
 
    if (!tabObj.find("[name=btnSave]").is(":visible")){ 
        disableFormSaveOnEnter($("#defaultForm-"+tabID));  
    }
    
    tabObj.find("[name=btnSaveAndProceed]").click(function(){ tabObj.find("[name=hidSaveAndProceed]").val(1) ; });   
    tabObj.find("[name=btnSave]").click(function(){ tabObj.find("[name=hidSaveAndProceed]").val(0) ; });   
     
    tabObj.find(".remove-button").click(function() {
        removeDetailRows(this); 
        
        if (getTabObj() && $.isFunction(getTabObj().afterRemoveRowHandler))  
            getTabObj().afterRemoveRowHandler()  
    });   

    tabObj.find('.add-row-button').unbind('click').bind( "click", function(event) {    
        $row = addNewTemplateRow($(this).attr("attr-template"),null,$(this).closest(groupName),obj.rebindEl,$(this).closest("." + newRowClass) ); 
        if ($.isFunction(getTabObj().afterAddNewTemplateRowHandler))  
            getTabObj().afterAddNewTemplateRowHandler($row) 
    });

    
    tabObj.find(".input-integer").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),0); });
    tabObj.find(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this)); });
    tabObj.find(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),2); });
    tabObj.find(".inputautodecimal").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur($(this),-2); });
    
    tabObj.find(".inputnumber, .inputdecimal, .input-integer, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
    
    tabObj.find(".input-date").datepicker({ 
                                                showButtonPanel: true, 
                                                currentText: 'Now', 
                                                dateFormat:'dd / mm / yy', 
                                                changeMonth: true,  
                                                changeYear: true,
                                                beforeShow : function(input, inst) {  
                                                        inst.dpDiv.removeClass('month-year-datepicker');
                                                    }
                                            }).keyup(function(e) { 
        var allowEmpty = $(this).attr("attr-allow-empty") ;
        if (!allowEmpty) return;
         
        if(e.keyCode == 8 || e.keyCode == 46) {
            $.datepicker._clearDate(this);
        }
    }); 
    
    tabObj.find(".input-datetime").datetimepicker({ currentText: 'Now', dateFormat:'dd / mm / yy', changeMonth: true,  changeYear: true})
    .keyup(function(e) { 
        var allowEmpty = $(this).attr("attr-allow-empty");
        if (!allowEmpty) return;
         
        if(e.keyCode == 8 || e.keyCode == 46) {
            $(this).val("");
            //$.datepicker._setTimeDatepicker(this,'00-00-0000','00:00');
        }
    });  
    
    // update max days
    tabObj.find(".input-date, .input-datetime").each(function() {     
        var maxDays = $(this).attr("max-days") || '';
        var minDays = $(this).attr("min-days") || '';
        if(maxDays != '') $(this).datepicker('option', 'maxDate', "+"+maxDays+"D" );
        if(minDays != '') $(this).datepicker('option', 'minDate', "+"+minDays+"D" );
    });
    
    tabObj.find(".input-date").change(function() {   
             
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
    
        
    // $('#defaultForm-' + tabID+ ' .input-date').datepicker('option', 'maxDate', "+14D" );
    
    
    tabObj.find('.input-month').datepicker({
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
 
    tabObj.find(".multi-selectbox:not(:disabled)").searchableOptionList({  maxHeight: '250px',  showSelectAll: true, showSelectionBelowList: true  }); 
    
    tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
    
    tabObj.find(".mnv-barcode-input").keydown(function (e) {barcodeHandler(this,e); });
    updateRowNumber(tabObj.find(".transaction-detail")); 
    
    // DETAIL CLONE
    var btnAddRow = tabObj.find("[name=btnAddRows]"); 
    btnAddRow.on('click', function() { 
        var rowQty = parseInt(btnAddRow.closest(".add-row-panel").find('.mnv-new-row-qty').val()) || 1; 
        for(i=0;i<rowQty;i++)
            addNewTemplateRow("detail-row-template",null,null,obj.rebindEl); 
    });
 
    if (btnAddRow.length == 0) { 
        tabObj.find('.mnv-new-row-qty').remove(); // gk bisa pake btnAddRow.closest(".add-row-panel"). karena posisinya btnAddRow nya gk ada
    }
        
    // if has transaction table
	// search by group
    // utk file upload, akan masalah kalo ad didalam transaksi row jg
    
    var tableTransactionDetail = tabObj.find(".mnv-transaction"); 
	tableTransactionDetail.each(function(i) {
	  var mnvGroup = $(this);
      var transactionDetailRowObj = mnvGroup.find(".transaction-detail-row");
	  if(transactionDetailRowObj.length == 0) { 
		   
          if(tableTransactionDetail.find(".detail-row-template").length > 0)
            addNewTemplateRow("detail-row-template",null,mnvGroup,obj.rebindEl);
           
          //if(tableTransactionDetail.find(".file-row-template").length > 0)
          //  addNewTemplateRow("file-row-template",null,mnvGroup);
          
      }
	});
//	
//	for(i=0;i<totalGroup;i++){
//		mnvGroup = tableTransactionDetail[i];
//		 if(mnvGroup.find(".transaction-detail-row").length == 0) 
//		    addNewTemplateRow("detail-row-template",null,mnvGroup,obj.rebindEl);
//	}
		
//	console.log("test")
//	console.log( tableTransactionDetail.length );
//	console.log( tableTransactionDetail.find(".transaction-detail-row").length  );

//    if( tableTransactionDetail.length != 0 && tableTransactionDetail.find(".transaction-detail-row").length == 0) 
//        addNewTemplateRow("detail-row-template",null,null,obj.rebindEl);
//    
    // if has payment table
    var tablePaymentDetail = tabObj.find(".mnv-payment-method");
    //if( tablePaymentDetail.length != 0 && tablePaymentDetail.find(".transaction-detail-row").length == 0) 
     
    // selalu add new row, karena gk ada tombol add nya
    addNewTemplateRow("payment-method-row-template");
       
    //diletakan diluar agar kehandle jg ketika edit form 
    bindEl(tablePaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(obj,tablePaymentDetail); });
    bindEl(tablePaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(obj,tablePaymentDetail); });
 
    tabObj.find('.mnv-currency .input-currency').on('change',function() { onChangeCurrency(this); });
    tabObj.find('.mnv-currency .input-currency').change();
       
    tabObj.find(".mnv-total-group-detail").hide(); //kalo langsung hide dr css, stylenya rusak
    tabObj.find(".mnv-total-field").focus(function() {   
      $(this).closest("div").find(".collapsible-icon").hide();
      $(this).closest(".mnv-total-group").find(".mnv-total-group-detail").slideDown();
    });
      
    tabObj.find(".mnv-total-group-hide-detail").click(function() {  
       var objGroup =  $(this).closest(".mnv-total-group"); 
       objGroup.find(".mnv-total-group-detail").slideUp();
       objGroup.find(".collapsible-icon").show();
    });
    
    tabObj.find('.mnv-new-row-qty').on('change',function() { 
        var qty = parseInt($(this).val()) || 1;
        if (qty<1) 
            qty = 1;
        else if (qty>10)
            qty = 10;
        
        $(this).val(qty).blur();
        
    });
    tabObj.find('.mnv-new-row-qty').change();
    

    // enter to next
    // gk bisa, tab focus nya ngaco
    
//    tabObj.find(".form-control").bind('keypress',function(e) {
//     
//	  var code = e.keyCode || e.which;
//	  if (code == 13  && !$(e.target).is("textarea")) { 
//		  e.preventDefault();
//          
//            var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
//            focusable = form.find('input, button').filter(':visible');
//            next = focusable.eq(focusable.index(this)+1);
//            if (next.length) {
//                next.focus();
//            }
//            return false;
//           
//	  }
//	});
    
    // kalo ad input file
    // cek ad template utk input-file tdk, kalo ad dan masih blm ad row, maka add new
    var fileRowTemplateClass = 'file-row-template';
    var inputFileObj = tabObj.find('.'+fileRowTemplateClass);
    
    inputFileObj.each(function(i) {
	  var mnvGroup = $(this).closest('.div-table');
        
        var inputFileRow = mnvGroup.closest(".file-upload-detail").find(".transaction-detail-row");
        if (inputFileRow.length == 0)
            addNewTemplateRow(fileRowTemplateClass,null,mnvGroup); 
    });
                                //
    //if(inputFileObj.length > 0) {
        //var inputFileRow = inputFileObj.first().closest(".file-upload-detail").find(".transaction-detail-row");
        //if (inputFileRow.length == 0)
            //addNewTemplateRow(fileRowTemplateClass,null); 
    //}
     
    
    bindArrowNav(tabObj.find('.transaction-detail-row')); // parameternya detail-row
    
    updateControlLabelPadding();
    removeButtonPanelIfEmpty(tabID);
    updateStickyStyle();
    bindFormPaging(tabID);
    loadMoreHistory(tabID);
    
    obj.loadOnReady();
}

function loadMoreHistory(tabID){
    $("#"+tabID+" .history-link").on("click", function(){
        var pkey = $("#"+tabID+" [name=hidId]").val();
        var year = parseInt($(this).attr("rel-year"));
        var tablekey = parseInt($(this).attr("rel-tablekey"));
        $(this).remove();
        
        $.ajax({
                type: "GET",
                url:  'ajax-history.php',
                data: "action=getRowHistory&id="+pkey+"&year=" + year+"&tablekey=" + tablekey ,  
                success: function(data){  
                    if(!data) return;  
                     $("#"+tabID+" .history-table").append(data); 
                } 
        }).done(function( data ) {   
                     
        }); 
    }); 
}

function removeButtonPanelIfEmpty(tabID){ 
    var objLength = $("#"+tabID+" .form-button-panel button, #"+tabID+" .form-button-panel .next-page, #"+tabID+" .form-button-panel .prev-page").length;
    if(objLength == 0 ) $("#"+tabID+" .form-button-panel").hide(); 
}

function bindFormPaging(tabID){
     $(".next-page, .prev-page").on("click", function(){
        $("#"+tabID+" .form-button-panel button").attr("disabled",true);
        $("#"+tabID+" .next-page").unbind("click").addClass("next-page-muted").removeClass("next-page");
        $("#"+tabID+" .prev-page").unbind("click").addClass("prev-page-muted").removeClass("prev-page"); 
        
        var href = tabParam[tabID]['href']; 
        var currentPkey = $("[name=hidId]").val() || 0;
        var nextPkey = $(this).attr("attr-pkey");
            
        // kalo add harusnya gk ad tombol ini juga
        if (currentPkey == 0) return;
        
        // load next page 
        href = href.replace("id="+currentPkey, "id="+nextPkey);
        tabParam[tabID]['href'] = href; //overwrite 
        
        $("#"+tabID).load(encodeURI(href)); // URL gk boleh ad spasi. tp kalo pake urlencode, jd error
    });
}

function onChangePaymentMethodHandler(obj,tablePaymentDetail, templateRow){  
  
    var detailField = tablePaymentDetail.find('.mnv-detail-field');
    
    //hitung total payment
    var amount = 0;
    detailField.each(function() {    
         amount += parseFloat(unformatCurrency($(this).val())) || 0;
    })    
      
    tablePaymentDetail.find('.mnv-total-field').val(amount).blur();
       
    if (!templateRow)
        templateRow = "payment-method-row-template";
              
    var newRow = autoAddNewRowTemplate(detailField,templateRow); 
    if(newRow){ 
        bindEl(newRow.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(obj,tablePaymentDetail,templateRow); });
        bindEl(newRow.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(obj,tablePaymentDetail,templateRow); });
    }
    
    if(obj.onChangePaymentMethodHandler)
        obj.onChangePaymentMethodHandler();
     
}

function setFormValidation(obj, form,fieldValidation,submitParam){
 
        form.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
                },
                fields:  fieldValidation
        })
        .on('success.form.bv', function(e) {  
            
            submitForm( e,
              {tabID : obj.tabID },
              {parentPanelId : submitParam.parentPanelId , parentTitle : submitParam.parentTitle, autoPrintURL : submitParam.autoPrintURL  }, 
              {value: submitParam.value, valueDBField : submitParam.valueDBField,  key: submitParam.key, revalidateField:  submitParam.revalidateField }
             ); 
        });
		  
}

function autoAddNewRowTemplate(obj,templateRow, autoComplete){  
     
    var hasEmptyValue = false;
    obj.each(function() {    
        
            // harusnya otomatis paling bawah
            if($(this).closest(".div-table-row").hasClass(templateRow))
                return false; 
        
            value = parseInt(unformatCurrency($(this).val())) || 0;
   
            if(value == 0){ 
                hasEmptyValue = true;
                return false;   
            } 
    })   
    
    if (!hasEmptyValue) { 
        var newRow = addNewTemplateRow(templateRow);  
        if(autoComplete) 
            bindAutoCompleteForTransactionDetail(autoComplete.object, autoComplete.objAndvalueDetailForAutoComplete, autoComplete.url); 
        
        return newRow;
    }
}

function bindEl(obj,action,func){  
    obj.unbind(action).on(action, func);   
}

function bindSelectableDataRecord(obj){
     
    if(isMobile()) return;
    
    obj.find(".selectable").selectable({
         filter : "li",	
         cancel: ".unselectable, .data-card",  
         stop: function() {       
            resetSelectedRows();

            $( ".ui-selected", this ).each(function() { 
                selectMultiRows($(this).closest(".data-record")); 
            });
          }
     })     
}

function setAutoComplete(tabID, autoCompleteObj, clearOnNotFound){
     
        var objName = autoCompleteObj.objName;
        var objValue = autoCompleteObj.objValue;
        var url = autoCompleteObj.url;  
    
        if (clearOnNotFound == undefined)
            clearOnNotFound = true;
     
        $( "#" + tabID + " [name="+objName+"]" ).autocomplete({
		  source: url, 
		  minLength: 1,
          autoFocus: true,
		  select: function( event, ui ) {       
		   		$("#"+ tabID + " [name="+objValue+"]" ).val(ui.item.pkey); 
               
               /* var TABKEY = 9;
                this.value = ui.item.value;
 
                if (event.keyCode == TABKEY) { 
                    alert("sd")
                    event.preventDefault();
                    this.value = this.value + " ";
                   // $('#search').focus();
                }

                return false; */
              
			},  
		  search: function( event, ui ) { },
		  change: function( event, ui ) { 
		  		 if (clearOnNotFound && ui.item == null) 
					clearAutoCompleteInput(this,objValue);
				 
			},
		}).change(function() {
		   if (clearOnNotFound && $(this).val() == "") 
					clearAutoCompleteInput(this,objValue); 
		}); 
}

function getSelectedTabIndex() { 
    return $("#tabs").tabs('option', 'active');
}

function findTabIndexByTitle(title){
	 var num_tabs = 0;
	 var foundNeedle = false;
	 	
	 $('#tabs ul li a').each(function(i) {    
			  if (this.text.localeCompare(title) == 0) {   
			  		 foundNeedle = true;  
					 return false;                                                                              
			  }
			  num_tabs++;
	  });
	   
	  if (foundNeedle)
	  	return num_tabs;
	  else 
	   return -1;
}

// actual addTab function: adds new tab using the title input from the form above
function addTab(title,href,hasCloseButton,selectOnExist) {  
	
    title = decodeURI(title);
    
	//cek dulu sudah ad blm tabnya, kalo sudah ad select aj 
	 tabNameExists = false;
 	 var num_tabs = -1; 
    
    if(selectOnExist) 
	  num_tabs = findTabIndexByTitle(title);
     
	 if(num_tabs != -1 ){ 
		  $tabs.tabs( "option", "active", num_tabs );   
          adjustActionBarPosition();
	 } else {
		
        var closeButton = "";
        if (hasCloseButton == undefined || hasCloseButton == true)
            closeButton = "<span class=\"ui-icon ui-icon-close\" role\"presentation\"></span>";
          
		$( "<li attr-href=\""+href+"\"><a href='" + href + "'>" + title + "</a>" + closeButton + "</li>" ).appendTo( "#tabs .ui-tabs-nav" );
	 	 
        $tabs.tabs( "refresh" );   
		$tabs.tabs( "option", "active",  $("#tabs ul:first li").length -1 ); 
         
		if(typeof selectedTab !== 'undefined') 
			tabParam[selectedTab.newPanel[0].id] = { title: title, href:href , phpDataListFile:"",addDataFile:"", selectedPkey: [],quickView:[], lastRowIndex : 0, filterCriteria:[], tagInformation:[], selectedCriteriaTagKey:[],orderby:"",ordertype:"", isTransaction:false, tablekey: 0};  
	  }
    
}   

function openTabForShortCutAdd(href,opt){   

    var selectedPkey = tabParam[selectedTab.newPanel[0].id].selectedPkey;
    
    
    title = decodeURI(opt.title);
    
	//cek dulu sudah ad blm tabnya, kalo sudah ad select aj 
	 tabNameExists = false;
 	 var num_tabs = -1; 
    
//    if(selectOnExist) 
//	  num_tabs = findTabIndexByTitle(title);
     
	 if(num_tabs != -1 ){ 
		  $tabs.tabs( "option", "active", num_tabs );   
          adjustActionBarPosition();
	 } else {
		
        var closeButton = "";
        if (typeof hasCloseButton === 'undefined' || hasCloseButton == true)
            closeButton = "<span class=\"ui-icon ui-icon-close\" role\"presentation\"></span>";
           
        href += (href.indexOf('?') == -1)  ? '?' :  '&'; 
        href += 'shortcut=1&refkey='+selectedPkey[0];
         
		$( "<li attr-href=\""+href+"\"><a href='" + href + "'>" + title + "</a>" + closeButton + "</li>" ).appendTo( "#tabs .ui-tabs-nav" );
	 	 
        $tabs.tabs( "refresh" );   
		$tabs.tabs( "option", "active",  $("#tabs ul:first li").length -1 ); 
         
		tabParam[selectedTab.newPanel[0].id] = { title: title, href:href , phpDataListFile:"",addDataFile:"", selectedPkey: [],quickView:[], lastRowIndex : 0, filterCriteria:[], tagInformation:[], selectedCriteriaTagKey:[],orderby:"",ordertype:"", isTransaction:false, tablekey: 0};  
	  }
}


function openTabForShortCutEdit(href,pkey,opt){   

    var selectedPkey = tabParam[selectedTab.newPanel[0].id].selectedPkey;
     
    title = decodeURI(opt.title);
    
	//cek dulu sudah ad blm tabnya, kalo sudah ad select aj 
	 tabNameExists = false;
 	 var num_tabs = -1; 
     
	 if(num_tabs != -1 ){ 
		  $tabs.tabs( "option", "active", num_tabs );   
          adjustActionBarPosition();
	 } else {
		
        var closeButton = "";
        if (typeof hasCloseButton === 'undefined' || hasCloseButton == true)
            closeButton = "<span class=\"ui-icon ui-icon-close\" role\"presentation\"></span>";
           
        href += (href.indexOf('?') == -1)  ? '?' :  '&'; 
        href += 'id='+pkey;
         
		$( "<li attr-href=\""+href+"\"><a href='" + href + "'>" + title + "</a>" + closeButton + "</li>" ).appendTo( "#tabs .ui-tabs-nav" );
	 	 
        $tabs.tabs( "refresh" );   
		$tabs.tabs( "option", "active",  $("#tabs ul:first li").length -1 ); 
         
		tabParam[selectedTab.newPanel[0].id] = { title: title, href:href , phpDataListFile:"",addDataFile:"", selectedPkey: [],quickView:[], lastRowIndex : 0, filterCriteria:[], tagInformation:[], selectedCriteriaTagKey:[],orderby:"",ordertype:"", isTransaction:false, tablekey: 0};  
	  }
}


function openTabForEdit(){  
    var selectedPkey = tabParam[selectedTab.newPanel[0].id].selectedPkey;
	   
    if (selectedPkey.length == 0){
		showMsgDialog ("Anda belum memilih data yang hendak diubah.");
		return ;
	} 
    
    dataPkeyCol = JSON.stringify(selectedPkey); // utk jaga compability
    dataPkey = 	selectedPkey[0];
	
	var title = selectedTab.newTab[0].textContent;
	var selectedTabId = selectedTab.newPanel[0].id; 
	var phpDataListFile = tabParam[selectedTabId].phpDataListFile; 
	var addDataFile = tabParam[selectedTabId].addDataFile;
    
    if (addDataFile.indexOf('?') == -1) 
        addDataFile += '?';
    else
        addDataFile += '&';
     
	var href = addDataFile + "title=" + title + "&id=" + dataPkey + "&idcol=" + dataPkeyCol + "&fileName=" + phpDataListFile + "&selectedPanelId="+selectedTabId;
     
    // udah gk kepake, kalo mau dipake lg, ad error kalo edit beberapa data
    //$("#"+selectedTabId+" [relid="+dataPkey+"]").find(".unread-status").removeClass("unread-status");
    
	addTab("<i class=\"far fa-edit title-icon\"></i>" + title ,href);  
}

function bindAutoCompleteForTransactionDetail(targetObj,objAndValue,searchFile,handlingFunction){ 
 
	// untuk form yang berdiri sendiri diluar admin/list.php
 
    if(typeof selectedTab !== 'undefined') { 
        var objTarget = $("#" + selectedTab.newPanel[0].id + " [name='" + targetObj + "']");
    } else { 
        var objTarget = $("[name='" + targetObj + "']");
    }
	      
    var onSelectFunction = '';
    var onChangeFunction = '';
    
    if ($.isArray(handlingFunction)){
        onSelectFunction = handlingFunction.onSelectFunction;
        onChangeFunction = handlingFunction.onChangeFunction;
    }else{
        onSelectFunction = handlingFunction;
    }
    
	objTarget.autocomplete({
	  source: searchFile,
      autoFocus: true,
	  minLength: 1,  
      open: function(event, ui) { 
            if($(this).attr("isbarcode") == 1){  
                $(this).attr("isbarcode",0);
                $(this).data("ui-autocomplete").menu.element.children().first().click();
                $(this).closest("div").next().find("input").focus();
            } 
      },       
      response: function( event, ui ) {
         if (ui.content.length == 0)
               $(this).attr("isbarcode",0); // biar gk autoselect terus
      },  
	  select: function( event, ui ) {     
           
            //var nextInput = $(this).closest("div").next().find("input");
          
			if (onSelectFunction != undefined && onSelectFunction != ""){
                
                //maintain compability
                if (jQuery.type(onSelectFunction) == 'string')
				    eval(onSelectFunction+"(this,objAndValue,ui)"); 
                else 
                    onSelectFunction(this,objAndValue,ui);
                
                event.preventDefault(); // ini diperlukan agar kita bisa ganti value yg terpilih selain pake nilai 'value'
			}else{ 
                var tableRow = $(this).closest(".transaction-detail-row"); 
				if(!tableRow || tableRow.length <= 0) tableRow = $(this).closest(".div-table-row");  //utk compability
                
                for(i=0;i<objAndValue.length;i++){  
                    var elObj = tableRow.find("[name='" + objAndValue[i].object +"']").first();
                    var elVal = ui.item[objAndValue[i].value];
                     
                    if (objAndValue[i].type == "date")
                       elVal = moment(elVal).format(_DATE_FORMAT_);
                    
                    elObj.val(elVal).change();//.blur();   // gk boleh ad blur, kalo ad blur, change dibawah gk jalan, terus kalo utk number gmana ??
                    //console.log(objAndValue[i].object + " >> " + $(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").val());
                    
				}
                // asumsi nilai field index biasanya bukan angka

                tableRow.find(".inputnumber, .inputdecimal, .inputautodecimal").blur(); 
			}   
          
            // kalo focus next, gk sempet keupdate sepertinya, sudah pindah duluan
            //nextInput.focus();
		},  
	  search: function( event, ui ) { 	  
            
//          if (ui.item == null)  
//            $(this).attr("isbarcode",0); // biar gk autoselect terus kalo gk nemu 
          
	  			/*for(i=0;i<objAndValue.length;i++){
	  				$(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").first().val("").blur(); 
				}*/ 
	  },
	  change: function( event, ui ) {     
            var nextInput = $(this).closest("div").next().find("input");
          
            var chkPick = $(this).closest(".div-table-row").find("[name='chkPick[]']");
            //if(chkPick.length == 0) return; //klao gk ad checkbox, gk perlu lanjut
          
			if (ui.item == null) {  
              
                chkPick.prop("checked", false);
                
				for(i=0;i<objAndValue.length;i++){  
					$(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").first().val("").blur();
				}
                  
				$(this).val("").change();
                
			} else{  
				chkPick.prop("checked", true);
            }
          
            // gk bisa taro di change, karena ad delay nanti terlihat
            //$(this).closest(".div-table-row").find(".inputnumber, .inputdecimal, .inputautodecimal").blur();
            chkPick.change();
			if (onChangeFunction != undefined && onChangeFunction != "") 
                eval(onChangeFunction+"(this,objAndValue,ui)");
          
          
            nextInput.focus();
		},
	});
   
    // kalo ad barcode 
    // kalo blur, dan hidKey kosong, query ulang
    // harus insert handlingFunction yg dikirim jg 
    
    
    // perlu reset value dulu
/*    
    objTarget.on('keypress',function(e) {
        
        var keyCode = (e.which) ? e.which : e.keyCode;
        if(e.which == 13) {
             $.ajax({
                type: "GET",
                url:  searchFile,
                data: "searchData" + id ,  
                success: function(data){ 
                     if(!data) return;
                     
                    for(i=0;i<objAndValue.length;i++){  
                        $(this).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").first().val("").blur();
                    }

                        // targetContent.html(data);
                } 
            }).done(function( data ) {   
                    
                 
            });
        }
        
    });*/

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

function clearAutoCompleteKey(hidKeyName){    
    if (jQuery.type(hidKeyName) == 'string')
	   $(obj).closest('form').find("[name='"+hidKeyName+"']").first().val(""); 
    else
        hidKeyName.val("");  
}

function updateTagStatusPanel(){
    
	var tagInformation = tabParam[selectedTab.newPanel[0].id].tagInformation; 
    if (!tagInformation) return;
    
	var tagHTML = '';
    var allSelected = 'checked="checked"';
	 
    for(i=0;i<tagInformation.length;i++){
		tagHTML +=  '<div class="div-table-row tag-list">'; 
		//tagHTML +=  '<div class="div-table-row">'; 
		
		var checked = '';
		var selectedClass ='';
		var selectedStyle ='';
        
		if(jQuery.inArray( parseInt(tagInformation[i].tagPkey) , tabParam[selectedTab.newPanel[0].id].selectedCriteriaTagKey ) >= 0){
			checked = 'checked="checked"'; 
			selectedClass = "text-white";
			selectedStyle = 'background-color:' + tagInformation[i].hexColor;
		}else{
            allSelected = '';
        }
		  
//		tagHTML +=  '<div class="div-table-col div-table-col-header" ><label class="'+selectedClass+'" style="'+selectedStyle+'"><span style="margin-top:0.5em"><input name="chk-group-tag-filter[]"  type="checkbox" ' + checked + ' class="chk-group-filter" group="selectedCriteriaTagKey" value="' + tagInformation[i].tagPkey + '"></span>' + tagInformation[i].tagName + '<div class="total-status" style="background-color:' + tagInformation[i].hexColor + '">' + tagInformation[i].totalData + '</div></label></div>';
//		tagHTML +=  '</div>';  
        
		tagHTML +=  '<div class="div-table-col div-table-col-header" >';
		tagHTML +=  '<label class="'+selectedClass+'" style="'+selectedStyle+'">';
		tagHTML +=  '<div class="flex" style="align-items: flex-start">';
			tagHTML +=  '<div style="padding-top:0.1em"><input name="chk-group-tag-filter[]"  type="checkbox" ' + checked + ' class="chk-group-filter" group="selectedCriteriaTagKey" value="' + tagInformation[i].tagPkey + '" ></div>';
			tagHTML +=  '<div class="consume">'+tagInformation[i].tagName  + '</div>';
			tagHTML +=  '<div class="total-status bg-blue-steel" style="background-color:' + tagInformation[i].hexColor + ' !important">' + tagInformation[i].totalData + '</div>';
		tagHTML +=  '</div>';
		tagHTML +=  '</label>';  
		tagHTML +=  '</div>';  
		tagHTML +=  '</div>';  


        // edit
        tagHTML +=  '<div  style="display:none;" class="div-table-row tag-input">'; 
        tagHTML +=  '<div class="div-table-col" style="padding 0.3em 0"><div  style="padding:0.5em; border-radius:0.5em; background-color:' + tagInformation[i].hexColor + '"><input name="name[]" class="form-control input-01" autocomplete="off" value="'+tagInformation[i].tagName+'" type="text" ></div><input name="hidTagDetailKey[]" value="' + tagInformation[i].pkey + '" type="hidden"></div>';
        tagHTML +=  '</div>';  
         
	}  
	
 	tagHTML +=  '</div>';  
    tagHTML =  '<div id="tag-filter" class="div-table-status-information" ><div class="div-table-caption"><input name="chk-group-filter-tag-master[]"  type="checkbox"  class="chk-group-filter"  '+allSelected+' ></input> ' + phpLang.tag.toUpperCase() + ' <div class="manage-button edit-tag-button" style="float:right" onClick="editTagList(this)">'+ phpLang.edit.toLowerCase()+'</div><div class="manage-button save-tag-button" style="float:right; display:none" onClick="saveTagList(this)">'+ phpLang.save.toLowerCase()+'</div></div>' + tagHTML;
	tagHTML = '<form><input name="action" value="update" type="hidden">'+tagHTML+'</form>'
	
    return tagHTML;
	//$("#tag-status-panel").html(tagHTML); 
}

function updateStatusPanel(){ 
    
	$("#left-status-panel").html("");
 	   
	if (tabParam[selectedTab.newPanel[0].id] == undefined ||tabParam[selectedTab.newPanel[0].id].phpDataListFile == "" ){ 
		return;	
	}
    
    var filterCriteria = tabParam[selectedTab.newPanel[0].id].filterCriteria;
    
    if(filterCriteria){ 

            for (var k=0;k<filterCriteria.length;k++){
                var options = filterCriteria[k];   

                var title = options['title'];
                var statusInformation = options['statusInformation'];
                var criteriaKey = options['selectedCriteriaKey'];
                var groupFilter = options['group'];

                var statusHTML = ''; 
                var totalStatus = 0;
                var allSelected = 'checked="checked"';

                for(i=0;i<statusInformation.length;i++){  
                        
                    statusHTML +=  '<div class="div-table-row" >'; 

                    var checked = '';
                    var selectedClass ='';
 
                    if(criteriaKey && jQuery.inArray( parseInt(statusInformation[i].pkey), criteriaKey ) >= 0){
                        checked = 'checked="checked"';
                        selectedClass = "selected";
                    }else{
                        allSelected = '';
                    }

                    statusHTML +=  '<div class="div-table-col div-table-col-header" >';
                        statusHTML +=  '<label class="'+selectedClass+'">';
                        statusHTML +=  '<div class="flex" style="align-items: flex-start">';
                            statusHTML +=  '<div style="padding-top:0.1em"><input name="chk-group-filter[]"  type="checkbox" ' + checked + ' class="chk-group-filter" group="'+k+'" value="' + statusInformation[i].pkey + '" ></div>';
                            statusHTML +=  '<div class="consume">'+statusInformation[i].name + '</div>';
                            statusHTML +=  '<div class="total-status bg-blue-steel">' + statusInformation[i].totalData + '</div>';
                        statusHTML +=  '</div>';
                    statusHTML +=  '</label>';
                    statusHTML +=  '</div>';
                    statusHTML +=  '</div>';
                    totalStatus += parseInt(statusInformation[i].totalData) ;
                }  
                statusHTML +=  '</div>';

                statusHTML = '<div class="div-table-status-information " style="margin-bottom:2em;"><div class="div-table-caption"><input name="chk-group-filter-master[]"  type="checkbox" class="chk-group-filter" '+allSelected+' group="'+k+'" ></input> ' + title.toUpperCase () + '</div>' + statusHTML;


                var totalStatusLabel = (k==0) ?'<div class="div-table-status-information" style="margin-bottom:1em; font-weight:bold;" ><div class="div-table-row " ><div class="div-table-col" style="color:#333;  " ><span class="panel-title">' + phpLang.totalData.toUpperCase () + '</span><div class="total-status" style="color:#333;" >' + totalStatus + '</div></div></div></div>' : '';
                statusHTML = totalStatusLabel + statusHTML; 

                $("#left-status-panel").append(statusHTML);
            }
    }
        
	  
    $("#left-status-panel").append(updateTagStatusPanel());
    
	$("#left-status-panel .total-status").formatCurrency({roundToDecimalPlace: 0 });

    $("#left-status-panel [name='chk-group-filter[]']").click(function() {    
            var keyIndex = $(this).attr("group");  
          
		    var selectedKey = Array(); 
			$(this).closest(".div-table-status-information").find("[name='chk-group-filter[]']").each(function() {  
				 if ($(this).prop("checked") == true)
				 	selectedKey.push( parseInt($(this).val()) );
			});
			 
			tabParam[selectedTab.newPanel[0].id]['filterCriteria'][keyIndex]['selectedCriteriaKey'] = selectedKey; 
           
			updateData(false);
	});
     
    $("#left-status-panel [name='chk-group-tag-filter[]']").click(function() {    
            //var keyIndex = $(this).attr("group");  
          
		    var selectedKey = Array(); 
			$(this).closest(".div-table-status-information").find("[name='chk-group-tag-filter[]']").each(function() {  
				 if ($(this).prop("checked") == true)
				 	selectedKey.push( parseInt($(this).val()) );
			});
			   
			tabParam[selectedTab.newPanel[0].id]['selectedCriteriaTagKey'] = selectedKey; 
          
			updateData(false);
	});
    
    ////// chk master handler
    $("#left-status-panel [name='chk-group-filter-master[]']").change(function() {    
            
            var keyIndex = $(this).attr("group");  
            var checked = $(this).prop("checked");
                
		    var selectedKey = Array(); 
			$(this).closest(".div-table-status-information").find("[name='chk-group-filter[]']").each(function() {  
                 $(this).prop("checked",checked);
                
                 if(checked)
				    selectedKey.push( parseInt($(this).val()) );
			});
			   
			tabParam[selectedTab.newPanel[0].id]['filterCriteria'][keyIndex]['selectedCriteriaKey'] = selectedKey; 
          
			updateData(false);
	});
     
    $("#left-status-panel [name='chk-group-filter-tag-master[]']").change(function() {    
            
            //var keyIndex = $(this).attr("group");  
            var checked = $(this).prop("checked");
                
		    var selectedKey = Array(); 
			$(this).closest(".div-table-status-information").find("[name='chk-group-tag-filter[]']").each(function() {  
                 $(this).prop("checked",checked);
                
                 if(checked)
				    selectedKey.push( parseInt($(this).val()) );
			});
			   
			tabParam[selectedTab.newPanel[0].id]['selectedCriteriaTagKey'] = selectedKey; 
          
			updateData(false);
	});
     
     
}

function unformatCurrency(value){ 
	if (value == undefined)
		return 0;
		
	return value.replace(/,/g,"");
}	  	
		
function collapseAllMenu(){
	$("#menu ul li").each(function(index) {  
	   $(this).removeClass('active').addClass('inactive');
	}); 
	
	$(".submenu").each(function(index) {  
	   $(this).hide();
	}); 
}

function selectAllRows(){   
    var rowClass = (isMobile()) ? '.mobile-selectable' : '.selectable';
    
	$("#" + selectedTab.newPanel[0].id + " "+ rowClass +" li").addClass("ui-selected"); 
	$("#" + selectedTab.newPanel[0].id + " .data-record [name='chkRow[]']").prop("checked",true);
    
     
	var selectedPkey = Array();
	$( ".ui-selected", "#" + selectedTab.newPanel[0].id + " "+ rowClass ).each(function() {  
		 selectedPkey.push($(this).attr("relId"));
	});
	  
	tabParam[selectedTab.newPanel[0].id].selectedPkey = selectedPkey; 
    
    checkIsAllRowsSelected();
}
 
function deselectAllRows(){
    var rowClass = (isMobile()) ? '.mobile-selectable' : '.selectable';
    
	$("#" + selectedTab.newPanel[0].id + " "+ rowClass +" li").removeClass("ui-selected");   
	$("#" + selectedTab.newPanel[0].id + " .data-record [name='chkRow[]']").prop("checked",false);
    
	tabParam[selectedTab.newPanel[0].id].selectedPkey = Array();  	
    
    checkIsAllRowsSelected();
}
  

function deselectRow(obj,selectedTabId){   
    
    if(!selectedTabId)
        selectedTabId = selectedTab.newPanel[0].id;
    
	obj.removeClass("ui-selected");  
    obj.closest(".data-record").find("[name='chkRow[]']").prop("checked",false); 
     
    tabParam[selectedTabId].selectedPkey = jQuery.grep(tabParam[selectedTabId].selectedPkey, function(value) {
      return value != obj.attr("relId");
    });

    checkIsAllRowsSelected();
}

function selectRow(obj){
    deselectAllRows();
    
	obj.addClass("ui-selected");   
    obj.closest(".data-record").find("[name='chkRow[]']").prop("checked",true);
	  
	//tabParam[selectedTab.newPanel[0].id].selectedPkey.push(obj.attr("relId"));   
    var selectedPkey = Array();
    selectedPkey.push(obj.attr("relId"));
    
    tabParam[selectedTab.newPanel[0].id].selectedPkey = selectedPkey;
    
    checkIsAllRowsSelected();
}

function selectMultiRows(obj){
    obj.addClass("ui-selected");   
    obj.closest(".data-record").find("[name='chkRow[]']").prop("checked",true);
	   
    tabParam[selectedTab.newPanel[0].id].selectedPkey.push(obj.attr("relId"));
    
    checkIsAllRowsSelected();
}

function resetSelectedRows(){ 
    $("#" + selectedTab.newPanel[0].id + " .data-record [name='chkRow[]']").prop("checked",false); 
    tabParam[selectedTab.newPanel[0].id].selectedPkey = Array();  	 
}

function checkIsAllRowsSelected(){
    var datarows = $("#" + selectedTab.newPanel[0].id + " .data-record");
    var unselectedRows = $("#" + selectedTab.newPanel[0].id + " .data-record [name='chkRow[]']:not(:checked)"); 
    
    var checked = (datarows.length == 0 || unselectedRows.length > 0) ? false : true;
    $("#" + selectedTab.newPanel[0].id + " [name='chkRow-master']").prop("checked",checked);
}

function toggleAllSelectedDataDetail(state){ 	  
	 selectedPkey =  tabParam[selectedTab.newPanel[0].id].selectedPkey;
	 var target =  $("#" + selectedTab.newPanel[0].id + " .data-list ol .data-record");
	 
	 if (selectedPkey.length == 0){ 
	 	// jika tidak ad data yg diselect, tutup dulu semua quick view yg terbuka
	 	var showAll = true; 
		
		 target.find(".table-data-record-detail:visible").each(function(i) { 
	 			showAll = false;
				toggleQuickView($(this).closest(".data-record"),state);
		 }); 
		  
		 if(showAll){
			  target.each(function(i) { 
				toggleQuickView($(this),state);
			  }); 
		 }
	 }else{
		 // toggle hanya data yg diselect	
		 target.each(function(i) { 
				 if(jQuery.inArray( $(this).attr("relId"),selectedPkey ) >= 0){ 
					  toggleQuickView($(this),state);
				 }  
		 }); 	
		 
	 } 
}

function toggleQuickView(obj,state){ 
	if (tabParam[selectedTab.newPanel[0].id].quickView == false)
		return;
		
	var id = obj.attr("relId") ;    
	
	phpDataListFile = tabParam[selectedTab.newPanel[0].id].phpDataListFile;  
	
	var targetContent = $("#" + selectedTab.newPanel[0].id + " .table-data-record-detail" + id);  
	var isVisible = targetContent.is( ":visible" );
	  
	if (isVisible){ 
		if (state == undefined || state == 1) 
			targetContent.slideToggle();
	}else{   
		if (state == undefined || state == 2) {
			$.ajax({
				type: "POST",
				url:  phpDataListFile,
				data: "generateQuickView=1&id=" + id ,  
				success: function(data){ 
						 targetContent.html(data);
				} 
			}).done(function( data ) {   
					if (data != "")
						targetContent.slideToggle();
			});
		} 
	}  	 
}
	   

function updateData(loadMoreTriggered, selectedTabId ){    
    
     deselectAllRows();
     
	 var quickSearch = ""; 
	     
	  if (selectedTabId == undefined){
   		 selectedTabId = selectedTab.newPanel[0].id; 
	  }
	  
	  phpDataListFile = tabParam[selectedTabId].phpDataListFile;
	  targetContent = $("#" + selectedTabId + " .data-list");
	   
	  if (!loadMoreTriggered){ 
        $("html, body").animate({ scrollTop: 0 }, "fast");
		targetContent.html(_LOADING_ICON_);			 
	  }else{  
		targetContent.find(".load-more:first").find(".loading-icon:first").show(); 
	  }
	   
		//adding quick search value 
        var quickSearchObj = $("[name=quick-search-" + selectedTabId+"]");
		if ( quickSearchObj.val() != undefined)
			 quickSearch = $("[name=quick-search-" + selectedTabId+"]").val();  
       
    
        $("[name=quick-search-" + selectedTabId+"]").attr("reltype",0);
    
        quickSearchObj.closest("div").find(".clear-text-icon, .search-icon").hide();
        quickSearchObj.closest("div").find(".loading-icon").show();
       
	   $.ajax({
				type: "POST",
				url:  phpDataListFile,
		 		data: {generateDataRecords:1,
						quickSearchKey : quickSearch,
						page : $("[name=selPage-" + selectedTabId+"]").val(),
						loadMoreTriggered : loadMoreTriggered,
						lastRowIndex : tabParam[selectedTabId].lastRowIndex,
						filterCriteria :  tabParam[selectedTabId].filterCriteria, 
						selectedCriteriaTagKey :  tabParam[selectedTabId].selectedCriteriaTagKey,
						orderby :  tabParam[selectedTab.newPanel[0].id].orderby,
						ordertype :  tabParam[selectedTab.newPanel[0].id].ordertype
					   } ,
				success: function(data){  
				 	     var temp = JSON.parse(data); 
						  
						 tabParam[selectedTabId].filterCriteria = temp.filterCriteria;   
						 tabParam[selectedTabId].tagInformation = temp.tagInformation;   
						 tabParam[selectedTabId].contextMenu = temp.contextMenu;  
						 tabParam[selectedTabId].contextMenuCallback = temp.contextMenuCallback;   
                         tabParam[selectedTabId].isTransaction = temp.isTransaction;    
                         tabParam[selectedTabId].transactionStatus = temp.transactionStatus;
						 
						 if ( !loadMoreTriggered )  {    
						  	targetContent.hide().html(temp.dataList).fadeIn(); 
						 }else{ 
							 var pageIndex = Math.ceil(tabParam[selectedTabId].lastRowIndex / phpConfiguration.adminTotalRowsPerPage);
							 
                            var newContent = "<div class=\"page-break\">Page " + (pageIndex + 1) + "</div><div style=\"clear:both;\"></div>";
                            newContent += temp.dataList
							targetContent.find(".data-list-row:first").append($(newContent).hide().fadeIn()); 
						 }
						  
						 if (temp.eof)
								targetContent.find(".load-more:first").hide();
						 else
								targetContent.find(".load-more:first").show();
						 
						 var loadMoreObj = targetContent.find(".load-more:first");
						 loadMoreObj.find(".loading-icon:first").hide(); 
						 loadMoreObj.click(function() {  
								$(this).unbind("click");  
								updateData(true); 
						  }); 
			  			
					     tabParam[selectedTabId].lastRowIndex =  temp.lastRowIndex; 
						   
						 rebuildPaging($("[name=selPage-" + selectedTabId +"]"),temp.totalPages,temp.selectedPageIndex);
						 
						 updateStatusPanel();
						 updateRightClick(); 
						 deselectAllRows();
                         adjustVisibleColumn(); 

                        quickSearchObj.closest("div").find(".loading-icon").hide();
                        if (quickSearchObj.val() != "")
                            quickSearchObj.closest("div").find(".clear-text-icon").show();
                        else
                            quickSearchObj.closest("div").find(".search-icon").show();
					
//						$("#" + selectedTabId + " .will-spin").removeClass("fa-spin");
				} 
		});    
}

function updateRightClick(){  
     if(isMobile())  return;
    
	 var contextItem = tabParam[selectedTab.newPanel[0].id].contextMenu;  
	 var contextMenu =  $.contextMenu({
							selector: '#' + selectedTab.newPanel[0].id + ' .data-record',  
							events: {
								show: function(opt){ 
									  var selectedPkey = tabParam[selectedTab.newPanel[0].id].selectedPkey;   
									  if (selectedPkey.length <= 1){ 
											deselectAllRows(); 
											selectRow($(this).closest(".data-record")); 
									  }
								}
							 },  
							 
							callback: function(key, options) { eval(tabParam[selectedTab.newPanel[0].id].contextMenuCallback) }, 
							items: contextItem,  	
						}); 	 
	 				 
}

function ajaxDeleteData(selectedPkey,phpDataListFile){

    var dialogMsgObj  = $( "#dialog-message" );
    var description = dialogMsgObj.find("[name=txtDesc]").val();
    description = (description) ? description.trim() : '';

    $.ajax({
            type: "POST",
            url:  phpDataListFile,
            data: { action:"delete",
                    selectedPkey:selectedPkey,
                    description:description
                  }, 
        }).done(function( data ) { 

            var errorMsg = parseError(data);   

            if (errorMsg.valid == false && errorMsg.errorMsg != '')
                showMsgDialog(errorMsg.errorMsg,"Hapus Data Gagal"); 

            // tetep di refresh karena beberapa data mungkin berhasil dihapus.
            updateData(false);

        });     
}
 
function deleteData(){ 
	var selectedTabId = selectedTab.newPanel[0].id;   
	var phpDataListFile = tabParam[selectedTabId].phpDataListFile;
	var targetContent = $("#" + selectedTabId + " .data-list"); 
	var selectedPkey = tabParam[selectedTabId].selectedPkey;
    var dialogMsgObj  = $( "#dialog-message" );
    var isTransaction = tabParam[selectedTabId].isTransaction;
	
	if (selectedPkey.length == 0){
		showMsgDialog ("Anda belum memilih data yang hendak dihapus.");
		return ;
	} 
	
    var htmlContent = "Anda yakin akan menghapus data ini ?"; 
    if (isTransaction)
        htmlContent += '<div class="description"><div class="error-message"></div><textarea name="txtDesc" type="textarea" class="form-control" style="height:5em" placeholder="'+ucfirst(phpLang.cancelReason)+' ..."></textarea></div>';
    
    
	dialogMsgObj.html(htmlContent); 
    dialogMsgObj.dialog({
	  width: 300,
      height: "auto",
	  modal: true, 
      close: function(){dialogMsgObj.find(".error-message").html(""); dialogMsgObj.val("");}, 
	  title:"Konfirmasi Hapus Data",  
	});
    
	var buttons = new Array();
    buttons.push({
                 text: "OK", 
                 id: 'OK', 
                 click:  function() { 
                                var description = dialogMsgObj.find("[name=txtDesc]").val();
                                description = (description) ? description.trim() : '';

                                if(isTransaction && description == "" ){   
                                    dialogMsgObj.find(".error-message").html(phpErrorMsg['903']);
                                }else{ 
                                    ajaxDeleteData(selectedPkey,phpDataListFile);
                                    $( this ).dialog( "close" );
                                }
                         }
                });
    
    /*if (isTransaction){
        
        buttons.push({
                     text: "OK & Duplikasi", 
                     id: 'OKCopy', 
                     click:  function() {
                                    var description = dialogMsgObj.find("[name=txtDesc]").val();
                                    description = (description) ? description.trim() : '';

                                    if(description == "" ){   
                                        dialogMsgObj.find(".error-message").html(phpErrorMsg['903']);
                                    }else{ 
                                        // utk duplikasi, harus cari statuskey terakhir, terus panggin fungsi chnage status
                                        ajaxDeleteData(selectedPkey,phpDataListFile);
                                        $( this ).dialog( "close" );
                                    }

                             }
                    });
    }*/

    buttons.push({
                 text: "Cancel", 
                 id: 'Cancel', 
                 click:  function() {
                                     $( this ).dialog( "close" );
                         }
                }); 

    dialogMsgObj.dialog('option', 'buttons', buttons); 
    dialogMsgObj.closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus(); 
	  
}

 
function duplicateData(){ 
	var selectedTabId = selectedTab.newPanel[0].id;   
	var phpDataListFile = tabParam[selectedTabId].phpDataListFile;
	var targetContent = $("#" + selectedTabId + " .data-list"); 
	var selectedPkey = tabParam[selectedTabId].selectedPkey;
	
	if (  selectedPkey.length == 0){
		showMsgDialog ("Anda belum memilih data yang hendak diduplikasi.");
		return ;
	} 
	
	$( "#dialog-message" ).html("Anda yakin akan menduplikasi data ini ?");
	$( "#dialog-message" ).dialog({
	  width: 300,
	  modal: true,
	  title:"Konfirmasi Duplikasi Data", 
	  open: function() {
		  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
	  },
        
      close:function() {}, 
        
	  buttons : {
		  OK : function (){
					 $.ajax({
						type: "POST",
						url:  phpDataListFile,
						data: {action:"duplicate",
								selectedPkey:selectedPkey
							  }, 
					}).done(function( data ) { 
					 
						var errorMsg = parseError(data);   
						
						if (errorMsg.valid == false && errorMsg.errorMsg != '')
							showMsgDialog(errorMsg.errorMsg,"Duplikasi Data Gagal"); 
						 
						// tetep di refresh karena beberapa data mungkin berhasil dihapus.
						updateData(false);
						
					});    
				 	
					$( this ).dialog( "close" );
		  },
		  Cancel : function (){ 
		  	$( this ).dialog( "close" );
		  }
	  },
	});  
}

function printTransaction( filename, splitWindows ){ 
        var selectedTabId = selectedTab.newPanel[0].id;
        var selectedPkey = tabParam[selectedTabId].selectedPkey;  
      
    	if (selectedPkey.length == 0){
            showMsgDialog ("Anda belum memilih data yang hendak diprint."); 
            return ;
        }

		var arrFileName = filename.split('?');
		filename = arrFileName[0].replace(/\/$/, ""); // remove trailing slash
	
//		var param = '/' + arrFileName[1] || '';
	
		var param = '';
		if (arrFileName[1] != undefined) 
				param = '/' + arrFileName[1];
		 
        splitWindows = (splitWindows) ? splitWindows : false;
    
        if(splitWindows){
            for(var i=0;i<selectedPkey.length;i++) 
                window.open(filename+'/' + selectedPkey[i] + param, "_blank" ); 
        }else{
            window.open(filename+'/' + selectedPkey+ param );   
        }     
         
}

function changeStatus(statusKey,statusName){ 
     
    var dialogMsgObj  = $( "#dialog-message" );
	var selectedTabId = selectedTab.newPanel[0].id;    
	var selectedPkey = tabParam[selectedTabId].selectedPkey;  
    var transactionStatus = tabParam[selectedTabId].transactionStatus;
     
    var cancelStatusKey = transactionStatus[transactionStatus.length-1]['pkey'];
    
    var isCancel = (tabParam[selectedTabId].isTransaction == true && statusKey == cancelStatusKey) ? true : false;

	if (selectedPkey.length == 0){
		showMsgDialog ("Anda belum memilih data yang hendak diubah statusnya.");
		 $("[name=selStatus-"+selectedTab.newPanel[0].id+"]").val(0); 	 
		return ;
	}
	  
    var htmlContent = "Anda yakin akan mengubah status data ini menjadi " + statusName + " ?";
     
    if (tabParam[selectedTabId].isTransaction == true && isCancel)
        htmlContent += '<div class="description"><div class="error-message"></div><textarea name="txtDesc" type="textarea" class="form-control" style="height:5em" placeholder="'+ucfirst(phpLang.cancelReason)+' ..."></textarea></div>';
    
	dialogMsgObj.html(htmlContent);
    
	dialogMsgObj.dialog({
	  width: 300,
      height: "auto",
	  modal: true, 
      close: function(){dialogMsgObj.find(".error-message").html(""); dialogMsgObj.val("");},
	  title:"Konfirmasi Perubahan Status",  
	});
	
	var buttons = new Array();
	buttons.push({
					 text: "OK", 
					 id: 'OK', 
					 click:  function() { 
                                    var description = dialogMsgObj.find("[name=txtDesc]").val();
                                    description = (description) ? description.trim() : '';
                         
                                    if(isCancel && description == "" ){   
                                        dialogMsgObj.find(".error-message").html(phpErrorMsg['903']);
                                    }else{ 
										changeStatusData(statusKey,selectedPkey,0,description);
										$( this ).dialog( "close" );
                                    }
							 }
					});
	
	if (isCancel){
		buttons.push({
					 text: "OK & Duplikasi", 
					 id: 'OKCopy', 
					 click:  function() {
                                    var description = dialogMsgObj.find("[name=txtDesc]").val();
                                    description = (description) ? description.trim() : '';
                         
								    if(isCancel &&  description == "" ){   
                                        dialogMsgObj.find(".error-message").html(phpErrorMsg['903']);
                                    }else{ 
										changeStatusData(statusKey,selectedPkey,1,description);
										$( this ).dialog( "close" );
                                    }
                                    
							 }
					});
	}
	 				
	buttons.push({
					 text: "Cancel", 
					 id: 'Cancel', 
					 click:  function() {
										 $( this ).dialog( "close" );
							 }
					} 
					
				); 
	
	
	dialogMsgObj.dialog('option', 'buttons', buttons); 
    dialogMsgObj.closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus(); 
	
    $("[name=selStatus-"+selectedTab.newPanel[0].id+"]").val(0); 	 
}

function setRowToLoadingState(selectedTabId, selectedPkey){
     var overlay = '<div class="loading-overlay" style="width:100%;  background-color:rgba(0, 0, 0, .3); position: absolute; top:0; left:0; text-align:center; padding-top:5px;"><div class=\"loading-page-icon fas fa-spinner fa-spin\"></div></div>';
     for(i=0;i<selectedPkey.length;i++){
        var row = $("#"+selectedTabId+" [relId="+selectedPkey[i]+"]"); 
        deselectRow(row, selectedTabId);
         
        var rowHeight = row.height();
         
        row.css("position","relative");
        row.append(overlay);
        row.find(".loading-overlay").css("height", rowHeight+ "px");
         
        row.attr("class","data-record unselectable"); 
        //row.html('<div style="padding-top:5px">'+_LOADING_TEMPLATE_+'</div>').attr("class","data-record unselectable process-queue"); 
    }
}

function changeStatusData(statusKey,selectedPkey,copyData, description){

	var selectedTabId = selectedTab.newPanel[0].id;   
	var phpDataListFile = tabParam[selectedTabId].phpDataListFile;
	var selectedPkey = tabParam[selectedTabId].selectedPkey; 
	  	
    /*  
    // block selected row 
    setRowToLoadingState(selectedTabId, selectedPkey);

    $.ajax({
        type: "POST",
        url:  phpDataListFile,
        data:{action:"changestatus",
            newStatus:statusKey,
            selectedPkey:selectedPkey ,
            copyData:copyData ,
            description:description 
        },
    }).done(function( data ) {  
        var errorMsg = parseError(data);   
		
		if (errorMsg.valid == false && errorMsg.errorMsg != '')
			showMsgDialog(errorMsg.errorMsg,"Perubahan Status Gagal"); 
        
        generateDataRow(selectedTabId, selectedPkey);  
    }); 
 
    //=========*/
   // console.log(phpDataListFile);
//    console.log(selectedPkey);
    
	$.ajax({
		type: "POST",
		url:  phpDataListFile, 
		data:{action:"changestatus",
				newStatus:statusKey,
				selectedPkey:selectedPkey ,
				copyData:copyData ,
				description:description 
			},
	}).done(function( data ) {  
		var errorMsg = parseError(data);   
		
		if (errorMsg.valid == false && errorMsg.errorMsg != '')
			showMsgDialog(errorMsg.errorMsg,"Perubahan Status Gagal"); 
		 
		// tetep di refresh karena beberapa data mungkin berhasil diubah statusnya.
		 updateData(false);  
	});  
}

function changeTag(tagKey,statusName){ 
	var selectedTabId = selectedTab.newPanel[0].id;   
    var selectedTabObj = $("#" + selectedTabId);
	var phpDataListFile = tabParam[selectedTabId].phpDataListFile;
	var selectedPkey = tabParam[selectedTabId].selectedPkey; 
	
	if (selectedPkey.length == 0){
		showMsgDialog ("Anda belum memilih data yang hendak diubah tag nya."); 
		return ;
	}
	 
	var msg =  "Anda yakin akan mengubah tag data ini menjadi " + statusName + " ?";
	if (tagKey == 0)
		msg = "Anda yakin akan menghilangkan tag data ini ?";
		
		
	$( "#dialog-message" ).html(msg);
	$( "#dialog-message" ).dialog({
	  width: 300,
	  modal: true,
	  title:"Konfirmasi Update Tag", 
	  open: function() {
		  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
	  },
	  buttons : {
		  OK : function (){
               
                    setRowToLoadingState(selectedTabId, selectedPkey)
                     
                    $.ajax({
						type: "POST",
						url:  phpDataListFile,
						data:{action:"changetag",
							newTag:tagKey,
							selectedPkey:selectedPkey
						},
					}).done(function( data ) {  
                        //updateTagStatusPanel();
                        generateDataRow(selectedTabId, selectedPkey);  
					}); 
				 	
					$( this ).dialog( "close" );
		  },
		  Cancel : function (){ 
		  	$( this ).dialog( "close" );
		  }
	  },
	});  
}

function bindDataRowChkBox(obj){ 
    
    if (obj.prop("checked"))
        selectMultiRows(obj.closest(".data-record"));
    else
        deselectRow(obj.closest(".data-record"));
   
}


function generateDataRow(selectedTabId, arrPkey){ 
    
	var phpDataListFile = tabParam[selectedTabId].phpDataListFile;
    
    $.ajax({
        type: "POST",
        url:  phpDataListFile,
        data:{action:"generaterow", 
              selectedPkey:arrPkey, 
        },
    }).done(function( data ) { 
        // isi ulang data
        if (!data) return;
        
        var data = JSON.parse(data);
        
        for(i=0;i<data.length;i++){
            var pkey = data[i]['pkey'];
            var newContent = data[i]['html']; 
            
            var row = $("#"+selectedTabId+" [relId="+pkey+"]");  
            
            row.fadeOut("slow").replaceWith(newContent);
            row = $("#"+selectedTabId+" [relId="+pkey+"]");  // select ulang
            row.fadeIn("slow");
        } 
        
        bindEl( $( "#" + selectedTabId + " .data-record [name=\'chkRow[]\']"),"click", function(){bindDataRowChkBox($(this))} ); 
        
    }); 
}

function parseError(data){   
    
 	var errorArr = JSON.parse(data);
    
	var error = "";
	var status = true;
	for (i=0;i<errorArr.length;i++){ 
		   // nanti harus diupdate bisa pisah antara warning, error atau success
           // sementara cumamarking saja d salah satu yg error
			if(status == true)
                status = errorArr[i].valid;
        
            // ssementara munculin yg error saja
            if (status != true)
			 error = error + "<li>" + errorArr[i].message + "</li>"; 
	}
	
	if (error != "")
		error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";
	
	var returnArr = {};
	returnArr.valid = status; 
	returnArr.errorMsg = error;
	
	return returnArr;
}
 

function showMsgDialog(msg,title){
	if (title == undefined)
		title = "Informasi";
		 
	$( "#dialog-message" ).html(msg);
	$( "#dialog-message" ).dialog({
	  width: 600,
	  modal: true,
	  title:title, 
	  buttons:{ 'OK' : function() { $( this ).dialog( "close" );} } 
	});
    
}
 

function rebuildPaging(objectname,totalPages,selectedPageIndex){  
	objectname.html("");
	if (totalPages == 0){
		var newOption = $('<option value="0">0 / 0</option>');
		objectname.append(newOption);
	} else{
		for(i=0;i<totalPages;i++){
			var selected = "";
			if (selectedPageIndex == i)
				selected = "selected=\"selected\"";
				
			var newOption = $('<option value="'+i+'" '+ selected + '>'+(i+1)+' / ' + totalPages + '</option>');
			objectname.append(newOption);
		} 
	} 
} 
/* 
function prepareToken($form){
    
    var tokenElName = 'hidToken[]';
    var obj = $form.find(".mnv-transaction");
    var token = 1;
    
    row = obj.find(">.transaction-detail-row"); 
    
    var inputEl = obj.find("input, select, textarea").not("[name='"+tokenElName+"']");  
    // gk boleh input level transaksi kedua harusnya
    inputEl.each(function() {  
      var newName = $(this).attr("name").replace("[]","[1][]");
      $(this).attr("name",newName);   
    })
    
    // overwrite index yg sudah diset ke [0]
    row.each(function(i) {    
            token++; 
            $(this).prepend( $('<input hidden="'+tokenElName+'" value="'+token+'">'));
        
            // second layer
            var detailRow = $(this).find(".transaction-detail-row"); 
            
             detailRow.each(function(i) {     
                //$(this).prepend( $('<input name="'+tokenElName+'" value="'+token+'">'));

                var detailRowEl = $(this).find("input, select, textarea").not("[name='"+tokenElName+"']");   
                detailRowEl.each(function() {      
                    var newName = $(this).attr("name").replace('[1][]','['+token +'][]');  
                    $(this).attr("name",newName);    
                });

            }); 
    })
       
}
*/


function updateRowsInformation(form){  
    
    //var totalRowsName = 'hidTotalRows';
    var scope = ".transaction-detail";
    
    var rowList = form.find(scope).not(".row-template " + scope);
    rowList.each(function() {     
        var totalRows = $(this).find(">.transaction-detail-row").length;
        var level = $(this).attr("attr-level") || 0;
        var groupName = ($(this).attr("attr-group")) ?  $(this).attr("attr-group") + 'TotalRows' : 'hidTotalRows';
        
        var totalRowsObj = $(this).find("[name='"+groupName+"["+level+"][]']").first();
        if (totalRowsObj.length == 0){  
            totalRowsObj = $('<input name="'+groupName+'['+level+'][]" type="hidden">');
            $(this).closest(scope).prepend(totalRowsObj); 
        } 
         
        totalRowsObj.val(totalRows);
    });
    
     
    // remove all hidDetailKey in template row
    
    //update hidDetailKey array index
   /* var hidDetailKey = form.find("[name='hidDetailKey[]']"); 
    hidDetailKey.each(function() {     
        var level = $(this).closest(scope).attr("attr-level");
        if(level) // compability issue
            $(this).attr("name","hidDetailKey["+level+"][]");
    });*/
    
}


// versi awal sebelum form upload STORAGE
//function submitForm(e, form, parentElement, responseTarget){ 
//       
//    var tabID = form.tabID;
//    var parentPanelId = parentElement.parentPanelId;
//    var parentTitle = parentElement.parentTitle;
//    var autoPrintURL = parentElement.autoPrintURL;
//    
//    // Prevent form submission
//    e.preventDefault();
//    
//    // Get the form instance
//     var $form = $(e.target);
// 
//    var saveAndNew = $form.find("[name=hidSaveAndNew]").val();  
//    
//    disabledButton($form.find(".btn-primary"));   
//    
//    //calculate total rows if needed
//    updateRowsInformation($form);
//
//    // Get the BootstrapValidator instance
//    var bv = $form.data('bootstrapValidator');
//    
//    //getckeditor(); 
//    
//    $.ajax({
//      type: "POST",
//      url: $form.attr('action'),  
//      beforeSend : function() {
//         // prepareToken($form); 
//          ckeditorUpdateElement(selectedTab.newPanel[0].id )  
//      },
//      data: $form.serialize(), 
//      success: function(result) { 
//                        onFormSubmitDone( {form : $form,  saveAndNew : saveAndNew, tabID : tabID, autoPrintURL : autoPrintURL},
//                                          result,   
//                                          {parentPanelId : parentPanelId, parentTitle : parentTitle },
//                                          responseTarget);    
//                }, 
//      dataType: 'json'
//    });
//     
// 
//}

function submitForm(e, form, parentElement, responseTarget){ 
       
    var tabID = form.tabID;
    var parentPanelId = parentElement.parentPanelId;
    var parentTitle = parentElement.parentTitle;
    var autoPrintURL = parentElement.autoPrintURL;
    
    // Prevent form submission
    e.preventDefault();
    
    // Get the form instance
     var $form = $(e.target);
 
    var saveAndNew = $form.find("[name=hidSaveAndNew]").val();  
    
    disabledButton($form.find(".btn-primary"));   
    
    //calculate total rows if needed
    updateRowsInformation($form);

    // Get the BootstrapValidator instance
    var bv = $form.data('bootstrapValidator');
    
    $.each( $form.find(".ckeditor"), function() {
        var tempRel = $(this).attr("attr-rel-val");
        var hidObj = $(this).closest("div").find("[name=\""+tempRel+"\"]");
        hidObj.val($(this).val());
    });
    
    $.ajax({
      type: "POST",
      url: $form.attr('action'),  
      beforeSend : function() {  
         // prepareToken($form); 
         // ckeditorUpdateElement(selectedTab.newPanel[0].id)  
      },
      data: new FormData($form[0]), //$form.serialize(), 
      success: function(result) { 
                        onFormSubmitDone( {form : $form,  saveAndNew : saveAndNew, tabID : tabID, autoPrintURL : autoPrintURL},
                                          result,   
                                          {parentPanelId : parentPanelId, parentTitle : parentTitle },
                                          responseTarget);    
                }, 
     dataType: 'json',
     cache: false,
     contentType: false,
     processData: false    
    });

    // Try saving snapshot first, but don't block AJAX if it fails
    //saveSnapshot()
    //.then(function(id) {
    //    // snapshotId = id;
    //    console.log("Snapshot saved with ID");
    //})
    //.catch(function(err) {
    //    console.warn("Failed to save snapshot, continuing with AJAX:", err);
    //})
    //.finally(function() {
        //$.ajax({
        //      type: "POST",
        //      url: $form.attr('action'),  
        //      beforeSend : function() {  
        //         // save dulu ke indexeddb buat jaga2 kalo gagal 
        //         // prepareToken($form); 
        //         // ckeditorUpdateElement(selectedTab.newPanel[0].id)  
        //      },
        //      data: new FormData($form[0]), //$form.serialize(), 
        //      success: function(result) { 
        //                        // hapus indexeddb
        //                        //deleteSnapshot(pkey,reftablekey); <-- delete snapshot here
//
        //                        onFormSubmitDone( {form : $form,  saveAndNew : saveAndNew, tabID : tabID, autoPrintURL : autoPrintURL},
        //                                          result,   
        //                                          {parentPanelId : parentPanelId, parentTitle : parentTitle },
        //                                          responseTarget);  
//
        //                }, 
        //     dataType: 'json',
        //     cache: false,
        //     contentType: false,
        //     processData: false    
        //    });
    //});
     

}

// versi awal sebelum form upload STORAGE
//function ckeditorUpdateElement(tabID) {   
//    var objEditor = ckeditorList[tabID]; 
//    
//    if (objEditor == undefined)
//        return;
//    
//    for (i=0;i<objEditor.length;i++){  
//        var elementId = objEditor[i].elementId; 
//        var editor = objEditor[i].editor; 
//        $("#"+ elementId ).val(editor.getData());  
//    } 
//       
//} 

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

function hasErrMsg(result){
	for(i=0;i<result.length;i++){ 
		if(result[i]['valid'] == false) return true;
	}
	
	return false;
}
 
function onFormSubmitDone(formArgs,result,parentElement,responseTarget){   
     
        var form = formArgs.form;
        var saveAndNew = formArgs.saveAndNew;
        var tabID = formArgs.tabID;
        var autoPrintURL = formArgs.autoPrintURL; 
        var parentPanelId = parentElement.parentPanelId;
        var parentTitle = parentElement.parentTitle; 
        
        var responseTargetValueObj =  eval(responseTarget.value);
        var responseTargetKeyObj =  eval(responseTarget.key);
        var revalidateField = eval(responseTarget.revalidateField);
    
        if (saveAndNew == undefined)
            saveAndNew = 0;
    
		var error = ""; 
		for (i=0;i<result.length;i++)    
			error = error + "<li>" + result[i].message + "</li>";  
		
		if (error != "")
			error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";  
			 
		$("#" + tabID + " .notification-msg").html(error).hide().fadeToggle("fast");
			 
		if (hasErrMsg(result)){ 
			$("#" + tabID + " .notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal"); 
			form.data('bootstrapValidator').resetForm();
			$("html, body").animate({ scrollTop: 0 }, "slow");
			//$("#" + tabID ).closest('form').bootstrapValidator('revalidateField');
				
		}else{
            
            if(parentPanelId){ 
                 
               if (saveAndNew == 1){ 
                    $("#" + tabID + " .notification-msg").hide();
                    var href = tabParam[tabID].href.replace(/&id=([^&]$|[^&]*)/i, ""); 
                    $("#" + tabID).load(href);
                }else{ 
                    $("#" + tabID + " .notification-msg").removeClass("bg-red-cardinal").addClass("bg-green-avocado");  
                
                    // cek kalo ad idcol, jgn close dan keluarkan id dr idcol
                    // perlu cek jg gk kalo sdh data terakhir ? 
                    var nextPageButton = $("#" + tabID + " .next-page");
                    var nextPkey = nextPageButton.attr("attr-pkey") || 0;
                    nextPkey = parseInt(nextPkey);
                     
                    if(nextPkey == 0) { 
                        selectedTab.newTab[0].remove();

                        var num_tabs = findTabIndexByTitle(parentTitle); 
                        $tabs.tabs( "option", "active", num_tabs );   
                        $tabs.tabs("refresh");   
                        
                       // masalah di klik kanan, dsb karena selectedTab nya gk berubah, jd kita pindahin saja sementara
                        updateData(false,  parentPanelId ); 
                    }else{
                        nextPageButton.click();
                    }
                    
                    // add auto print 
                    var saveAndProcess = false;
                    if ($("#" + tabID + " [name=hidSaveAndProceed]") && $("#" + tabID + " [name=hidSaveAndProceed]").val() == 1)
                        saveAndProcess = true;
                      
                    if(autoPrintURL && saveAndProcess && result[0]['data']['statuskey'] == 2 && $("#" + tabID + " [name=hidId]"))
                        window.open(autoPrintURL +'/'+ result[0]['data']['pkey']);
 
                }
                
            }else{     
                
                 
                // harusnya utk quickadd, tp perlu dimodif jg utk shortcut 
                
                if(responseTargetValueObj != ''){ 
                    
                    var fieldValue = "name";
                    var fieldKey = "pkey";
                    if(responseTarget.valueDBField  && responseTarget.valueDBField != "") 
                        fieldValue = responseTarget.valueDBField;                 


                    responseTargetValueObj.val(result[0]['data'][fieldValue]); 
                    responseTargetKeyObj.val(result[0]['data'][fieldKey]).change(); 

                    //validate ulang  
                    if(revalidateField)
                        responseTargetValueObj.closest('form').bootstrapValidator('revalidateField', responseTargetValueObj.attr("name") );   

                    hideOverlayScreen(); 
                }else{
                    // utk shortcut 
                    selectedTab.newTab[0].remove(); 
                    $tabs.tabs("refresh");  // gk tau kerefresh semua atau gmana 
                }
            }
            
		}  
      
        disabledButton(form.find(".btn-primary"),false);   
}
  
// FILE UPLOADER HANDLER */  

function deleteImageUploaderThumb(obj,fileUploaderTarget,token){
	$(obj).closest("li").remove(); 
	updateItemImageArray(fileUploaderTarget,token); 
} 


function deleteFileUploaderThumb(obj,fileUploaderTarget,token){
	$(obj).closest("li").remove(); 
	updateItemFileArray(fileUploaderTarget,token); 
} 

function pushImageThumb(fileUploaderTarget,fileInfo,multipleFile,multipleColor,variantTarget){  
    
    var selectedTabId = selectedTab.newPanel[0].id; 
    if (fileUploaderTarget.tabID != undefined)
        selectedTabId = fileUploaderTarget.tabID;
    
    var path = phpConfiguration.uploadTempDocShort; 
    
	var target = $("#defaultForm-"+selectedTabId+ " ." + fileUploaderTarget.name); 
	var iconMultipleColor = '';
	 
	if (multipleFile == false){ 
		target.find(".image-list").html(""); 
	}
	 
	if (multipleColor == true){ 
//		iconMultipleColor =  "<div class=\"product-variant-icon-small minerva-icon-15\" style=\"float:right; margin-right:0.5em\" onClick=\" loadImageVariant('"+fileUploaderTarget+"',this, {'folder':'" +fileInfo.folder+ "', 'token':'" +fileInfo.token+ "', 'fileName':'" +fileInfo.fileName+ "','phpThumbHash':'" +fileInfo.phpThumbHash+ "'} ,'"+variantTarget+"')\" ></div>";
	} 
	 
	var extension = fileInfo.fileName.substr( (fileInfo.fileName.lastIndexOf('.') +1) );
	fileurl = "../phpthumb/phpThumb.php?src="+path+ fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName+"&w=150&h=150&far=C&f=png&hash=" + fileInfo.phpThumbHash;
	
	if (extension == 'ico')
		fileurl = phpConfiguration.uploadTempURL + fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName;
	  
	 
 	var temp = "<li relfilename=\""+fileInfo.fileName+"\" relPHPThumbHash=\""+fileInfo.phpThumbHash+"\">";
	temp += "<div class=\"file-uploader-image\"><img src=\""+ fileurl +"\"/></div>";
	temp += "<div class=\"file-uploader-action-bar\">";
	temp += "<input type=\"hidden\" name=\"hidDetail"+fileUploaderTarget['name']+"Key[]\">";
	temp += "<input type=\"hidden\" name=\"hidName"+fileUploaderTarget['name']+"[]\">";
	temp += "<a href=\"#\" onClick=\"deleteImageUploaderThumb(this,{'tabID':'"+fileUploaderTarget.tabID+"' , 'name':'"+fileUploaderTarget.name+"'},'" + fileInfo.token  + "')\"><i class=\"far fa-times\" style=\"float:right; font-size:1.2em;\" ></i></a>";
	temp += "<a href=\"/phpthumb/phpThumb.php?src="+path+fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName+"&far=C&f=png&hash="+fileInfo.phpThumbHash+"\" target=\"_blank\"><i class=\"far fa-eye\" style=\"float:right; font-size:1.2em; margin-right:0.5em; padding-bottom:0.2em\"></i></a>";
	temp += iconMultipleColor;
	temp += "</div>";
	temp += "</li>"; 
	
	//../phpthumb/phpThumb.php?src=/home/programstok/minerva/../_temp/freeplan.program-stok.com/item/2/avatar-default.png&w=150&h=150&far=C&hash=726eb8d79291e5895aa5cadabc7c8cf2
	
	target.find(".image-list").append(temp);	
	   
	updateItemImageArray(fileUploaderTarget,fileInfo.token);
	  
    target.find('.image-list li:last img').on('load', function(){
        $(this).closest(".file-uploader-image").css("background-image","none"); 
    }); 
			
}

function updateItemImageArray(fileUploaderTarget,token){
	  
    var selectedTabId = selectedTab.newPanel[0].id; 
    if (fileUploaderTarget.tabID != undefined)
        selectedTabId = fileUploaderTarget.tabID;
    
	 var target = $("#defaultForm-"+selectedTabId+ " ." + fileUploaderTarget.name);   
    
	 uploadedImage[token+selectedTabId] = Array();
	 $("#defaultForm-"+selectedTabId+ " ." +fileUploaderTarget.name + " .image-list li").each(function(i) {   
             var fileName = $(this).attr("relfilename"); 
             $(this).find("[name=\"hidName"+fileUploaderTarget.name+"[]\"]").val(fileName);
			 uploadedImage[token+selectedTabId].push(fileName); 
     });
	   
	 target.find("[name=" + fileUploaderTarget.name + "]").val(uploadedImage[token+selectedTabId]);
	 
}
 

function createImageUploader(fileUploaderTarget,fileInfo,multipleFile,multipleColor,variantTarget){ 
     
	var selectedTabId = selectedTab.newPanel[0].id;
    
    if (fileUploaderTarget.tabID != undefined)
        selectedTabId = fileUploaderTarget.tabID;
     
	var target = $("#defaultForm-"+selectedTabId).find("." + fileUploaderTarget.name);
	  
    //alert("#defaultForm-"+selectedTabId+ " ." + fileUploaderTarget.name);
    //target.hide();
    
	 if (fileInfo.token == undefined || fileInfo.token == "")  
		 fileInfo.token = Math.floor((Math.random() * 1000) + 1).toString() + $.now();     
	
	  
	uploadedImage[fileInfo.token+selectedTabId] = Array();
	target.append("<input type=\"hidden\" name=\"" + fileUploaderTarget.name + "\" />"); 
	target.append("<input type=\"hidden\" name=\"token-" + fileUploaderTarget.name + "\" value=\"" + fileInfo.token + "\" />");
	 
	if (variantTarget!= undefined && variantTarget != ""){ 
		target.append("<input type=\"hidden\" name=\"" + variantTarget + "\" />");  
	} 
     
	if (fileInfo.arrImage != undefined || fileInfo.arrImage == ""){ 
         var i;
      	 for(i=0;i<fileInfo.arrImage.length;i++)  { 
             pushImageThumb(fileUploaderTarget,{"folder":fileInfo.folder, "token":fileInfo.token, "fileName":fileInfo.arrImage[i],"phpThumbHash":fileInfo.phpThumbHash[i]},multipleFile,multipleColor,variantTarget) 
         }
	}
	  
    
	var uploader = new qq.FileUploader({
						element: target.find('.file-uploader')[0], 
						action: 'fileuploader.php?action=upload&param='+  JSON.stringify( {'folder': fileInfo.folder,'token': fileInfo.token} ), 
						allowedExtensions:['jpg','jpeg','png','gif','ico'],
						onComplete: function(id, fileName, responseJSON){   
							if (responseJSON.success == true)
								pushImageThumb(fileUploaderTarget,{"folder":fileInfo.folder, "token":fileInfo.token, "fileName":responseJSON.fileName,"phpThumbHash":responseJSON.phpThumbHash},multipleFile,multipleColor,variantTarget); 
						} 
					});   
	 
	return fileInfo.token;				  
}
   

function createFileUploader(fileUploaderTarget,folder,token, arrFile,multipleFile){
	
	var selectedTabId = selectedTab.newPanel[0].id
	var target = $("#defaultForm-"+selectedTabId+ " ." + fileUploaderTarget);
	  
	if (token == undefined || token == "")  
		 token = Math.floor((Math.random() * 1000) + 1).toString() + $.now();     
	
	 
	uploadedFile[token+selectedTabId] = Array();
	target.append("<input type=\"hidden\" name=\"" + fileUploaderTarget + "\" />"); 
	target.append("<input type=\"hidden\" name=\"token-" + fileUploaderTarget + "\" value=\"" + token + "\" />");
	  
	if (arrFile != undefined || arrFile == ""){ 
         var i;
		 for(i=0;i<arrFile.length;i++) 
			 pushFileThumb(fileUploaderTarget,folder,token,arrFile[i],multipleFile) 
	}
	 
 
	var uploader = new qq.FileUploader({
						element: target.find('.file-uploader')[0], 
						action: 'fileuploader.php?action=upload&isfile=1&param='+  JSON.stringify( {'folder': folder,'token': token} ),  
						onComplete: function(id, fileName, responseJSON){  
							if (responseJSON.success == true)
								pushFileThumb(fileUploaderTarget,folder,token,responseJSON.fileName,multipleFile); 
						} 
					});   
	 
	return token;				  
}

function pushFileThumb(fileUploaderTarget,folder,token,fileName,multipleFile){ 
	var target = $("#defaultForm-"+selectedTab.newPanel[0].id+ " ." + fileUploaderTarget);
	var extension = fileName.substr( (fileName.lastIndexOf('.') +1) ).toLowerCase();
	
	var xPos = 0;
	/*switch(extension) { 
        case 'doc':
        case 'docx':
            xPos = 35;
        break;
        case 'pdf':
              xPos = 0;
        break;
        default:
          	 xPos = 70;
    }*/ 
	
 	var temp = "<li relfilename=\""+fileName+"\" >"; 
	temp += "<div class=\"panel\">";  
	temp += "<input type=\"hidden\" name=\"hidDetail"+fileUploaderTarget+"Key[]\">";
	temp += "<input type=\"hidden\" name=\"hidName"+fileUploaderTarget+"[]\">";
	temp += "<div class=\"file-uploader-description\"><a href=\"/download.php?filename="+ folder + token+ "/"+fileName+"\" target=\"_blank\" title=\""+fileName+"\">"+ fileName +"</a></div>"; 
	temp += "<div class=\"delete-icon\" onClick=\"deleteFileUploaderThumb(this,'" + fileUploaderTarget  + "','" + token  + "')\"><i class=\"fas fa-times\"></i></div>";
    temp += "<div style=\"clear:both;\"></div>";
    temp += "</div>";
	temp += "</li>";
     
    if (multipleFile == false){  
		target.find(".file-list").html(""); 
	} 
	
	target.find(".file-list").append(temp);	
	   
	updateItemFileArray(fileUploaderTarget,token);
	    
	target.find('.file-list li:last img').on('load', function(){
	  $(this).closest(".file-uploader-image").css("background-image","none"); 
	});  
			
}



function updateItemFileArray(fileUploaderTarget,token){
	
	 var selectedTabId = selectedTab.newPanel[0].id
	 var target = $("#defaultForm-"+selectedTabId+ " ." + fileUploaderTarget);  
	 
	 uploadedFile[token+selectedTabId] = Array();
	 $("#defaultForm-"+selectedTab.newPanel[0].id+ " ." +fileUploaderTarget + " .file-list li").each(function(i) {     
             var fileName = $(this).attr("relfilename"); 
             $(this).find("[name=\"hidName"+fileUploaderTarget+"[]\"]").val(fileName);
			 uploadedFile[token+selectedTabId].push($(this).attr("relfilename"));
	  });
     
	   
	 target.find("[name=" + fileUploaderTarget + "]").val(uploadedFile[token+selectedTabId]);
	 
}
  
/* IMAGE VARIANT */
 
function loadImageVariant(parentTarget,obj,fileInfo,variantTarget) {     
  	var target = $("#defaultForm-"+selectedTab.newPanel[0].id+ " ." + parentTarget);
	title = 'Update Variasi Warna'; 
 	
	var temp = target.find("[name=" + variantTarget + "]").val() ;
	var color = "#000000";
	 
	if(temp != ""){		 
		var imageList =  JSON.parse(temp); 
		
		if (imageList[fileInfo.fileName] != undefined){
			color = imageList[fileInfo.fileName][0].fileColor;
		} 
	}
	 
	 
	content = '<div class="'+variantTarget+'" reltoken ="'+fileInfo.fileName+'">'; 
	content += '<ul class="image-list">';
	content += '<li relfilename="'+fileInfo.fileName+'"  >';
	content += '<div class="file-uploader-image" ><img src="../phpthumb/phpThumb.php?src='+phpConfiguration.uploadTempDoc + fileInfo.folder +fileInfo.token+ '/'+fileInfo.fileName+'&w=150&h=150&far=C&hash='+fileInfo.phpThumbHash+'"/></div>';
	content += '<div class="file-uploader-action-bar">';  
	content += '<input type="color" class="form-control" style="padding:0 !important; width:2em; height:1.5em; border-radius:0; margin:auto;" name="variantColor[]" value="' + color +'" />'; 
	content += '</div>';
	content += '</li>';  
	content += '</ul>';
	content += '<div style="clear:both; height:1em;"></div>';
	content += '<div class="file-uploader">';
	content += '<noscript>';			
	content += '<p>Please enable JavaScript to use file uploader.</p>'; 
	content += '</noscript>'; 
	content += '</div>';
	content += '</div>'; 
			  
    alert("deprecated overlay screen");
	//loadOverlayScreen(title,content);
	 
	$( "." + variantTarget + " .image-list" ).sortable({ placeholder: "sortable-placeholder"});
	$( "." + variantTarget + " .image-list" ).disableSelection();
	
	createImageUploaderforImageVariant(parentTarget, variantTarget,{"folder":'item-variant/'});
   
	$("." + variantTarget + " .qq-upload-button").after("<div style=\"float:left; margin-left:1.5em;\"  class=\"btn btn-danger\" onClick=\"hideOverlayScreen()\">Batal</div> <div style=\"float:left; margin-left:0.5em;\" class=\"btn btn-primary\" onClick=\"updateItemImageArrayforImageVariant('" + parentTarget +"','" + variantTarget +"','" +fileInfo.token +"')\">Simpan</div>"); 
 }
 
function createImageUploaderforImageVariant(parentTarget, fileUploaderTarget,fileInfo){
	  
	var target = $("." + fileUploaderTarget);
	var parentTarget = $("#defaultForm-"+selectedTab.newPanel[0].id+ " ." + parentTarget);
	
	var fileName = target.attr("reltoken"); 
	token = fileName;
 	   
	var temp = parentTarget.find("[name=" + fileUploaderTarget + "]").val() ;
  
	if(temp != ""){		 
		var imageList =  JSON.parse(temp); 
		
		if (imageList[fileName] != undefined){
			for(i=1;i<imageList[fileName].length;i++){
				pushImageThumbforImageVariant(fileUploaderTarget,{"folder":fileInfo.folder, "token":token, "fileName":imageList[fileName][i].fileName,"phpThumbHash":imageList[fileName][i].phpThumbHash} ,imageList[fileName][i].fileColor);
			}	
		} 
	}
	 
	    
	var uploader = new qq.FileUploader({
						element: target.find('.file-uploader')[0], 
						action: 'fileuploader.php?action=upload&folder=' + fileInfo.folder + '&token='+ token, 
						allowedExtensions:['jpg','jpeg','png','gif'],
						onComplete: function(id, fileName, responseJSON){   
							if (responseJSON.success == true)
								pushImageThumbforImageVariant(fileUploaderTarget,{"folder":fileInfo.folder, "token":token, "fileName":responseJSON.fileName,"phpThumbHash":responseJSON.phpThumbHash} );  
						} 
					});   
	  			  
}
	
	
function pushImageThumbforImageVariant(fileUploaderTarget,fileInfo,colorValue){ 
   
  	var target = $("." + fileUploaderTarget);

    if (colorValue == undefined || colorValue == "")
		colorValue = "#000000";
		
	var temp = '<li relfilename="'+fileInfo.fileName+'" relPHPThumbHash ="'+fileInfo.phpThumbHash+'">';
	temp += '<div class="file-uploader-image" ><img src="../phpthumb/phpThumb.php?src='+phpConfiguration.uploadTempDoc + fileInfo.folder + fileInfo.token+ '/'+fileInfo.fileName+'&w=150&h=150&far=C&hash='+ fileInfo.phpThumbHash +'"/></div>';
	temp += '<div class="file-uploader-action-bar" style="width:40%; margin:auto;">';  
	temp += '<div style="width:50%;float:left;"><input type="color" class="form-control" style="padding:0 !important; width:2em; height:1.5em; border-radius:0;" name="variantColor[]" value="' + colorValue + '" /></div>'; 
 	temp += '<div style="width:50%;float:left; margin-top:0.2em;"><div class="delete-icon-small minerva-icon-15" onClick="deleteImageUploaderThumbforImageVariant(this,\'' + fileUploaderTarget  + '\',\'' + fileInfo.token  + '\')"></div></div>';
	temp += '</div>';
	temp += '</li>'; 
	
	target.find(".image-list").append(temp);	
	    
	target.find('.image-list li:last img').on('load', function(){
	  $(this).closest(".file-uploader-image").css("background-image","none"); 
	}); 
  		
}


function deleteImageUploaderThumbforImageVariant(obj,fileUploaderTarget,token){  
	$(obj).closest("li").remove();   
} 
 
function updateItemImageArrayforImageVariant(parentTarget,fileUploaderTarget,token){
	 var target = $("." + fileUploaderTarget); 
	 var parentTarget = $("#defaultForm-"+selectedTab.newPanel[0].id+ " ." + parentTarget);
	
	 var parentFileName = target.attr("reltoken"); 
	 
	 var arrImageVariant = parentTarget.find("[name=" + fileUploaderTarget + "]").val();
	 var fileArray = {};
	  
	 if (arrImageVariant != "")  
	   fileArray = JSON.parse(arrImageVariant);
		   
	fileArray[parentFileName] = new Array;
 	  
	 var temp;
					
	 $("." + fileUploaderTarget + " .image-list li").each(function(i) { 
			temp = new Object();
	 		temp['fileName'] = $(this).attr("relfilename"); 
			temp['fileColor'] =$(this).find('[name="variantColor[]"]').val();
            temp['phpThumbHash'] =  $(this).attr("relPHPThumbHash"); 
			 
			fileArray[parentFileName].push(temp);
	  });
	
	parentTarget.find("[name=" + fileUploaderTarget + "]").val(JSON.stringify(fileArray));
	  
	hideOverlayScreen();		
}

function onChangeCurrency(currencyObj,rateField){ 
            var tabID =  selectedTab.newPanel[0].id;
            currencyObj = $(currencyObj);
    
            if(!rateField) rateField = 'input-currency-rate';
     
            var rowObj =  currencyObj.closest(".mnv-currency");
            var currencyRateObj =  rowObj.find("." + rateField); 
            var inputNumber =  $("#"+tabID+" .mnv-input-number"); 
     
            if(currencyObj.val() == 1){ // IDR
                currencyRateObj.prop("readonly", true);
                currencyRateObj.val(1); 
                changeNumberDecimal(currencyRateObj,true);  
                changeNumberDecimal(inputNumber,true); 
                
            }else{
                currencyRateObj.prop("readonly", false);
                changeNumberDecimal(currencyRateObj,false);
                changeNumberDecimal(inputNumber,false); 
            }
     
            $("#"+tabID+" .mnv-input-number").blur();   
            $("#"+tabID+" .mnv-active-currency").html(currencyObj.find("option:selected").text());
            currencyRateObj.change().blur(); 
}

function updateAvailableCurency(){
    
}
	 
function arrayColumn(array, columnName) {
    return array.map(function(value,index) {
        return value[columnName];
    })
}

/* END OF IMAGE VARIANT */ 
function mnvOptionsRowShow($newRow){ 
     
    var formPanel  =  $newRow.find(".options-row .form-panel");
    var formPanelResult =  $newRow.find(".options-row .form-panel-result");
  
    formPanel.find("input,select,textarea").each(function() {  
        label = $(this).attr("attr-label");
        if (!label) return;

        type = $(this).attr("type");
         
         if (type == "select") 
             value = $(this).find("option:selected").text();
         else 
             value = $(this).val();


         el = $newRow.find("."+label);
         elVal = el.find("."+label+"-value").first();
         el.hide();
         elVal.html(""); 
         if(value){  
            elVal.html(value);
            el.show();
         }
         
    })     
 
    formPanelResult.show();  
}


function mnvOptionsRowOnClick(obj){
     
    $newRow = obj.closest(".transaction-detail-row");
    
    var label, type, value, el;
    var formPanelResult =  $newRow.find(".options-row .form-panel-result");
    var formPanel  =  $newRow.find(".options-row .form-panel");

    formPanelResult.hide();
    var visible = formPanel.toggle().is(":visible");
    if (!visible) 
        mnvOptionsRowShow($newRow); 

    
}

function updateTemplateRow(selector, arrValue){
    var tabID =  selectedTab.newPanel[0].id;
     
    var el = JSON.parse(arrValue);
    $("#"+tabID+" [name=\""+el[0].selector+"[]\"]").each(function(){   
        if($(this).val() == "")
            removeDetailRows($(this),true);
    });	 
      
    addNewTemplateRow(selector, arrValue);  
}

function addNewTemplateRow(selector, arrValue, rowSelector, rebindHandler, newRowPosition){
 
            //selector HARUS class, bkn id
            //rowSelector => utk obj pada row tertentu. misalnya class ".row-template" terdapat di beberapa tempat dalam satu form

            var tabID =  selectedTab.newPanel[0].id;
            var elToken = $("#defaultForm-"+tabID).find("[name='detailRowsToken[]']:enabled"); // diletakan diatas, karena baris baru blm terbentuk
        
            var groupName = '.transaction-detail';
            var newRowClass = 'transaction-detail-row';
            var $template = (rowSelector) ? rowSelector.find("." +selector) : $("#defaultForm-"+tabID+" ." + selector);
            var $newRow   = $template.clone().removeClass(selector).removeClass("row-template").addClass(newRowClass);
  
            if (newRowPosition)
                $newRow.insertAfter(newRowPosition);
            else    
                $newRow.insertBefore($template.first());

            $newRow.show(); 
			    
			 if(arrValue != undefined && arrValue.length > 0){ 
			 		var temp = JSON.parse(arrValue);  
                    var i;
			 	 	for(i=0;i<temp.length;i++) {     
                          if(jQuery.type(temp[i].value) === 'string' )  
                              temp[i].value = decodeHTMLEntities(temp[i].value); 
                        
						  $newRow.find("[name=\"" + temp[i].selector +"[]\"]").val( temp[i].value );
					}  
			  }
		 
			$newRow.find('.inputnumber').bind( "blur", function(event) { inputNumberOnBlur($(this)); });
			$newRow.find('.input-integer').bind( "blur", function(event) { inputNumberOnBlur($(this),0); });
			$newRow.find('.inputdecimal').bind( "blur", function(event) { inputNumberOnBlur($(this),2);});  
			$newRow.find('.inputautodecimal').bind( "blur", function(event) { inputNumberOnBlur($(this),-2);});  
            $newRow.find(".inputnumber, .inputdecimal, .input-integer, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
    
			$newRow.find('input, select, textarea').removeAttr("disabled tabIndex");   
		    $newRow.find('.no-tabs-index, input[readonly], select[readonly], textarea[readonly]').attr("tabIndex","-1");  // kecuali input date ?
            $newRow.find(".multi-selectbox").searchableOptionList({  maxHeight: '250px',  showSelectAll: true, showSelectionBelowList: true  });

            $newRow.find(".input-date").removeClass("hasDatepicker");
            $newRow.find(".input-date").removeAttr("id"); 
            $newRow.find(".input-date").datepicker({ currentText: 'Now', dateFormat:'dd / mm / yy', changeMonth: true,  changeYear: true});

            //set ulang, karena kalo level kedua, sudah ke enabled ketika add row 
		    $newRow.find('.row-template').find('input, select, textarea').attr("disabled",true).attr("tabIndex","-1"); 
			 
            $newRow.find('.ckeditor').each(function(){  
				var newID = new Date().getTime() + Math.floor((Math.random() * 99999) + 1); 
				$(this).attr("id",newID);
				$("#" + newID).ckeditor(); 
			});	   
    
            $newRow.find('.btn-more-options').bind( "click", function(event) { mnvOptionsRowOnClick($(this))  });
            $newRow.find(".mnv-barcode-input").keydown(function (e) {barcodeHandler(this,e); });
     
            var temp = elToken.last().val();
            token = (temp) ? parseInt(temp) + 1 : 1; 
            $newRow.find("[name='detailRowsToken[]']").val(token);
    
            var formInput = $newRow.find('.options-row .form-panel').find("input, select, textarea"); 
            formInput.addClass("label-style");
            formInput.bind("focusout", function(event) { $(this).addClass("label-style");})
                     .bind("focus", function(event) { $(this).removeClass("label-style") }); 
     
			// Set the label and field name  
			$newRow.find('.remove-button').bind( "click", function(event) { 
                removeDetailRows(this);   
                if (getTabObj() && $.isFunction(getTabObj().afterRemoveRowHandler))  
                    getTabObj().afterRemoveRowHandler() 
            });
			$newRow.find('.add-row-button').unbind('click').bind( "click", function(event) {   
                $row = addNewTemplateRow($(this).attr("attr-template"),null,$(this).closest(groupName),rebindHandler,$(this).closest("." + newRowClass) ); 
                 
                if (typeof getTabObj() !== 'undefined' && $.isFunction(getTabObj().afterAddNewTemplateRowHandler))  
                    getTabObj().afterAddNewTemplateRowHandler($row) 
            });
			  
            // utk icon navigasi 
    
            bindArrowNav($newRow);
            updateRowNumber($newRow.closest(groupName));
    
            if (rebindHandler) rebindHandler();
    
	 		updateControlLabelPadding();
            return $newRow;
}

function bindArrowNav($row){
     
    if (typeof $row === 'undefined') return;
    
    var Obj = $row.find(".arrow-nav");
    Obj.unbind('click').bind( "click", function(event) { 
        updateOrder($(this));
    });   

    Obj.unbind('hover').hover(
      function() {
         $(this).closest(".transaction-detail-row").addClass("highlight");
      }, function() {
         $(this).closest(".transaction-detail-row").removeClass("highlight");
      }
    );
 
    reorderList($row.closest(".row-panel"));
}

function inputNumberOnFocus(obj){
    
    if (obj.prop('readonly'))  return;
    if (obj.prop('disabled'))  return;
        
     if(obj.val() == 0 || !$.isNumeric(unformatCurrency(obj.val())) )
         obj.val("");
}

function inputNumberOnBlur(obj,decimal){  	 
	  if(obj.val() == "" || !$.isNumeric(unformatCurrency(obj.val())) )  
		 obj.val(0);  
    
     var tabID = selectedTab.newPanel[0].id;   
     
     // kalo ad attribute decimal, overwrite
     var attrDecimal = parseInt(obj.attr("mnv-attr-decimal")) || 0;
     if (attrDecimal != 0)   decimal= attrDecimal;
    
	  if (decimal == undefined){ 
          decimal = 0;  
       
          try {
              var tablekey = tabParam[tabID].obj.tablekey;    
              decimal = (phpModuleSetting[tablekey] == undefined) ? 0 : phpModuleSetting[tablekey].decimalnumber;   
            }  catch(err) { }  
      } else if (decimal == -2){
           decimal = (obj.val() % 1 != 0) ? 2 : 0;
      }
   
	  obj.formatCurrency({roundToDecimalPlace: decimal });
}

function loadPopup(arrParam){
    //pisahin sama loadOverlayScreen karena sudah dipake utk form quickadd
    
    $(':focus').blur();
    $("html, body").css("overflow","hidden");
    $("#popup-panel").fadeIn("fast"); 
      
    if (arrParam.content)
        $("#popup-panel .content-panel").html(arrParam.content);  
      
        
    if (arrParam.url){  
        
        $("#popup-panel .content-panel").html(""); // reset dulu
        $("#popup-panel .content-panel").load(arrParam.url);  
        
        // ini gk guna ternyata, gk kepanggil. jd handle dari form langsung
        //$("#popup-panel .close-panel").click( function(){  hideOverlayScreen();  });

//        $(".close-panel").click( alert("click"));
    }
    
    $("#popup-panel" ).on( "click", function(e) {  
//        var isInPopup = $(e.target).closest('.popup-form');
        var isInPopup = $(e.target).closest('.form-panel'); // terakhir update utk form employee commission
        
        if(isInPopup.length == 0){
            hideOverlayScreen(); 
        }
         
        
//        if ($(e.target).hasClass('content-panel')) { return; }
//        if ($(e.target).closest('.content-panel').length) { return; }
       
    });
    
    
}

function loadOverlayScreen(arrParam){      
    
    $(':focus').blur();
    $("html, body").css("overflow","hidden");
    $("#popup-panel").fadeIn("fast"); 
      
    if (arrParam.content)
        $("#popup-panel .content-panel").html(arrParam.content);  
     
    if (arrParam.url){ 
        var id = Date.now(); 
        //alert(arrParam.targetID)
        //targetTabID="+selectedTab.newPanel[0].id
        
        var valueDBField = ''; 
      
        if (arrParam.element.valueDBField)
            valueDBField = '&valueDBField=' + arrParam.element.valueDBField ;
    
        var url = arrParam.url + "?quickadd=1&targetID="+arrParam.targetID+"&keyElement="+arrParam.element.key+"&valueElement="+arrParam.element.value+valueDBField+"&revalidateField="+arrParam.revalidateField+"&tabID=" + id; 
        var headerTable = "<div class=\"header-panel div-table\"><div class=\"div-table-row\"><div class=\"title-panel div-table-col\"></div><div class=\"close-panel div-table-col\"><i class=\"fas fa-times\"></i></div></div></div>";
        
        $("#popup-panel .content-panel").html("<div id=\""+id+"\" class=\"form-panel\">" + headerTable + "<div class=\"detail-panel\"></div></div>");
        $("#popup-panel .content-panel .title-panel").html(arrParam.title);
        $("#popup-panel .content-panel .detail-panel").load(url);
        
        if (arrParam.width)
            $("#popup-panel .form-panel").css("width",arrParam.width );
         
                                          
        $("#" +id+ " .close-panel").click( function(){   
            hideOverlayScreen();     
        });
    }
}

function decreaseActiveAjaxConnections(obj){
    obj.activeAjaxConnections--; 
    if (obj.activeAjaxConnections <= 0)   hideOverlayScreen();
}

function hideOverlayScreen(){   
        $("html, body").css("overflow","inherit"); 
		$("#popup-panel").fadeOut("fast");  
} 

function disableFormSaveOnEnter(obj){
	obj.bind('keyup keypress', function(e) {
	  var code = e.keyCode || e.which;
	  if (code == 13  && !$(e.target).is("textarea")) { 
		e.preventDefault();
		return false;
	  }
	});
}
	
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, "&#039;");
}

function decodeHTMLEntities(text) {
  if (!text) return;
  if(typeof text != "string") return text; 
    
   var entities = [
        ['amp', '&'],
        ['apos', '\''],
        ['#x27', '\''],
        ['#x2F', '/'],
        ['#39', '\''],
        ['#47', '/'],
        ['lt', '<'],
        ['gt', '>'],
        ['nbsp', ' '],
        ['quot', '"']
    ];

    for (var i = 0, max = entities.length; i < max; ++i) 
        text = text.replace(new RegExp('&'+entities[i][0]+';', 'g'), entities[i][1]);

    return text;
}

function openTab(title,tabName){
    addTab(title,tabName,true,true);
}
 
function removeDetailRows(obj,alwaysDeleteRow,doNothing){ 
     
    // jaga2 karena ad yg akses langsung fungsi ini
    doNothing = (doNothing != undefined) ? doNothing : false;
    
    var transactionTable = $(obj).closest(".transaction-detail");
    $listRow = transactionTable.find(">.transaction-detail-row");
    var detailRow = $(obj).closest(".transaction-detail-row");
   
    
    //console.log('length ' + $listRow.length);
    if ($listRow.length <= 1 && !alwaysDeleteRow) {  
        detailRow.find("input").not(obj).val("").blur();
        detailRow.find("textarea").html("");
        detailRow.find("select").prop('selectedIndex', 0); 
    }  else{     
        detailRow.remove();
    }
     
    if (!doNothing) {
        // transactionTable.find("input:checkbox")
        //     .each(function () { 
        //         updateChkBoxOnClick(this); 
        // })
        transactionTable.find("input:checkbox")
            .not("[name='dummychkPick-master']") // master di kecualikan, karena kalau master unchecked, detail akan di unchecked semuanya
            .each(function () { 
                updateChkBoxOnClick(this); 
        })
    }

    updateRowNumber(transactionTable);
    reorderList(transactionTable);
    
    if(!doNothing){  
        var handler = $(obj).attr("attrhandler");  
        if (handler != undefined)
            eval(handler);
    }
}
 
function clearAllRows(formObj, removeFirstIndex, doNothing){  
     //  formObj.find(".remove-button").each(function() {   $(this).click(); })      
      removeFirstIndex = (removeFirstIndex != undefined) ? removeFirstIndex : true;
      doNothing = (doNothing != undefined) ? doNothing : false;
    
      // pake transaction-detail-row agar yg row template gk keselect
      formObj.find(".transaction-detail-row").find(".remove-button").each(function() { removeDetailRows(this,removeFirstIndex,doNothing); })      
}
 

function convertDateToStandartFormat($date){
    var parts = $date.split(' / ');
    var formatedDt = parts[2]+'-'+parts[1]+'-'+parts[0];
     
    return formatedDt;
}

function getTabObj(id){
    var tabID = (id) ? id : selectedTab.newPanel[0].id;
    return tabParam[tabID].obj;
}

function setReadonly(obj,isReadonly){
    obj.prop("readonly",isReadonly);
    
    if(isReadonly)
        obj.attr("tabindex","-1");
    else
        obj.removeAttr("tabindex");
}

function updateAvailableUnit(itemKeyObj, selUnitObj){
     
     $.ajax({
				type: "GET",
				url:  'ajax-item',
		 		data: { action : 'getAvailableUnit',
                        itemkey: itemKeyObj.val() 
                      } ,
				success: function(data){   
					
        				 if (!data) return;
				 	     var data = JSON.parse(data);
                    
                        // update combobox services
                        var newOptions = {};
                        for(i=0;i<data.length;i++)  
                            newOptions[data[i].conversionunitkey] =  data[i].unitname;       
 
                        var options = (selUnitObj.prop) ? selUnitObj.prop('options') : selUnitObj.attr('options');  

                        $('option', selUnitObj).remove();

                        $.each(newOptions, function(val, text) {
                            options[options.length] = new Option(text, val);
                        });
                    
                        // add conversion 
                        selUnitObj.find("option").each(function(i){ 
                                $(this).attr("relconversionmultiplier",data[i].conversionmultiplier);
                            } 
                        ) 

                        //selUnitObj.find('option:eq(0)').prop('selected', true).change();
                        selUnitObj.val(data[0]['deftransunitkey']);
                    
				} 
		});    
}

function updateComboboxReadonly(obj,readonlyValue){  
				
     var readonly = (readonlyValue == undefined) ? true : readonlyValue;
     obj.find("option:selected").attr('disabled', false);
     obj.find("option:not(:selected)").attr('disabled', readonly); 
}

function quickAddCallback(){}
 
function reconfirmUpdateData(tabObj, ui, target,current, opt){ 
	// gk bisa kirim $(this) karena clearAutoCompletenya pake obj, bukan $(obj)
	
	title = (opt.title != undefined ) ? opt.title : "Konfirmasi Perubahan Data";
	htmlContent = (opt.htmlContent != undefined) ? opt.htmlContent : "Merubah referensi akan mereset detail transaksi.";
	
	targetKey = target.key;
	targetValue = target.value;
	currentKey = current.key;
	currentValue = current.value;

	var obj = tabObj.find("[name="+targetValue+"]"); 
	
	if (tabObj.find("[name="+currentKey+"]" ).val() != ''){
			$( "#dialog-message" ).html(htmlContent);
			$( "#dialog-message" ).dialog({
			  width: 300,
			  modal: true,
			  title: title, 
			  open: function() {
				  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
			  },
			  close:function() {
					tabObj.find("[name="+targetKey+"]" ).val(tabObj.find("[name="+currentKey+"]" ).val());
					tabObj.find("[name="+targetValue+"]" ).val(tabObj.find("[name="+currentValue+"]" ).val());
					$(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name")); 
			  },
			  buttons : {
				  OK : function (){  
						 if (ui.item == null) { 
							clearAutoCompleteInput(obj,targetKey);	
							tabObj.find("[name="+currentKey+"]" ).val(''); 
							tabObj.find("[name="+currentValue+"]" ).val(''); 
						 }else{
							tabObj.find("[name="+currentKey+"]" ).val(ui.item.pkey); 
							tabObj.find("[name="+currentValue+"]" ).val(ui.item.value);  
						 } 

						// update ulang
					   if(opt.updateFunc != undefined) opt.updateFunc(); 
					   if(opt.rebindEl != undefined) opt.rebindEl();  // gk bisa taro dibawah karena ketrigger setelah tombol diklik
					    $(obj).focus();

						$( this ).dialog( "close" );
				  },
				  Cancel : function (){  
						$( this ).dialog( "close" );
				  }
			  },
			});	 
		}else{ 
			 if (ui.item == null) {
				clearAutoCompleteInput(obj,targetKey);	
				tabObj.find("[name="+currentKey+"]" ).val(''); 
				tabObj.find("[name="+currentValue+"]" ).val(''); 
			 }else{ 
				tabObj.find("[name="+currentKey+"]" ).val(ui.item.pkey); 
				tabObj.find("[name="+currentValue+"]" ).val(ui.item.value); 

			 } 	

			// update ulang
			if(opt.updateFunc != undefined) opt.updateFunc();

			if(opt.rebindEl != undefined) opt.rebindEl();  
			$(obj).focus();
		} 

}

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
 
function changeNumberDecimal(obj,isnumber){
    if(isnumber){
          obj.removeClass("inputdecimal").addClass("inputnumber");
          obj.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this)) });
    }else{ 
          obj.removeClass("inputnumber").addClass("inputdecimal");
          obj.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this),2) });
    }
    
    obj.blur();
}

function updateDecimal(obj,fieldName){
    
    var parentObj =  $(obj).parent().parent();

    fieldName = (fieldName) ? fieldName  :  'discountValueInUnit[]';
    var objDiscVal = parentObj.find("[name='"+fieldName+"']");
    var discType = $(obj).val();

    objDiscVal.removeClass("inputnumber").addClass("inputdecimal");

    if (discType == 1){
        objDiscVal.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this)) });
    }else{
        objDiscVal.unbind("blur").bind( "blur", function(event) {  inputNumberOnBlur($(this),2) }); 
    } 

   objDiscVal.blur(); 

}

function calculateAge(dob){
	// dob in Y-m-d 
	dob = new Date(dob); 
	var today = new Date(); 
	var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
	return age;
}

function updateFinalDiscountDecimal(obj, objDiscVal){
    
            if(!objDiscVal){ 
                var parentObj =  $(obj).parent().parent(); 
                objDiscVal = parentObj.find("[name='finalDiscount']");
            }
     
            var discType = $(obj).val();

            objDiscVal.removeClass("inputnumber").addClass("inputdecimal");

            if (discType == 1){
                objDiscVal.unbind("blur").bind( "blur", function(event) {  inputNumberOnBlur($(this)) });
            }else{
                objDiscVal.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this),2)}); 
            } 

           objDiscVal.blur();
}

function updateRowNumber(obj){    
    $(obj).each(function(index, value) { 
        var incr = 1;
        $(this).find(".row-number").each(function() { 
            $(this).html(incr+".");
            incr++;
        });
    }); 
}

function barcodeHandler(obj,e){  
   
 // You may replace `c` with whatever key you want
    /*if ((e.metaKey || e.ctrlKey) && ( String.fromCharCode(e.which).toLowerCase() === 'j') ) { 
        e.preventDefault();
        $(obj).blur();
        $(obj).focus(); 
    } */
    
    
    var keyCode = (e.which) ? e.which : e.keyCode;
     
    if (keyCode == 13){  
        $(obj).attr("isbarcode",1);
        e.preventDefault(); 
    } 
}

function updateChkBoxOnClick(obj,onChangeFunc){   
    
    //console.log("updateChkBoxOnClick");
    
    var chkValue = $(obj).prop("checked") ? 1 : 0;
    $(obj).val(chkValue); 
    $(obj).next().val(chkValue); 
    
   
    if(onChangeFunc) onChangeFunc();
    
    // testing, gk tau akan ad bug atau gk
    $(obj).next().change();
} 

function updateChkBoxOnChange(obj){   
    
    var checked = "",chkValue = 0;
    
    if($(obj).val() == 1){
        checked = "checked";
        chkValue = 1;
    } 
    
    //console.log("updateChkBoxOnChange");
    
    $(obj).prev().prop("checked",checked).change(); // dont use click !
    $(obj).prev().val(chkValue);
}

function updateChkPick(obj,onChangeFunc){ 
    var obj = $(obj);  
    var container = obj.closest(".mnv-checkbox-group");
    
    if (obj.attr("relignore")) return; 

    var chkPick = container.find("[name='chkPick[]']:enabled");
  
    chkPick.prev().attr("relignore", true); 
    chkPick.val(obj.next().val()).change(); 
    chkPick.prev().removeAttr("relignore"); 
 
    // cukup sekali, gk perlu setiap klik detail dihitung ulang 
    if(onChangeFunc) onChangeFunc();
}

this.updateChkMaster = function updateChkMaster(obj, onChangeFunc) {

    var obj = $(obj);

    var elName = obj.next().attr("name");
    var elMasterChkName = elName.replace('[]', '') + "-master";

    if (obj.attr("relignore")) {
        return;
    }

    var container = obj.closest(".mnv-checkbox-group");

    var masterHidden = container.find("[name='" + elMasterChkName + "']");
    var masterDummy = masterHidden.prev();

    // Ambil SEMUA detail checkbox yang aktif
    var enabled = container.find("[name='" + elName + "']:enabled");

    var total = enabled.length; // Total detail checkbox

    // Hitung berapa detail yang bernilai 1 / checked
    var checked = enabled.filter(function () { return $(this).val() == 1; }).length;

    var newVal = (total > 0 && checked === total) ? 1 : 0;

    masterDummy.attr("relignore", true);
    masterHidden.val(newVal);
    masterDummy.prop("checked", newVal === 1);

    masterDummy.removeAttr("relignore");

    if (onChangeFunc) onChangeFunc();
}


//
//function updateChkMaster(obj,onChangeFunc){  
//    
//    // dummy element 
//    var obj = $(obj);  
//    
//    // value element
//    var elName = obj.next().attr("name"); 
//    var elMasterChkName = obj.next().attr("name").replace('[]','')+"-master";
//    
//    if (obj.attr("relignore"))
//        return;
//
//    var container = obj.closest(".mnv-checkbox-group");
//    
//    var chkPickMaster = container.find("[name='"+elMasterChkName+"']");
//    chkPickMaster.prev().attr("relignore", true);
//
//    if (obj.val() == 0){
//        // kalo uncheck, ud pasti pickall uncheck jg
//        chkPickMaster.val(0).change(); 
//    } else{
//         var flag = true; 
//         container.find("[name='"+elName+"']:enabled").each(function(){ 
//           if ($(this).val() == 0){  
//               flag = false;
//               container.find("[name='"+elMasterChkName+"']").val(0).change();
//               return;
//           }  
//         })  
//
//        if (flag) container.find("[name='"+elMasterChkName+"']").val(1).change();
//    }
//     
//    chkPickMaster.prev().removeAttr("relignore");
//    if(onChangeFunc) onChangeFunc();
//}


function waFormat(phoneNumber){

    phoneNumber = phoneNumber.replace(' ','');
    phoneNumber = phoneNumber.replace('-','');

    if(phoneNumber.substring(0,1) == "0")
        phoneNumber = phoneNumber.substring(1, phoneNumber.length); 
 
    if(phoneNumber.substring(0,3) == "+62")
        phoneNumber = phoneNumber.substring(3, phoneNumber.length); 

    if(phoneNumber.substring(0,1) == "+")
        phoneNumber = phoneNumber.substring(1, phoneNumber.length); 

    phoneNumber = '62'+phoneNumber; 
 
    return phoneNumber;

}

function ucfirst(txt){
    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
}

function sortColumn(obj,selectedTabId){ 
    var selectedTabObj = $("#" + selectedTabId);
    var ordertype = obj.attr("reltype");

    tabParam[selectedTabId].orderby = obj.attr("relcol");
    tabParam[selectedTabId].ordertype = ordertype;

    selectedTabObj.find(".sortable").removeClass("sortable-active");
    selectedTabObj.find(".sortable .order-type").removeClass("arrow-up").removeClass("arrow-down").hide();

    obj.addClass("sortable-active");

    if (ordertype == 1)
        obj.find(".order-type:first").addClass("arrow-down").show();
    else
        obj.find(".order-type:first").addClass("arrow-up").show();


    obj.attr("reltype",ordertype * -1); 

    updateData(false);
} 

function clearText(obj,selectedTabId){ 
        var panel = obj.closest("div");

        var quickSearcObj = $("[name=quick-search-"+ selectedTabId +"]"); 
        if ( quickSearcObj.val().trim() == "") return;

        quickSearcObj.val("");  
        panel.find(".clear-text-icon").hide();
        panel.find(".search-icon").show();

        $("[name=selPage-"+selectedTabId +"] option:first").attr('selected','selected');
        updateData(false); 
}

function quickSearch(obj,selectedTabId){ 
      var searchKey = obj.val();
      var panel = obj.closest("div");
      if (searchKey){ 
          panel.find(".search-icon").hide();
          panel.find(".clear-text-icon").show();
          obj.attr("reltype",1);
      }else{
          panel.find(".clear-text-icon").hide();
          panel.find(".search-icon").show();  
      } 
    
}
 
function quickSearchOnLostFocus(obj,selectedTabId){ 
    
      // hanya jika sebelumnya pernah search
      if ( obj.attr("reltype") == 0) return;
    
      obj.attr("reltype",0);
    
      var searchKey = obj.val();
 
      if (searchKey == ""){ 
            $("[name=selPage-"+selectedTabId+"] option:first").attr('selected','selected');
            updateData(false);
      }
}

function updateDataListSettings(obj){
    var selectedTabId = selectedTab.newPanel[0].id;
    var selectedTabObj = $("#" + selectedTabId);
    var param = [];
    var phpDataListFile = tabParam[selectedTabId].phpDataListFile;

    var column = obj.find(".data-list-settings .column-preview .column");  
    column.each(function() {  

        var temp = {};
        temp['code'] = $(this).attr("relcol");
        temp['width'] = $(this).css("width");

        param.push(temp);
    });  

    $.ajax({ 
      url: phpDataListFile,  
      method : 'POST',
      data: 'action=updatecolumnheader&fileName='+tabParam[selectedTabId].phpDataListFile+'&param='+JSON.stringify(param),  
      success: function(data){   
          
           selectedTabObj.find(".data-list").remove();
          
           var dataListSettingsObj = selectedTabObj.find(".data-list-settings");
           dataListSettingsObj.addClass('border-green text-green-avocado');
           dataListSettingsObj.css('text-align','center');

           var message  = '<i class="fas fa-check"></i> ' + phpLang.settingsSaved +' '+ phpLang.pleaseReopenThisTab ;
           dataListSettingsObj.html(message); 
      } 
    }); 
}        

function refreshDataList(obj,selectedTabId, syncMarketplace ){ 
	
//	obj.addClass("fa-spin");
    phpDataListFile = tabParam[selectedTabId].phpDataListFile;
    targetContent = obj.closest(".panel-data-list").find(".data-list"); 
    targetContent.html(_LOADING_ICON_);	

    //import from marketplace   
    if (phpDataListFile == 'salesOrderList'){ 
        
        var marketplaceURL = (syncMarketplace == true) ? '/cron/updateMarketplaceOrders.php' : '/cron/updateMarketplaceOnRrefresh.php';
        $.ajax({
            type: "POST",
            url:  marketplaceURL,
            asyn : false 
        }); 
    } 
    
    updateData(false);
}

function updatePreview(obj){ 

            var previewColumnHeader = '';

            var dataListSettinsgObj = obj.closest(".data-list-settings"); 
            var optionRowObj = obj.closest(".div-table-row");

            var checked =  optionRowObj.find("[name='chkTitle[]']").val();
            var title = optionRowObj.find("[name='title[]']").val();
            var relcol = optionRowObj.find("[name='hidCode[]']").val();
            var width = optionRowObj.find("[name='width[]']").val();
            var previewColumn = dataListSettinsgObj.find(".column-preview");

            var checkedList = []; 
            dataListSettinsgObj.find("[name='chkTitle[]']").each(function() {  
                    if ($(this).val() == 1){ 
                        var selectedCol = $(this).closest(".div-table-row").find("[name='hidCode[]']").val();
                        checkedList.push(selectedCol);
                    }
            });
    
            //remove jg column yg gk terdaftar
            previewColumn.find(".column").each(function() {   
                var col  = $(this).attr("relcol"); 
                if (jQuery.inArray(col,checkedList) == -1) {   $(this).remove();  }
            });

            var columnFound = false;
            previewColumn.find(".column").each(function() {  
                if ($(this).attr("relcol") == relcol){  
                    $(this).css("width",width+"px");
                    columnFound = true;
                    return;
                }
            });

            if (checked == 1 && !columnFound){ 
                 previewColumnHeader = '<div class="div-table-col-5 column" style="width: '+width+'px" relcol="'+relcol+'">'+title+'</div>';
                 previewColumn.append(previewColumnHeader);
            }

            // adjust width
            var maxWidth = 0;
            var maxObj = '';
            previewColumn.find(".column").each(function() {    
                var colWidth = parseInt($(this).width());
                if ( colWidth > maxWidth){ 
                    maxWidth = colWidth;
                    maxObj = $(this);
                }
            });

            maxObj.css("width","auto");

           /* var previewColumnHeader = '';
            obj.find(".data-list-settings [name='chkTitle[]']").each(function() {  
                if($(this).val() == false) return;

                var title = $(this).closest(".div-table-row").find("[name='title[]']").val();
                var width = $(this).closest(".div-table-row").find("[name='width[]']").val();

                previewColumnHeader += '<div class="column" style="width: '+width+'px">'+title+'</div>';
            });

            obj.find(".data-list-settings .column-preview").html(previewColumnHeader);*/
}

function standardizeRegistrationNumber(str){
    return str.replace(/\s+/g, '').toUpperCase();
}

// ===== SN HANDLER

 function SNOptHander(tabObj, obj, snRegex){

        var row = $(obj).closest(".transaction-detail-row");  
        var formPanel = row.find(".form-panel");

        if (formPanel.is(":visible")) {
            var hasValue = false;
            $varSN = row.find("[name='snList[]']").val();
            var list = $varSN.split(snRegex);

            $varSN = '';
            if (list.length > 0 ){
                $varSN = "<ul class=\"tag-list\">";

                for(i=0;i<list.length;i++){ 
                    if (list[i].length == 0)
                        continue;

                    $varSN += '<li>'+list[i]+'</li>';
                    hasValue = true;
                }

                $varSN += "<ul>";
            }

            if(hasValue) 
                row.find(".form-panel-result").html($varSN).show();
            else
                row.find(".form-panel-result").html("").hide(); 
        }


        calculateSNNeeded(tabObj, row);

    }

    function updateSNOptions(tabObj,row){

        var fullReceive =  tabObj.find("[name=chkIsFullReceive]").val();

        if(fullReceive == 0){
            tabObj.find(".btn-sn-options").prop("disabled",true); 
            tabObj.find(".options-row").hide(); 
        }else{ 

            var rowList = (row) ? row :  tabObj.find(".transaction-detail-row");
            rowList.each(function(){  
                var useSN = $(this).find("[name=\"hidNeedSN[]\"]").val();  
                if(useSN == 1){ 
                    $(this).find(".btn-sn-options").prop("disabled",false);  
                    $(this).find(".options-row").show(); 
                    calculateSNNeeded(tabObj, $(this));
                }else{ 
                    $(this).find(".btn-sn-options").prop("disabled",true);  
                    $(this).find(".options-row").hide(); 
                }
            })
        }
     }
        
   function calculateSNNeeded(tabObj, target){
        if(target)  
           detailRow =  ( target.nodeType )  ? $(target).closest(".transaction-detail-row") : target;
        else
           detailRow =  tabObj.find(".transaction-detail-row");

        detailRow.each(function(){   
                if ($(this).find("[name='hidNeedSN[]']").val() != 1 || $(this).find(".options-row").is(":visible") == false)
                    return;

                $(this).find(".total-sn-label").show();
                disabledButton($(this).find("[name=btnMoreOptions]"),false);
                $(this).find(".options-row").show();

                totalQty = $(this).find("[name='qty[]']").val();
                totalQty = unformatCurrency(totalQty);

                totalSN = $(this).find(".tag-list li").length; 
                remaining = totalSN-totalQty;

                $(this).find(".total-sn-remaining").html(remaining);
                $(this).find(".total-sn-label").removeClass("text-red-cardinal text-blue-munsell");
                if(remaining < 0)
                    $(this).find(".total-sn-label").addClass("text-red-cardinal");
                else if(remaining > 0)
                    $(this).find(".total-sn-label").addClass("text-blue-munsell");                      
        })             
    } 

// ===== SN


this.customCodeHandler = function customCodeHandler(obj){ 
    var tabObj = obj.tabObj; 
    var selCustomCodeObj = tabObj.find("[name=selCustomCode]");  
    var codeObj = tabObj.find("[name=code]");  
    
    obj.customCodeCache[selCustomCodeObj.val()] = codeObj.val(); 
    selCustomCodeObj.change(function(){onCustomCodeChange(obj, tabObj)}); 
    selCustomCodeObj.change();

    codeObj.change(function(){updateCustomCodeCache(obj, tabObj)});  
}            

this.updateCustomCodeCache = function updateCustomCodeCache(obj, tabObj){ 
    var selCustomCodeObj = tabObj.find("[name=selCustomCode]");  
    var codeObj = tabObj.find("[name=code]");  
    obj.customCodeCache[selCustomCodeObj.val()] = codeObj.val();
}
 
this.onCustomCodeChange = function onCustomCodeChange(obj, tabObj){  
        var selCustomCodeObj = tabObj.find("[name=selCustomCode]");  
        var codeObj = tabObj.find("[name=code]");  
    
        var customcodekey = selCustomCodeObj.val(); 
        var oldCode = '';

        $.ajax({
            type: "GET",
            url:  'ajax-custom-code.php',  
            asyn: false,
            data: 'action=getDataRowById&pkey=' + customcodekey, 
            success: function(data){    
                var autoCodeText = "[auto code]";
                var data = JSON.parse(data);   
                data = data[0];

                oldCode = (obj.customCodeCache[customcodekey]) ? obj.customCodeCache[customcodekey] : '' ;

                // harus simpan kode sebelumnya... 
                if(data.useautocode == 1){ 
                    var tempCode = (oldCode) ? oldCode : autoCodeText;
                    codeObj.val(tempCode); 
                    codeObj.prop("readonly", true); 
                    selCustomCodeObj.closest('form').bootstrapValidator('revalidateField', 'code');   
                }else{ 
                    // sementara
                    if (oldCode == autoCodeText) oldCode = ""; 
                    codeObj.val(oldCode);
                    codeObj.prop("readonly",false);
                }
            }  
        });     
}

function multiLang(tabObj){ 
    bindEl(tabObj.find("[name=mnvSelLang]"),'change',function(){ 
         
      if($(this).val() == 0){ 
          tabObj.find("div[rellang]").show(); 
          tabObj.find("div[rellang]:not(:first-child)").addClass("margin-top-05");
      }else{  
          tabObj.find("div[rellang]").removeClass("margin-top-05").hide(); 
          tabObj.find("div[rellang="+$(this).val()+"]").show(); 
      } 
    });
}
 
function parseJSON(data){ 
	
	data = $.trim(data);
	
	if(!data) data = '[]'; 
	if(data.length == 0) data = '[]'; 
	 
	return JSON.parse(data);
}

function isMobile(){  
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) { 
      return true;
    } 
    return false;
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
    
    /*
    //if ($.isArray(selectbox)){  
    if(selectbox.length > 1)
        console.log("test");
        selectbox.each(function(){ updateSelectBox($(this),newOptions); }); 
    }else{ 
        console.log("test 123");
        updateSelectBox(selectbox,newOptions);
    }*/
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
    // pindahin keatas agar sekali change aj cukup yg pertama
    //select.find('option:eq(0)').prop('selected', true).change(); 
}

function updateSelectBoxSelectedVal(selObj, val){ 
            selObj.val(val);
            selObj.find("option").removeAttr("selected");
            selObj.find("option[value='"+val+"']").attr("selected", "selected");
}

function updateSaveButtonSticky(tabObj){
        var margin = 18;
        var savePanel = tabObj.find(".form-button-panel");
        var footerPanel = tabObj.find(".footer-panel");

        if (footerPanel.hasClass("sticky")){
            var height = footerPanel.height(); 
            height += margin;
            savePanel.css("bottom",height+"px"); 
        }else{
            savePanel.css("bottom","0"); 
        }
}

function getInvoiceRoundedTax(val, roundType){
    if (typeof roundType === 'undefined' ) return val;
    
    	switch(roundType) { 
                case 1:  return val; break;
                case 2:  return Math.ceil(val); break;
                case 3:  return Math.floor(val); break; 
                default: return Math.round(val); break; // agar sama seperti Baseclass
        }
    
}

function onSubmitFileAjax(tabObj,opt){
    
            var pkey =  parseInt(tabObj.find("[name=hidId]").val()) || 0;
            if(pkey == 0) return;
               
            var divForm = (typeof opt.divForm !== 'undefined') ? opt.divForm : '#file-update-ajax';
    
            if((typeof opt.ajaxFile !== 'undefined')){
                ajaxFile =  opt.ajaxFile;
            }else{
                return;
            }
    
            var ajaxFile = '';
            if((typeof opt.ajaxFile !== 'undefined')){
                ajaxFile =  opt.ajaxFile;
            }else{
                return;
            }
             
            var action = 'updateFileAjax';
            if((typeof opt.action !== 'undefined')){
                action =  opt.action;
            } 
            
            var divPanel = tabObj.find(divForm);
            // sementara cari jenis button, harusnya cuma 1
            var inputButton = divPanel.find("button");
    
            var formData = new FormData();
            formData.append('action', action);
            formData.append('pkey', pkey);
    
              // Loop through all inputs inside the div
            divPanel.find('input').each(function (index, input) {
                var $input = $(input);
                  
                var name = $input.attr('name');
  
                if (input.type === 'file') {
                  if (input.files.length > 0) {
                    for (i = 0; i < input.files.length; i++) {
                      formData.append(name, input.files[i]);
                    }
                  } else {
                    // Append an empty File object so PHP sees it
                    var emptyFile = new File([""], "", { type: "application/octet-stream" });
                    formData.append(name, emptyFile);
                  }
                } else {
                  formData.append(name, $input.val());
                }
                  
              });

                $.ajax({
                  url: ajaxFile,       // Your PHP file
                  type: 'POST',
                  data: formData,
                  contentType: false,      // Important: tell jQuery not to set contentType
                  processData: false,      // Important: tell jQuery not to process the data
                  success: function (data) {
                        data = parseJSON(data); 
                        if(data.length == 0) alert("Update Error");
 
                        alert(data[0].message);
                      
                        if(data[0].valid == false){
                        }else{
                            inputButton.hide();
                        }
                  },
                  error: function () {
//                    console.error('Upload failed.');
                  }
                });
        }


	 function reorderList(arrObj){
         
			// per scope panel
            if (typeof arrObj === "undefined") return;
                //arrObj = tabObj.find(".row-panel");

            $.each(arrObj, function( index, value ) {
                
                var i = 1;
                var obj = $(this);	 
                
                obj.find(".arrow-nav").removeClass("disabled");

                obj.children(".transaction-detail-row").first().find(".arrow-nav").first().addClass("disabled");   
                obj.children(".transaction-detail-row").last().find(".arrow-nav").last().addClass("disabled");   
 
                // blm tau masalah gk kalo beda panel
                var numberCtrObjName = obj.find(".arrow-nav").first().attr("rel-ctr"); 
                if(typeof numberCtrObjName !== 'undefined'){
                    obj.find("[name=\"" +numberCtrObjName+ "\"]").each(function( index ) {
                        $(this).val(i++);
                    }); 
                }
                
                // pake class karena setiap section nama DOM nya berbeda
                i = 1;
                obj.find(".hid-order-list").each(function( index ) {
                    $(this).val(i++);
                });
            });
    }
		
    function updateOrder(obj){
			var orderObj = obj.closest("div").find(".hid-order-list");
			var row = obj.closest(".transaction-detail-row");
			var panel = obj.closest(".row-panel");
			var rowBefore = row.prev();
			var rowAfter = row.next();

			var currOrder = row.find(".hid-order-list").val();
			var totalDetail = panel.find(".hid-order-list").length - 1;

			if(obj.attr("rel") < 0){ 
				if(currOrder == 1) return;
				row.insertBefore(rowBefore); 
			}else{ 
				if(currOrder == totalDetail) return;
				row.insertAfter(rowAfter);  
			}
 
			reorderList([panel]);
		}
//
//    function openSnapshotDB() {
//
//      if (localSnapshotDB) {
//        console.log("DB already initialized !");
//        return;
//      }
//
//      const request = indexedDB.open("offlineSnapshotDB", 1);
//
//      request.onupgradeneeded = function (event) {
//        const database = event.target.result;
//
//        if (!database.objectStoreNames.contains("state")) {
//          database.createObjectStore("state", { keyPath: "id" });
//        }
//      };
//
//      request.onsuccess = function (event) {
//        localSnapshotDB = event.target.result;
//        console.log("IndexedDB ready");
//      };
//
//      request.onerror = function () {
//        console.error("IndexedDB failed to open");
//      };
//    }

function openSnapshotDB() {
    return new Promise(function (resolve, reject) {
        // If already initialized, resolve immediately
        if (window.localSnapshotDB) {
            console.log("DB already initialized!");
            return resolve(window.localSnapshotDB);
        }

        var request = indexedDB.open("offlineSnapshotDB", 1);

        request.onupgradeneeded = function (event) {
            var database = event.target.result;

            if (!database.objectStoreNames.contains("state")) {
                database.createObjectStore("state", { keyPath: "id" });
            }
        };

        request.onsuccess = function (event) {
            window.localSnapshotDB = event.target.result;
            console.log("IndexedDB ready");
            resolve(window.localSnapshotDB);
        };

        request.onerror = function (event) {
            console.error("IndexedDB failed to open");
            reject(event.target.error);
        };
    });
}


function saveSnapshot() {
      return new Promise(function(resolve, reject) {
            openSnapshotDB().then(function(db) { 
                try{

                    //if (!localSnapshotDB) {
                    //    console.error("DB not ready");
                    //    return;
                    //}

                    // kalo yg dibuka bkn FORM
                    var activeFormPanel = $("#tabs ul li").eq(getSelectedTabIndex()).attr("aria-controls"); 
                    const panel = $("#" + activeFormPanel).find(".tab-panel-form"); 
                    if (panel.length === 0)   return; // element does not exist

                    // id gk bisa pake selectedIndex karena bisa sama ketika buka tab
                    // pake code + pkey jika ada, jika gk ad pake mktime aj
                    var dataPkey = panel.find("[name=hidId]").val() || 0;
                    var dataCode = panel.find("[name=code]").val() || '';
                    var reftablekey = panel.find("[name=reftablekey]").val() || 0;

                    if (reftablekey == '' || reftablekey == 0) return ; // harus ad reftable


                    const html = panel.html();
                    const tx = localSnapshotDB.transaction("state", "readwrite");
                    const store = tx.objectStore("state");

                    var title = selectedTab.newTab[0].textContent;
                    var selectedTabId = selectedTab.newPanel[0].id; 


                    // kalo data baru, buat random token
                    //var index = (dataPkey != 0) ? dataCode+'-'+dataPkey : Date.now().toString(36) + Math.random().toString(36).substr(2);
                    //console.log(index);
                    store.put({
                        id: dataPkey,
                        code: dataCode,
                        reftablekey: reftablekey,
                        title: title,
                        html: html,
                        savedAt: Date.now()
                    });

                    tx.oncomplete = function () {
                      localSnapshotDB.close();
                      localSnapshotDB = null; // ✅ IMPORTANT 
                      resolve(dataPkey);    
                      console.log("HTML saved"); 
                    };

                    tx.onerror = function (e) {
                         localSnapshotDB.close();
                         localSnapshotDB = null; // ✅ IMPORTANT
                         reject(e);              
                         console.log("Transaction error");
                    };

                    tx.onabort = function (e) {
                        localSnapshotDB.close();
                        localSnapshotDB = null; // ✅ IMPORTANT
                        reject(e);              
                        console.log("Transaction aborted");
                    };

                } catch (e) {
                    reject(e);
                    console.error("Error occurred:", e.message);
                }

            }).catch(function(err) {
                reject(err);
                console.error("Failed to open DB:", err);
            });
      });
}

function restoreSnapshot() {
    
    openSnapshotDB().then(function(db) {
    
        const tx = localSnapshotDB.transaction("state", "readonly");
        const store = tx.objectStore("state");
        //const index = store.index("savedAt");

         const getAllReq = store.getAll();
         getAllReq.onsuccess = function () {
            console.log("All data:", getAllReq.result);

            localSnapshotDB.close();
            localSnapshotDB = null; // ✅ IMPORTANT
        };

          // Failure handler
        getAllReq.onerror = function (event) {
            console.error("Failed to read data from IndexedDB:", event.target.error);
            
            localSnapshotDB.close();
            localSnapshotDB = null; // ✅ IMPORTANT
        };
        
        var selectedTabId = 0;
        addTab("<i class=\"far fa-file-alt title-icon\" ></i>TEST DRAFT" ,"/admin/cashOutDraftForm?restore-id=ui-id-23&title=testdraft&fileName=cashOutList&selectedPanelId="+selectedTabId); 
  
    }).catch(function(err) {
        console.error("Failed to open DB:", err);
    });
    
     
}

function deleteSnapshot(pkey,reftablekey){
    
    const tx = localSnapshotDB.transaction("state", "readwrite");
    const store = tx.objectStore("state");
    
    store.openCursor().onsuccess = function (e) {
            var cursor = e.target.result;
            if (!cursor)    return; 

            if (
                cursor.value.id === pkey &&
                cursor.value.reftablekey === reftablekey
            ) {
                cursor.delete();
                return; // stop here (delete only first match)
            }

            cursor.continue();
        };

}

/* this is the custom selectmenu widget extension add multiline and css theming support */
var multilineSelectmenu = $.widget("ui.multilineSelectmenu", $.ui.selectmenu, {
	_setText: function (element, value) {
    	if (value) {
			if (value.indexOf('\n') !== -1) {
				var lines = value.split('\n');
				value = '<span class="ui-selectmenu-menu-item-header">' + lines[0].trim() + '</span>'; 
				for (var i = 1; i < lines.length; i++) {
					value = value + '<span class="ui-selectmenu-menu-item-content">' + lines[i].trim() + '</span>'; 
				}
			} 
			element.html(value);
		} else {
			element.html("&#160;");
		}
	}
});

jQuery.fn.animateAuto = function(prop, speed, callback){
    var elem, height, width;
    return this.each(function(i, el){
        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
        height = elem.height() - 50, // gk tau kenapa lebih tinggi dr auto nya
        //height = elem.css("height"),
        //width = elem.css("width"),
        elem.remove();
        
        if(prop === "height")
            el.animate({"height":height}, speed, callback);
       /* else if(prop === "width")
            el.animate({"width":width}, speed, callback);  
        else if(prop === "both")
            el.animate({"width":width,"height":height}, speed, callback);*/
    });  
}
