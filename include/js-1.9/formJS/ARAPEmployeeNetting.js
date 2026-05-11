function ARAPEmployeeNetting(tabID, rs){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;    
    this.rs = (rs.length > 0) ? rs[0] : null;
    this.customerTax = 0;
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});
    objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'}); 
    var objAndValueForDPDetailAutoComplete  = objAndValue; 
      
    var  objAndValue = new Array;
    objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
    var objAndValueForCostDetailAutoComplete  = objAndValue; 
 
    var  objAndValue = new Array;
    objAndValue.push({object:'hidARKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'}); 
    objAndValue.push({object:'arOutstanding[]', value :'outstanding'}); 
    objAndValue.push({object:'arAmount[]', value :'outstanding'});  
    objAndValue.push({object:'arRefCode[]', value :'refcode'}); 
    objAndValue.push({object:'arRefCode2[]', value :'refcode2'}); 
    objAndValue.push({object:'arTransRefCode[]', value :'reftranscode2'});   
    objAndValue.push({object:'arCustomerName[]', value :'customername'});   
    var objAndValueForDetailAutoComplete = objAndValue;
    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidAPKey[]', value :'pkey'});   
    objAndValue.push({object:'refAPCode[]', value :'refcode'}); 
    objAndValue.push({object:'apOutstanding[]', value :'outstanding'}); 
    objAndValue.push({object:'apAmount[]', value :'outstanding'}); 
    objAndValue.push({object:'apRefCode[]', value :'refcode'}); 
    objAndValue.push({object:'apRefCode2[]', value :'refcode2'});   
    objAndValue.push({object:'apTransRefCode[]', value :'reftranscode2'});    
    objAndValue.push({object:'apCustomerName[]', value :'customername'});   
    var objAndValueForDetailAPAutoComplete = objAndValue;
 
    this.resetDetails = function resetDetails(){  
        thisObj.resetARDetails(); 
        thisObj.resetAPDetails();
    } 

    
    this.resetARDetails = function resetARDetails(){  
        clearAllRows(tabObj.find(".mnv-ar"));  
        thisObj.calculateTotalAR();  
    }
    
    this.resetAPDetails = function resetAPDetails(){  
        clearAllRows(tabObj.find(".mnv-ap"));  
        thisObj.calculateTotalAP();  
    }
      
     this.updateDetailAR = function updateDetailAR(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
            }

            detailRow.find("[name=\"arCode[]\"]").first().val(ui.item['code']);
            thisObj.calculateTotalAR();   
    }
     
     this.updateDetailAP = function updateDetailAP(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");

            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
            }

            detailRow.find("[name=\"apCode[]\"]").first().val(ui.item['code']);
            thisObj.calculateTotalAP();    
    }
     
  this.calculateTotal = function calculateTotal(){     
        var totalar = 0;
        var totalap = 0;  
         
        totalar = parseFloat(unformatCurrency(tabObj.find("[name='totalARAmount']").val())); 
        totalap = parseFloat(unformatCurrency(tabObj.find("[name='totalAPAmount']").val())); 
        var grandtotalar = totalar;
        tabObj.find("[name='grandtotalARAmount']").val(grandtotalar).blur();
        var grandtotalap = totalap;
        tabObj.find("[name='grandtotalAPAmount']").val(grandtotalap).blur(); 
       
    } 
    
    this.calculateTotalAR = function calculateTotalAR(){
        var amount = 0;      

        tabObj.find("[name='hidARKey[]']").each(function(){    
            var objAmount = $(this).closest(".div-table-row").find("[name='arAmount[]']"); 
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })
        
        tabObj.find("[name='totalARAmount']").val(amount).blur(); 
        thisObj.calculateTotal();
    }  
    
    this.calculateTotalAP = function calculateTotalAP(){
        var amount = 0;      

        tabObj.find("[name='hidAPKey[]']").each(function(){    
            var objAmount = $(this).closest(".div-table-row").find("[name='apAmount[]']");
            amount += parseFloat(unformatCurrency(objAmount.val())) || 0;

        })
        
        tabObj.find("[name='totalAPAmount']").val(amount).blur(); 
        thisObj.calculateTotal();
    }


    this.rebindAR = function rebindAR(){  
        bindEl(tabObj.find("[name='arAmount[]']"),'change', function() { thisObj.calculateTotalAR(); }); 
        bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete,'ajax-ar-employee.php?action=searchData&employeekey=' + tabObj.find("[name=hidEmployeeKey]").val(),thisObj.updateDetailAR); 
    } 
    
    this.rebindAP = function rebindAP(){ 
        bindEl(tabObj.find("[name='apAmount[]']"),'change', function() { thisObj.calculateTotalAP(); }); 
        bindAutoCompleteForTransactionDetail('apCode[]',  objAndValueForDetailAPAutoComplete,'ajax-ap-employee.php?action=searchData&employeekey=' + tabObj.find("[name=hidEmployeeKey]").val(),thisObj.updateDetailAP); 
    } 

    this.importDataAR = function importDataAR(){ 
        
        thisObj.activeAjaxConnections++; 
        
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
         
        var employeekey = tabObj.find("[name=hidEmployeeKey]" ).val();
        if(!employeekey) return;
        
        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
                 
        var ajaxData = "action=searchData&employeekey=" + employeekey+dateParam;  
          
        $.ajax({
            type: "GET",
            url:  'ajax-ar-employee.php',
            data: ajaxData,
            }).done(function( data ) { 
                    var data = JSON.parse(data);
                    if(data.length == 0){ 
                        addNewTemplateRow("ar-row-template");   
                    }else{
                        var i;
                        for(i=0;i<data.length;i++){  
                                var arrPostValue = []; 
                                arrPostValue.push({"selector":"hidARKey", "value":data[i].pkey});
                                arrPostValue.push({"selector":"arCode", "value":data[i].code}); 
                                arrPostValue.push({"selector":"arRefCode", "value":data[i].refcode});   
                                arrPostValue.push({"selector":"arRefCode2", "value":data[i].refcode2});   
                                arrPostValue.push({"selector":"arOutstanding", "value":data[i].outstanding});  
                                arrPostValue.push({"selector":"arAmount", "value":data[i].outstanding});  
                                arrPostValue.push({"selector":"arTransRefCode", "value":data[i].reftranscode2}); 
                                arrPostValue.push({"selector":"arCustomerName", "value":data[i].customername});  
                                addNewTemplateRow("ar-row-template",JSON.stringify(arrPostValue));  
                        }

                       thisObj.rebindAR();   
                    }
                    
                tabObj.find(".inputnumber, .inputdecimal").blur();  
                thisObj.calculateTotalAR(); 
                decreaseActiveAjaxConnections(thisObj); 
        }); 
    }
    
    
    this.importDataAP = function importDataAP(){ 
        thisObj.activeAjaxConnections++; 
        
        var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
 
        var employeekey = tabObj.find("[name=hidEmployeeKey]" ).val();
        if(!employeekey) return;
        
        var dateParam = "";
        if (checkDatePeriod){    
            var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
            var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
            dateParam = "&startdate="+startdate+"&enddate="+enddate;
        }
          
        var ajaxData = "action=searchData&employeekey=" + employeekey+dateParam;
            
        $.ajax({
            type: "GET",
            url:  'ajax-ap-employee.php',
            data: ajaxData,
            }).done(function( data ) {  
                    var data = JSON.parse(data); 
                    if(data.length == 0){ 
                        addNewTemplateRow("ap-row-template");  
                    }else{
                        var i;
                        for(i=0;i<data.length;i++){  
                                var arrPostValue = []; 
                                arrPostValue.push({"selector":"hidAPKey", "value":data[i].pkey});
                                arrPostValue.push({"selector":"apCode", "value":data[i].code}); 
                                arrPostValue.push({"selector":"apRefCode", "value":data[i].refcode});  
                                arrPostValue.push({"selector":"apRefCode2", "value":data[i].refcode2});  
                                arrPostValue.push({"selector":"apOutstanding", "value":data[i].outstanding});
                                arrPostValue.push({"selector":"apAmount", "value":data[i].outstanding}); 
                                arrPostValue.push({"selector":"apTransRefCode", "value":data[i].reftranscode2}); 
                                arrPostValue.push({"selector":"apCustomerName", "value":data[i].customername});    

                                addNewTemplateRow("ap-row-template",JSON.stringify(arrPostValue));  
                        }

                       thisObj.rebindAP(); 
                    }
                   
 
                tabObj.find(".inputnumber, .inputdecimal").blur();  
                thisObj.calculateTotalAP();  
                decreaseActiveAjaxConnections(thisObj); 
        }); 
    }
        
    this.importData = function importData(){ 
        
        loadOverlayScreen({content: _LOADING_TEMPLATE_});
        thisObj.activeAjaxConnections = 0;
        
        thisObj.resetDetails(); 
        thisObj.importDataAR();
        thisObj.importDataAP(); 
                
    }
	
	this.updateEmployeeInformation = function updateEmployeeInformation(event, ui){
            var obj = this; 
            if (tabObj.find("[name=hidCurrentEmployeeKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah karyawan akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Karyawan", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidEmployeeKey]" ).val(tabObj.find("[name=hidCurrentEmployeeKey]" ).val());
                        tabObj.find("[name=employeeName]" ).val(tabObj.find("[name=hidCurrentEmployeeName]" ).val());
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        thisObj.rebindEl(); 
                  },
                  buttons : {
                      OK : function (){  
                             if (ui.item == null) { 
                                clearAutoCompleteInput(obj,'hidEmployeeKey');	
                                tabObj.find("[name=hidCurrentEmployeeKey]" ).val(''); 
                                tabObj.find("[name=hidCurrentEmployeeName]" ).val(''); 
                             }else{
                                tabObj.find("[name=hidCurrentEmployeeKey]" ).val(ui.item.pkey); 
                                tabObj.find("[name=hidCurrentEmployeeName]" ).val(ui.item.value);  
                             } 
 
                            thisObj.resetDetails(); 
                            $( this ).dialog( "close" );
                      },
                      Cancel : function (){  
                            $( this ).dialog( "close" );
                      }
                  },
                });	 
            }else{ 
                 if (ui.item == null) {
                    clearAutoCompleteInput(obj,'hidEmployeeKey');	
                    tabObj.find("[name=hidCurrentEmployeeKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentEmployeeName]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentEmployeeKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentEmployeeName]" ).val(ui.item.value); 
                     
                 } 	

                 thisObj.rebindAR(); 
                 thisObj.rebindAP(); 
            } 	 

    }
 
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){ 
     thisObj.calculateTotalAR(); 
     thisObj.calculateTotalAP(); 
    } 

    this.rebindEl = function rebindEl(){ } 
    

    this.loadOnReady = function loadOnReady(){
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
         
        tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]"); 
            dateObj.removeClass("force-readonly"); 
            dateObj.datepicker((checked) ? "enable" : "disable");  
            if(!checked) dateObj.addClass("force-readonly");
         })  
        
        tabObj.find("[name=chkDatePeriod]").change();
        tabObj.find("[name=selCurrency]").change(function() { thisObj.updateCurrency(); });
        
        
         tabObj.find(".arap-show-detail").on('click', function() { 
            var $obj = $(this).closest(".arap-col").find(".options-row"); 
              
            $obj.css('display',($obj.is(":visible")) ? 'none' : 'table'); 
            
            var temp = $(this).attr("alt");        
            $(this).attr("alt",$(this).html());
            $(this).html(temp);
             
        });
          
        tabObj.find(" [name=btnAddARRows]").on('click', function() {
            addNewTemplateRow("ar-row-template"); 
            thisObj.rebindAR(); 
	     });
        
        tabObj.find(" [name=btnAddAPRows]").on('click', function() {
            addNewTemplateRow("ap-row-template"); 
            thisObj.rebindAP(); 
        });
        
        thisObj.calculateTotalAR();
        thisObj.calculateTotalAP();
         
        if(!this.rs){ 
            addNewTemplateRow("ar-row-template");  
            addNewTemplateRow("ap-row-template");  
        }
        
        thisObj.rebindEl(); 
        thisObj.rebindAR(); 
        thisObj.rebindAP();
         
    }
}
