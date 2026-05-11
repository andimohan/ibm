function RentalTimeSheet(tabID, rs) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
	
		var arrDetails = {};
    
		var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'selTimeUnit[]', value :'timeunitkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
        var objAndValueForDetailAutoComplete = objAndValue;  
        
        this.tabID = tabID;    
      
        this.rs = (rs.length > 0) ? rs[0] : null;
    
        this.updateDetail = function updateDetail(target,objAndValue,ui){

                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();  
              
                for(i=0;i<objAndValue.length;i++){   
                    
                    //overwrite kalo kg
                    if(objAndValue[i].object == 'hidGramasi[]' && ui.item['weightunitkey'] == 2)
                        ui.item[objAndValue[i].value] *= 1000; 
                    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
            
                        
//                detailRow.find("[name=\"selTimeUnit[]\"]").find("option:not(:selected)").attr('disabled', true);
//                detailRow.find("[name=\"selTimeUnit[]\"]").change();
            
//                updateAvailableUnit(itemKeyObj, selUnitObj);
//                thisObj.updateUnitPrice(selUnitObj);
             
                // harus handle manual utk obj autosearch
//                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
                
                thisObj.calculateDetail(itemKeyObj);
 
         }
        
	    this.calculateDetail = function calculateDetail(obj){      
               
                    var row =  $(obj).closest(".transaction-detail-row");   
                
                    var workTime =  unformatCurrency(row.find("[name='workTime[]']").val());
                    var workHour =  unformatCurrency(row.find("[name='workHour[]']").val());
                      
                    var overtime = workTime - workHour;
                    row.find("[name='overTime[]']").val(overtime).blur(); 

	     }
		
	   this.updateCustomerCity =  function updateCustomerCity(val){ 
				if(!val)
					return;

			   $.ajax({
					type: "GET",
					url:  'ajax-customer.php',
					async: false,
					data: "action=getDataRowById&pkey=" + val ,  
				}).done(function( data ) {  

						data = JSON.parse(data) ; 
						data = data[0];
					tabObj.find("[name=recipientCityName]").val(data.cityandcategoryname);  
					tabObj.find("[name=hidRecipientCityKey]").val(data.citykey);  

				});
        } 
	   
	    this.updateSOInformation = function updateSOInformation(){
                
                // udpate detail container
               $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-rental.php', 
                    async: false,
                    data: "action=getDetailById&pkey=" + tabObj.find("[name=hidRefkey]").val() ,  
                }).done(function( data ) {
                   
                    data = JSON.parse(data) ;  
    
                    // udpate detail
                    for(i=0;i<data.length;i++){  
                        var pkey = data[i].pkey;
                         
                        var arrTemp = {}; 
                        arrTemp['minimaltime'] = data[i].minimaltime;
                        arrTemp['itemkey'] = data[i].itemkey;
                        arrTemp['itemname'] = data[i].itemname;
                         
                        arrDetails[pkey] = arrTemp;     
                    }
                    
                    // update combobox services
                    var newOptions = {};
                    for(i=0;i<data.length;i++)  
                        newOptions[data[i].pkey] =  data[i].label;        
                    
                    var select = tabObj.find("[name=selJODetailKey]");
                    if(select.prop)  
                      var options = select.prop('options'); 
                    else  
                      var options = select.attr('options');
                    
                    $('option', select).remove();

                    $.each(newOptions, function(val, text) {
                        options[options.length] = new Option(text, val);
                    }); 

                    select.find('option:eq(0)').prop('selected', true).change();
				   console.log(arrDetails);

                }); 
              
             

         }   
        
        this.updateSalesOrderInformation = function updateSalesOrderInformation(){

            var sokey = tabObj.find("[name=hidRefkey]").val(); 
 
            $.ajax({
                type: "GET",
                url:  'ajax-sales-order-rental.php',
                async: false,
                data: "action=getDataRowById&pkey=" + sokey ,  
            }).done(function( data ) {  

                data = JSON.parse(data) ; 
                data = data[0]; 

                tabObj.find("[name=recipientName]").val(data.recipientname);  
                tabObj.find("[name=hidRecipientKey]").val(data.customerkey);  
                tabObj.find("[name=recipientEmail]").val(data.recipientemail);  
                tabObj.find("[name=recipientPhone]").val(data.recipientphone);  
                tabObj.find("[name=recipientAddress]").val(data.recipientaddress);  
				thisObj.updateCustomerCity(data.customerkey);
				thisObj.updateSOInformation(); 
 
            }); 
 
        }  
         
        this.updateTimeUnit = function updateTimeUnit(obj){ 
            $(obj).closest(".transaction-detail-row").find(".time-unit").html($(obj).find('option:selected').text());
        } 
		
		this.updateStartDate = function updateStartDate(obj){
			var rowObj =  $(obj).closest(".transaction-detail-row"); 
			var workhours =  tabObj.find("[name=hidWorkHour]").val();
			
			var startdate = rowObj.find("[name='trStartDate[]']").val();
			var startdate2 = rowObj.find("[name='trStartDate2[]']").val();
			var restdate = rowObj.find("[name='trRestDate[]']").val();
			//var restdate2 = rowObj.find("[name='trRestDate2[]']").val();
			var enddate = rowObj.find("[name='trEndDate[]']").val();
            var dateParam = "&startdate="+startdate+"&startdate2="+startdate2+"&restdate="+restdate+"&enddate="+enddate;
			$.ajax({
                type: "GET",
                url:  'ajax-sales-order-rental.php',
                async: false,
                data: "action=getCalculateDate" + dateParam ,  
            }).done(function( data ) {   
				if(!data) return; 
				
				if(data.length == 0){  
					alert(phpErrorMsg[213]) 
					return;
				}
				
                data = JSON.parse(data) ; 
                data = data[0]; 
				rowObj.find("[name='workTime[]']").val(data.worktime).blur(); 
				
				if(workhours>0)
					rowObj.find("[name='workHour[]']").val(workhours).blur(); 
				
				thisObj.calculateDetail(rowObj);
 
            });   
			
		} 
		
		this.updateWorkHour = function updateWorkHour(){
 			var sokey = tabObj.find("[name=selJODetailKey]").val(); 
			if(arrDetails[sokey]['minimaltime'])
				tabObj.find("[name=hidWorkHour]").val(arrDetails[sokey]['minimaltime']); 
		}
		
		this.updateDate = function updateDate(obj){      
			var dateParam = "";
        	var row =  $(obj).closest(".transaction-detail-row");   
			var startdate = row.find("[name='trStartDate[]']").val();
			var startdate2 = row.find("[name='trStartDate2[]']").val();
			var restdate = row.find("[name='trRestDate[]']").val();
			var restdate2 = row.find("[name='trRestDate2[]']").val();
			var enddate = row.find("[name='trEndDate[]']").val();
			dateParam = "&startdate="+startdate+"&startdate2="+startdate2+"&restdate="+restdate+"&restdate2="+restdate2+"&enddate="+enddate;
			$.ajax({
                type: "GET",
                url:  'ajax-sales-order-rental.php', 
                async: false,
                data: "action=getCalculateDate"+dateParam ,  
            }).done(function( data ) {     
				if(!data) return; 
				
				//alert("jalan");
				/*if(data.length == 0){   
					alert(phpErrorMsg[213]) 
					return;
				}*/
				
//                data = JSON.parse(data) ; 
//                data = data[0]; 

                /*tabObj.find("[name=recipientName]").val(data.recipientname);  
                tabObj.find("[name=hidRecipientKey]").val(data.customerkey);  
                tabObj.find("[name=recipientEmail]").val(data.recipientemail);  
                tabObj.find("[name=recipientPhone]").val(data.recipientphone);  
                tabObj.find("[name=recipientAddress]").val(data.recipientaddress);  
				thisObj.updateCustomerCity(data.customerkey);
				thisObj.updateSOInformation(); */
 
            });
 
		}
		
		this.updateDatex =  function updateDatex(obj){ 
					var row =  $(obj).closest(".transaction-detail-row");   
			var startdate = row.find("[name='trStartDate[]']").val();
			var startdate2 = row.find("[name='trStartDate2[]']").val();
			var restdate = row.find("[name='trRestDate[]']").val();
			var restdate2 = row.find("[name='trRestDate2[]']").val();
			var enddate = row.find("[name='trEndDate[]']").val();
			dateParam = "&startdate="+startdate+"&startdate2="+startdate2+"&restdate="+restdate+"&restdate2="+restdate2+"&enddate="+enddate;

                      /* $.ajax({
                            type: "GET",
                            url:  'ajax-sales-order-rental.php',  
                            async: false,
                            data: "action=getCalculateDate"+dateParam ,  
                        }).done(function( data ) {  
                           
                                //data = JSON.parse(data) ; 
                                data = data[0];
                            //tabObj.find("[name=recipientCityName]").val(data.cityandcategoryname);  
                            //tabObj.find("[name=hidRecipientCityKey]").val(data.citykey);  
                                
                        });*/
			
			$.ajax({
            type: "GET", 
            url:  'ajax-sales-order-rental.php',
            data: "action=getCalculateDate"+dateParam , 
            success: function(data){ 
//                    var data = JSON.parse(data);   
                    var i;
					console.log(data);
            } 
        });
			
			
			
        }
		   
                       
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){ 
//         
        }    
             
        this.rebindEl = function rebindEl(){   
//            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
            bindEl(tabObj.find("[name='workTime[]'], [name='workHour[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
//            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); thisObj.calculateDetail(this); });  
//            bindEl(tabObj.find("[name='selTimeUnit[]']"),'change',function(){ thisObj.updateTimeUnit(this); }); 
//			bindEl(tabObj.find("[name='chkIsUnlimited[]']"),'change',function(){ thisObj.updateIsUnlimited(this); }); 
			tabObj.find(".input-datetime").removeClass("hasDatepicker");
			tabObj.find(".input-datetime").removeAttr("id");
			tabObj.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
 			//bindEl(tabObj.find("[name='trStartDate[]'], [name='trStartDate2[]'],[name='trRestDate[]'], [name='trEndDate[]']" ), 'change',  function(){ thisObj.updateStartDate(this) }); 
 			bindEl(tabObj.find("[name='trEndDate[]']" ), 'change',  function(){ thisObj.updateStartDate(this) }); 
 
        }     
        
        this.loadOnReady = function loadOnReady(){ 
			 /*if(!this.rs){  
				 	//var newRow = addNewTemplateRow("detail-row-template");
					tabObj.find(".input-datetime").removeClass("hasDatepicker"); 
					tabObj.find(".input-datetime").removeAttr("id");
					tabObj.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
			} */
			
			 tabObj.find("[name=selJODetailKey]").change(function() { thisObj.updateWorkHour(); });

            thisObj.rebindEl(); 
   
        }  
     }
