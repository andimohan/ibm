function Dashboard( arrWidgets, arrNotificationPanel){
	
var thisObj = this;  
//var tabObj = $("#" + tabID);      

this.updateWidgetOrder =  function updateWidgetOrder(){}  
    
this.updateCheckedWidgets = function updateCheckedWidgets(){
    var dashboard = $("#dashboard");  
    var dashboardSettings = dashboard.find(".dashboard-settings");
     
    dashboardSettings.find('[name^=chkWidget-]').val(0).change();
      
    dashboard.find('.dashboard-stage').each(function() {  
        var widgetkey = $(this).attr("relkey");  
        dashboardSettings.find('[name=chkWidget-'+widgetkey+']').val(1).change(); // gk ngerti kenapa pake klik gk bisa
    });
}
    
this.refreshDashboardPanel = function refreshDashboardPanel(firstLoad){
        
        var dashboard = $("#dashboard");
	 
		dashboard.find('.refresh-graph i').addClass("fa-spin");
	
        firstLoad = (firstLoad) ? firstLoad : false;
      
        var width,height,additionalClass;
         
        if(firstLoad){ 
            $("#widgets-panel").html(""); 
            var tempFlag = true ;
            
            for (var j=0; j<arrWidgets.length; j++) {   
                width =  'width:' + ((arrWidgets[j]['width']) ? arrWidgets[j]['width'] : '100%') + ';';
                height =  'height:' + ((arrWidgets[j]['height']) ? arrWidgets[j]['height'] : '250px') + ';';
                additionalClass = (arrWidgets[j]['additionalClass']) ? arrWidgets[j]['additionalClass'] : '';
                additionalStyle = (arrWidgets[j]['additionalStyle']) ? arrWidgets[j]['additionalStyle'] : '';
                 
                //sementara
                if (tempFlag && arrWidgets[j]['height'] != "auto"){
                    tempFlag = !tempFlag;
                    $("#widgets-panel").append('<div style="clear:both"></div>');
                }
                
                $("#widgets-panel").append('<div class="dashboard-stage" style="'+width+height+'" relkey="'+arrWidgets[j]['pkey']+'"><div class="dashboard-panel '+arrWidgets[j]['panel']+' '+additionalClass+' " style="'+additionalStyle+'"></div></div>');  
            }   
        } 
    
        thisObj.updateAllPanels(arrWidgets);
          
}    
  
this.updateAllPanels = function updateAllPanels(arrWidgets){
     var dashboard = $("#dashboard"); 
    
    var data = []; 
    
    for (var j=0; j<arrWidgets.length; j++) { 
        var panelObj = arrWidgets[j];
        
        dashboard.find('.'+panelObj.panel).html(_LOADING_ICON_);  
        //var dataWidget = 'action=' + panelObj.panel+ '&startPeriod='+ $("#dashboard [name=trStartPeriod]").val()+ '&endPeriod='+ $("#dashboard [name=trEndPeriod]").val();
        
        var dataWidget = {};
        
        dataWidget['action'] = panelObj.panel;
        dataWidget['startPeriod'] = $("#dashboard [name=trStartPeriod]").val();
        dataWidget['endPeriod'] = $("#dashboard [name=trEndPeriod]").val();
        dataWidget['warehousekey'] = $("#dashboard [name=selWarehouse]").find(":selected").val();
        
        data.push(dataWidget);
     }
    
       
    $.ajax({
        type: "POST",
        url: "getDashboardData.php",
        data:  {data:data},
        success: function(data){    
                    if (!data) return;
                    var widgetData = JSON.parse(data);
             
                    for (var j=0; j<arrWidgets.length; j++) { 
                        var panelObj = arrWidgets[j]; 
                        var content =  widgetData[panelObj.panel];
                        thisObj.loadPanelData(content,panelObj) ;  
                    } 
             
					dashboard.find('.refresh-graph i').removeClass("fa-spin");
			
                    columnConform('.auto-height');  
                } 
    });  
}
    
this.updatePanel = function updatePanel(widgetkey){  
        
    var dashboard = $("#dashboard"); 
    
    var data = []; 
	var panelObj = null;
	
	// cari berdasarkan key nya, widget index ke berapa
	for(i=0;i<arrWidgets.length;i++){
		if(arrWidgets[i].pkey == widgetkey){
		   panelObj = arrWidgets[i];
		   break;
		  }
	}
    
    dashboard.find('.'+panelObj.panel).html(_LOADING_ICON_);  
      
	var dataWidget = {}; 
	dataWidget['action'] = panelObj.panel;
	dataWidget['startPeriod'] = $("#dashboard [name=trStartPeriod]").val();
	dataWidget['endPeriod'] = $("#dashboard [name=trEndPeriod]").val(); 
    dataWidget['warehousekey'] = $("#dashboard [name=selWarehouse]").find(":selected").val();
	data.push(dataWidget);
				 
    $.ajax({
        type: "POST",
        url: "getDashboardData.php",
        data:  {data:data},
        success: function(data){          
                    if (!data) return;
                    var widgetData = JSON.parse(data); 
					var content =  widgetData[panelObj.panel];
					thisObj.loadPanelData(content,panelObj) ;  
 
					dashboard.find('.refresh-graph i').removeClass("fa-spin");
                } 
    });  
    
}
         
this.updateWidget = function updateWidget(){
	  	var dashboard = $("#dashboard");  
	    var formObj = dashboard.find("[name=form-widget-dashboard-setting]"); 
	  	var param =  formObj.serialize() ; 
		$.ajax({
				type: "POST",
				url:  'ajax-widget-setting.php', 
				data: param + "&action=updateSettings",  
			}).done(function( data ) {   
	    			dashboard.find(".dashboard-settings").hide();  
                    arrWidgets = JSON.parse(data); 
                    thisObj.refreshDashboardPanel(true); 
			});   

 }         

this.updateWidgetProperties = function updateWidgetProperties(obj){
	  	var objPanel = obj.closest('.dashboard-panel');
        var typePanel = objPanel.find(".opt-type-panel");
        
	    var formObj = objPanel.find("[name=form-widget-setting]"); 
		var widgetkey = formObj.find("[name=hidPanelKey]").val();
     
        // update total div per section
        typePanel.each(function(){ 
            var relName = $(this).attr("rel-opt");
            var relClass = $(this).attr("rel-class");
            var elName = 'hidTotalOpt-'+relClass+'-'+relName; 
            var totalSelected = $(this).find(".opt-item .item.selected").length || 0; 
             
            $(this).find('[name=\"'+elName+'\"]').remove();
             
            $('<input>').attr({
                type: 'hidden', 
                name: elName,
                value: totalSelected
            }).appendTo($(this));

        });
     
		$.ajax({
				type: "POST",
				url:  'ajax-widget-setting.php', 
				data: formObj.serialize() 
			}).done(function( data ) {    
					// balikin lg ke asalnya agar gk hilang ketika generate ulang graph
 					thisObj.resetWidgetSettingLayer();			
					thisObj.updatePanel(widgetkey);
			});   

 }

this.loadPanelData = function loadPanelData(data,panelObj){  
    
    var dashboard = $("#dashboard"); 
    var dashboardPanel = dashboard.find('.'+panelObj.panel); 
    
    if (!data) { 
        dashboardPanel.html("<div class=\"text-muted\" style=\"text-align:center; padding: 0.5em\" >"+panelObj.title+"</div>");
        return;  
    }
    
    dashboardPanel.html(data);
       
	dashboard.find('.'+panelObj.panel).find(".btn-widget-setting").bind("click", function( event ) { 
       // open layer properties
		var thisPanel = $(this).closest('.dashboard-panel'); 
		var widgetkey = thisPanel.closest(".dashboard-stage").attr("relkey"); 
		
	 	var layer = dashboard.find('.widget-setting'); 
		layer.find("[name=hidPanelKey]").val(widgetkey);
		layer.find(".properties-table").empty();
		
		// load attribute
		$.ajax({
            type: "POST",
            url: "ajax-widget-setting.php",
            data: 'action=getPropertiesValue&widgetkey='+ widgetkey, 
            success: function(data){        
                      if (!data) return; 
					  data = JSON.parse(data); 
					  thisObj.updatePropertiesList(layer.find(".properties-table"),data); 
                    }
        });  
		
		
		layer.appendTo(thisPanel).show();
    })  
	
    dashboard.find('.'+panelObj.panel).find(".remove-widget").bind("click", function( event ) { 
        var removedWidget = $(this).closest(".dashboard-stage");
         $.ajax({
            type: "POST",
            url: "ajax-widget-setting.php",
            data: 'action=removeWidget&widgetkey='+ removedWidget.attr("relkey"), 
            success: function(data){       
                        removedWidget.remove();
                    }
        });  
    })  
 
} 

this.createProperties = function createProperties(propertiesType,dataRow, value){
 
	var inputObj = '';
    var widgetPanel = '#dashboard .widget-setting'; // sementara hardcode
	var optList = JSON.parse(dataRow.opt);   
    var propertiesName = dataRow.properties;
	var elName = propertiesName+'[]'; 
     
//    if(dataRow.subOpt != undefined)
//        elName = propertiesName+'-'+dataRow.subOpt+'[]'; 
    
	switch(propertiesType){ 
		   // kalo jenisnya opsi dataset 
		   case 'dataset' :  
							 var optionList = optList.dataset;  
							 inputObj += '<div class="opt-item flex">';
							 for(j=0;j<optionList.length;j++){ 
								 var selected = '';
								 var optValue = ''; 
								 if($.inArray(optionList[j]['key'], value) !== -1){
									selected ='selected';
									optValue = optionList[j]['key'];
								 }

								 inputObj += '<div class="user-select-none item '+selected+'" rel-key="'+optionList[j]['key']+'"><input name="'+elName+'" type="hidden" value="'+optValue+'"/>'+optionList[j]['label']+'</div>'; 
							 }

							 inputObj += '</div>';
					 				
							break;
			case 'func' :  
					$.ajax({
							type: "POST",
							url: "ajax-widget-setting.php",
							async: false,
							data:  {action:"getPropertiesByFunc", key:optList.func},
							success: function(optionList){     
										if (!optionList) return;
										   
										optionList =  JSON.parse(optionList);   
										inputObj += '<div class="opt-item flex">';
										for(j=0;j<optionList.length;j++){ 
											 var selected = '';
											 var optValue = ''; 
											 if($.inArray(optionList[j]['key'], value) !== -1){
												selected ='selected';
												optValue = optionList[j]['key'];
											 }

											 inputObj += '<div class="item '+selected+'" rel-key="'+optionList[j]['key']+'"><input name="'+elName+'" type="hidden" value="'+optValue+'"/>'+optionList[j]['label']+'</div>';
										 }

										 inputObj += '</div>';
									} 
						});  
				    break;
			
			
			case 'select-opt' : 
					// insert selectbox dan onchange
					var selOpt = optList['select-opt'];
			 
					var optList = '';  
					var divType = '';
			
					$.each(selOpt, function(key, listValue) {       
                        
					 	optList += '<option value="'+key+'" '+((value != null  && value.typekey == key) ? 'selected' : '')+'>'+listValue['label']+'</option>';   
                
                        dataRow.opt= JSON.stringify(listValue);   
                        dataRow.subOpt = key;
                         
                       	var newValue = value;
                        try  {  
                                newValue = value.value[key];
                         }  catch(err) {
                                newValue = value;
                         }    
 
                             
                        var divContent = thisObj.createProperties(listValue.type,dataRow,newValue);  
						divType += '<div rel-class='+propertiesName+' rel-opt="'+key+'" class="opt-type-panel" style="margin-top:1em">'+divContent+'</div>'; 
                        
					}); 
				
					var selectBox =  '<select name="selOptType-'+propertiesName+'">'+optList+'</select>'; 
					inputObj += '<div class="'+propertiesName+'">'+selectBox+divType+'</div>';
             	    inputObj += '<script>';
                    inputObj += 'var selectObj = $(\''+widgetPanel+' [name=selOptType-'+propertiesName+']\');';
                    inputObj += 'selectObj.change(function(){  var dashboard = new Dashboard(); dashboard.onChangeWidgetAttrType($(this)) });';
                    inputObj += 'selectObj.change();';
                    inputObj += '</script>';
             
				break;
			
             case 'database' : 
                    // blm ad scritpt hapus pkey, kalo valuenya kosong
					// insert selectbox dan onchange 
                    inputObj += '<div><ul id="widget-autocomplete" class="auto-complete flex-container"><li class="textbox flex-item"><input autocomplete="off" name="dummy'+propertiesName+'" type="text" class="form-control ui-autocomplete-input" value="" placeholder="Silahkan mulai mengetik ......"><input name="'+propertiesName+'" class="form-control  " value="" type="hidden"><i class="fab fa-sistrix"></i></li></ul></div>';
                    inputObj += '<script type="text/javascript">'; 
                    inputObj += '$(function() {';
                    inputObj += '$( "#widget-autocomplete [name=\'dummy'+propertiesName+'\']" ).autocomplete({'; 
                    inputObj += 'source: function(request,response){$.getJSON("ajax-customer.php?action=searchData&limit=25",request,response)} ,'; 
                    inputObj += 'minLength: 1,';
                    inputObj += 'autoFocus: true,';
                    inputObj += ' async : false, ';
                    inputObj += ' select: function( event, ui ) {   ';    
                    inputObj += ' $("#widget-autocomplete [name=\''+propertiesName+'\']" ).val(ui.item.pkey);  ';
                    inputObj += ' }, ';  
                    inputObj += 'change: function( event, ui ) {   ';
                    inputObj += ' if (ui.item == null) { ';
                    inputObj += '';
                    inputObj += '}else{ ';
                    inputObj += ' event.preventDefault(); ';
                    inputObj += '  $("#widget-autocomplete [name=\''+propertiesName+'\']" ).val(ui.item.pkey); ';
                    inputObj += ' } ';
                    inputObj += '},';    
                    inputObj +=  '}).change(function() {     ';
                    inputObj +=  ' if ($(this).val() == "") { ';
                    inputObj +=  '';
                    inputObj +=  '} '; 
                    inputObj +=  '}); '; 
                    inputObj +=  '}); ';
                    inputObj +=  '</script>';
				break;
			
			
			case 'select' : 
					// insert selectbox dan onchange
					var selOpt = optList['select'];
			 
					var optList = '';
					$.each(selOpt, function(key, label) {        
					 	optList += '<option value="'+key+'" '+((value != null  && value == key) ? 'selected' : '')+'>'+label+'</option>';   
					}); 
				
					var selectBox =  '<select name="'+propertiesName+'">'+optList+'</select>'; 
					inputObj += '<div class="'+propertiesName+'">'+selectBox+'</div>';
				break;
			
	}
	 
    
	return inputObj;
}

 
this.onChangeWidgetAttrType = function onChangeWidgetAttrType(obj){
    var selectedValue = obj.val();
    var propertiesRow = obj.closest(".properties-row");
    
    propertiesRow.find('.opt-type-panel').hide();
    propertiesRow.find('[rel-opt=\''+selectedValue+'\']').show(); 
}

    
this.updatePropertiesList = function updatePropertiesList(tableObj,data){  
	  tableObj.empty();
	  for(i=0;i<data.length;i++){  
		  
		  var dataRow = data[i]; 
		  var savedPropertiesValue  = (dataRow.value == '' || dataRow.value == null) ? dataRow.defaultvalue : dataRow.value; 
		  
		  try  {
               var temp = JSON.parse(savedPropertiesValue); // buat jaga2 kalo error
			   savedPropertiesValue = temp;
		  }  catch(err) {
               
		  }    
		  
		  
		  // switch tipe input, kalo ad opt, udah pasti json, bisa dari dataset atau table
		  var inputObj = ''; 
		  var rowHeaderClickable = false;
		  
		  if(dataRow.opt){  
				var optList = JSON.parse(dataRow.opt); 
              
                // kalo select box, jgn bisa diklik
                if(!optList["select"])
				    rowHeaderClickable = true;

				$.each(optList, function(key, value) {
					inputObj += thisObj.createProperties(key, dataRow, savedPropertiesValue ); 
				}); 
		  }else{
			  inputObj = '<input type="text" name="'+dataRow.properties+'" value="'+savedPropertiesValue+'"/>';
		  }
		   
		  tableObj.append('<div class="div-table-row properties-row"><div class="div-table-col-3 row-header"><div class="'+((rowHeaderClickable) ? 'select-all-toggle' : '')+'">'+dataRow.label+' '+((rowHeaderClickable) ? '<i class="check-all-icon fas fa-check-circle"></i>' : '')+'</div></div><div class="div-table-col-3">'+inputObj+'</div></div>');
	 	  thisObj.bindOptItem(tableObj,dataRow.properties);
	  }
}

this.bindOptItem = function bindOptItem(tableObj,itemName){ 
	var itemList = tableObj.find('.item');
	itemList.click(function(){  
		$(this).toggleClass('selected');  
		var value = $(this).hasClass('selected')?$(this).attr("rel-key"):''; 
		$(this).find('[name="'+itemName+'[]"]').val(value);
        
//        thisObj.summarizeSelectedOpt($(this).closest('.opt-type-panel'));
	});
	
	tableObj.find('.select-all-toggle').click(function(){  
		 // kalo ad satu yg gk ke pilih, select semua dulu
		 var totalItem = itemList.length;
		 var totalItemChoosed = tableObj.find('.item.selected').length;
		 
		var selectAll = (totalItem != totalItemChoosed) ? true : false;
		var obj = $(this).closest('.properties-row').find('.opt-type-panel:visible .opt-item .item');
		
		obj.each(function(){ 
			if (selectAll && !$(this).hasClass('selected')){ 
				$(this).click();
			}else if (!selectAll && $(this).hasClass('selected')){
				$(this).click(); 
			}
					 
		}) 
		
	});

}
    

this.resetWidgetSettingLayer = function resetWidgetSettingLayer(){
	var dashboard = $("#dashboard"); 
	var layer = dashboard.find('.widget-setting'); 
	
	dashboard.find('.widget-setting').appendTo($("#dashboard")).hide(); 
}
    
this.loadOnReady = function loadOnReady(){  
    var dashboard = $("#dashboard"); 

    dashboard.find(".refresh-graph").click(function(){ thisObj.resetWidgetSettingLayer(); thisObj.refreshDashboardPanel();  });  
    dashboard.find('.input-month').datepicker({
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

    dashboard.find(".input-month").change(function() {   
        var prevDateObj;
        var currDateObj;

        $(this).closest('.mnv-date-range').find('.input-month').each(function() {   
             if (prevDateObj){   
                 var prevDate = Date.parse(convertDateToStandartFormat(prevDateObj.val()));
                 var currDate = Date.parse(convertDateToStandartFormat($(this).val()));

                 if (prevDate > currDate) 
                    $(this).val(prevDateObj.val()); 

             } 
             prevDateObj = $(this);
        });  
    });  

    dashboard.find(".btn-dashboard-settings").bind("click", function( event ) {dashboard.find(".dashboard-settings").show();  thisObj.updateCheckedWidgets();  })
    dashboard.find(".btn-close-overlay").bind("click", function( event ) { $(this).closest(".in-tab-overlay").fadeOut(300);})
    dashboard.find("[name='btnSaveDashboardSettings']").bind("click", function( event ) { thisObj.updateWidget(); }) 
    dashboard.find("[name='btnSaveWidgetProperties']").bind("click", function( event ) { thisObj.updateWidgetProperties($(this)); }) 
    dashboard.find("[name='chkAllWidget']").bind("change", function( event ) {   
         var dashboardSettings = dashboard.find(".dashboard-settings"); 
         dashboardSettings.find('[name^=chkWidget-]').val($(this).val()).change();
    })
	
    dashboard.find("[name^=chkWidget-]").bind("change", function( event ) {   
         var dashboardSettings = dashboard.find(".dashboard-settings"); 
         var allChecked = true;

         dashboardSettings.find('[name^=chkWidget-]').each(function() {  
            if($(this).val() == 0){ 
                allChecked = false;
                return;
            }
        });

        dashboardSettings.find('[name=dummychkAllWidget]').prop("checked", allChecked); 
        dashboardSettings.find('[name=chkAllWidget]').val((allChecked) ? 1 : 0); 

    }) 

    thisObj.refreshDashboardPanel(true);

    $("#widgets-panel").sortable({ handle: '.title', stop: function( event, ui ) { thisObj.updateWidgetOrder(); }});
}
} 
