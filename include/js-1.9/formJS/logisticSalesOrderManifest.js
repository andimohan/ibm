function LogisticSalesOrderManifest(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        var objAndValue = new Array; 
        objAndValue.push({object:'hidLogisticKey[]', value :'pkey'});  	    	 
        objAndValue.push({object:'logisticTotal[]', value :'grandtotal'}); 
        objAndValue.push({object:'logisticDate[]', value :'trdate', type : 'date'});	
        var objAndValueForDetailAutoComplete = objAndValue;	
 
    
        this.tabID = tabID;    
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row"); 

            thisObj.updateRowInformation(detailRow,objAndValue,ui); 
            thisObj.calculateTotal();    
        } 
        

        this.calculateTotal = function calculateTotal(){    
            var amount = 0; 
            
            $("#" + tabID + " [name='chkPick[]']").not(":disabled").each(function(){   
                if ($(this).val() != 1 ) return;

                objAmount = $(this).closest(".div-table-row").find("[name='logisticTotal[]']"); 
                amount += parseInt(unformatCurrency(objAmount.val())) || 0;
            })  
            
            tabObj.find("[name='grandTotal']").val(amount).blur();
        } 
        
         this.updateRowInformation  = function updateRowInformation (detailRow,objAndValue,ui){
       
           var i;
           for(i=0;i<objAndValue.length;i++){     

                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
 
            }

            // GK BOLEH MASUKIN KE OBJ KARENA KENA LOOPING NANTI KARENA CHANGE LG
            detailRow.find("[name='logisticCode[]']").first().val(ui.item['value']); 

       }
  
        this.resetDetails = function resetDetails(){   
            clearAllRows($("#"+tabID)); 
            thisObj.calculateTotal(); 
        }
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        }
        
         
        this.importData = function importData(){  
            
            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;

            var citykey = tabObj.find("[name=hidCityKey]").val(); 
            var checkDatePeriod = (tabObj.find("[name=chkDatePeriod]").val() == 1) ? true : false; 
 
            var dateParam = "";
            if (checkDatePeriod){    
                var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
                var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
                dateParam = "&startdate="+startdate+"&enddate="+enddate;
            }           
            
            var transportationkey  = tabObj.find("[name=selTransportation]").val(); 
            
	        $.ajax({
	            type: "GET",
	            url:  'ajax-logistic-sales-order.php',
	            beforeSend:function (xhr){ 
                    clearAllRows($("#defaultForm-"+tabID));
                    thisObj.activeAjaxConnections++; 
	            }, 
	            data: 'action=searchData&recipientcitykey=' + citykey + '&transportationkey=' + transportationkey + dateParam, 
	            success: function(data){
                    
	                    var data = JSON.parse(data);  
	                    var i;
                        var newrow;
                    
                             
	                    for(i=0;i<data.length;i++){ 
                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidLogisticKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"logisticCode", "value":data[i].code});
                            arrPostValue.push({"selector":"logisticDate", "value": moment(data[i].trdate).format(_DATE_FORMAT_)}); 
                            arrPostValue.push({"selector":"logisticTotal", "value":data[i].grandtotal});  
                            newrow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
	                    }

                     
                     thisObj.rebindEl();
                    
	                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
	                 tabObj.find(".inputnumber").change().blur();
	                 tabObj.find(".inputdecimal").change().blur();

	                decreaseActiveAjaxConnections(thisObj); 
                     
                    tabObj.find("[name='chkPick-master']").val(1).change();  
                    
	            } ,
	             error: function(xhr, errDesc, exception) { 
                            decreaseActiveAjaxConnections(thisObj); 
                     
                }
	        });
	    } 
         
        this.onChangeChk = function onChangeChk(){   
            thisObj.calculateTotal();
        }
        
        this.updateCityInformation = function updateCityInformation(obj,event, ui){
           
                var topkey = 0;
            
				if (tabObj.find("[name=hidCurrentCityKey]" ).val() != ''){
					$( "#dialog-message" ).html("Merubah tujuan akan mereset detail transaksi.");
					$( "#dialog-message" ).dialog({
					  width: 300,
					  modal: true,
					  title:"Konfirmasi Perubahan Data Pelanggan", 
					  open: function() {
						  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
					  },
					  close:function() {
							tabObj.find("[name=hidCityKey]" ).val(tabObj.find("[name=hidCurrentCityKey]" ).val());
							tabObj.find("[name=cityName]" ).val(tabObj.find("[name=hidCurrentCityName]" ).val()); 
                          	$(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));  
                          
                            thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah
                            
					  },
					  buttons : {
						  OK : function (){  
						  		 if (ui.item == null) { 
									clearAutoCompleteInput(obj,'hidCityKey');	
									tabObj.find("[name=hidCurrentCityKey]" ).val(''); 
									tabObj.find("[name=hidCurrentCityName]" ).val('');  
								 }else{
									tabObj.find("[name=hidCurrentCityKey]" ).val(ui.item.pkey); 
									tabObj.find("[name=hidCurrentCityName]" ).val(ui.item.value);  
                                     
                                    topkey  = ui.item.termofpaymentkey;
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
						clearAutoCompleteInput(obj,'hidCityKey');	
						tabObj.find("[name=hidCurrentCityKey]" ).val(''); 
						tabObj.find("[name=hidCurrentCityName]" ).val('');  
					 }else{ 
						tabObj.find("[name=hidCurrentCityKey]" ).val(ui.item.pkey); 
						tabObj.find("[name=hidCurrentCityName]" ).val(ui.item.value); 
  
                        topkey  = ui.item.termofpaymentkey;
					 } 	
					  
                    thisObj.rebindEl();
				} 	 
             

        }
        
         this.updateTransportation = function updateTransportation(){


           $( "#dialog-message" ).html("Apakah Anda ingin mengganti transportasi untuk tujuan ini ?<br>Semua detail transaksi akan dihapus jika Anda mengganti mata uang.");
            $( "#dialog-message" ).dialog({
              width: 300,
              modal: true,
              title:"Konfirmasi Perubahan Data transportation", 
              close:function() {
                    tabObj.find("[name=selTransportation]").val(tabObj.find("[name=hidCurrentTransportationKey]" ).val()); 
              }, 
              open: function() {
                  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
              }, 
              buttons : {
                  OK : function (){     
                        tabObj.find("[name=hidCurrentTransportationKey]" ).val(tabObj.find("[name=selTransportation]" ).val());   
                        thisObj.resetDetails(); 
                       $( this ).dialog( "close" );
                  },
                  Cancel : function (){  
                        tabObj.find("[name=selTransportation]").val(tabObj.find("[name=hidCurrentTransportationKey]" ).val()); 
                        $( this ).dialog( "close" );
                  }
              } 

            });	  

        }    
          
         
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal(); ;   
        }
            
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal();  
        } 
        
        this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(){
            
        } 
         
        this.rebindEl = function rebindEl(){   
            
             var handling = [];
             handling.onSelectFunction = 'getTabObj().updateDetail'; 
             var citykey = tabObj.find("[name=hidCityKey]").val();
             var transportationkey = tabObj.find("[name=selTransportation]").val();
            
             bindAutoCompleteForTransactionDetail('logisticCode[]',objAndValueForDetailAutoComplete,'ajax-logistic-sales-order.php?action=searchData&statuskey=2&recipientcitykey=' + citykey + '&transportationkey=' +transportationkey,handling); 
             bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });
            
        } 
        
        this.loadOnReady = function loadOnReady(){
 
            tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); }); 
            tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)});  
 
  			tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
                var checked = ($(this).val() == 1) ? true : false;
                var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]");

                dateObj.removeClass("force-readonly");

                dateObj.datepicker((checked) ? "enable" : "disable"); 

                if(!checked) dateObj.addClass("force-readonly");
            })   
            tabObj.find("[name=chkDatePeriod]").change();

             tabObj.find("[name=trStartDate], [name=trEndDate]").bind( "change",function() { 
                var trStartDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val()));
                var trEndDate = Date.parse(convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val()));

                if (trStartDate > trEndDate) 
                    tabObj.find("[name=trEndDate]").val(tabObj.find("[name=trStartDate]").val()); 

            });
            tabObj.find("[name='chkPick-master']").val(1).change();    
            tabObj.find("[name=selTransportation]").change(function() { thisObj.updateTransportation();});

            thisObj.rebindEl(); 

        }
        
}
