function APPayableTax23Payment(tabID, varConstant,fileUpload){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;
    this.tablekey = varConstant.TABLEKEY;   
    this.useStorage = varConstant.useStorage;  
 
	var fileFolder = fileUpload.uploadFolder;
	var fileUploaderTarget = fileUpload.uploaderTarget;
	var rsFile = fileUpload.rsFile;	
    var simpleForm = varConstant.simpleForm || false;
	var arrFile = Array();

	var id = tabObj.find("[name=hidId]").val();
    
    var  objAndValue = new Array;
    objAndValue.push({object:'hidAPKey[]', value :'pkey'});   
    objAndValue.push({object:'refCode[]', value :'refcode'});  
	objAndValue.push({object:'refDate[]', value:'refdate', type : 'date'}); 	 
    objAndValue.push({object:'apAmount[]', value :'amount'}); 	
    objAndValue.push({object:'outstanding[]', value :'outstanding'});
    objAndValue.push({object:'amount[]', value :'outstanding'});
    var objAndValueForDetailAutoComplete = objAndValue;


	this.resetDetails = function resetDetails(){  
		clearAllRows(tabObj.find(".mnv-transaction")); 
		thisObj.calculateTotal(); 
	}


	this.updateDetail = function updateDetail(target,objAndValue,ui){   
			var detailRow = $(target).closest(".transaction-detail-row");
			
			for(i=0;i<objAndValue.length;i++){   
				if (objAndValue[i].type == "date")
				   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

				detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
			}

			detailRow.find("[name=\"apCode[]\"]").first().val(ui.item['code']);
	} 

	    
	this.calculateTotal = function calculateTotal(){
            
            var amount = 0;   

			tabObj.find("[name='chkPick[]']").not(":disabled").each(function(){  
                    if ($(this).val() != 1 )
                        return;
                 
                    objAmount = $(this).closest(".div-table-row").find("[name='amount[]']"); 
                    amount += parseInt(unformatCurrency(objAmount.val())) || 0; 
            });
 
 
            // if(simpleForm)
            //     tabObj.find(".total").text(amount).formatCurrency();
            // else
            tabObj.find("[name='total']").val(amount).blur(); 


            var totalPayment = 0; 
            tabObj.find("[name='paymentMethodValue[]']").each(function() { totalPayment += parseFloat(unformatCurrency($(this).val())) || 0; })   
            tabObj.find("[name='totalPayment']").val(totalPayment).blur();
            
            var balance = totalPayment - amount;    
            balance = Math.round((balance + Number.EPSILON) * 100) / 100 
            tabObj.find("[name='balance']").val(balance).blur(); 
    }
        
	  this.importData = function importData(){ 
          
			loadOverlayScreen({content: _LOADING_TEMPLATE_});
			thisObj.activeAjaxConnections = 0;
            
            var dateParam = "";
            var supplierParam = "";
 
            if (tabObj.find("[name=chkDatePeriod]").val() == 1){    
                var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
                var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
                dateParam = "&startdate="+startdate+"&enddate="+enddate;
            }
        
            if(tabObj.find("[name=hidSupplierKey]").val() != 0)
                supplierParam = "&supplierkey=" + tabObj.find("[name=hidSupplierKey]").val();
 
            
            $.ajax({
                type: "GET",
                url:  'ajax-ap-payable-tax.php',
                beforeSend:function (xhr){
	                clearAllRows(tabObj.find(".mnv-transaction"),true,true);
                   	thisObj.activeAjaxConnections++; 
                },
                data: "action=searchData&supplierkey=&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val()+ supplierParam +dateParam,
                success: function(data){ 

                        var data = parseJSON(data); 
                        var i;
                    
                     if (simpleForm) {
                            var $rows = tabObj.find(".mnv-transaction");
                            var $template  = $rows.find(".detail-row-template").first();
                            var fragment = $(document.createDocumentFragment());

                            for(i=0;i<data.length;i++){  

                                //var row = $template.clone().removeClass("detail-row-template").addClass("transaction-detail-row").show();
                                  var row = addNewTemplateRow("detail-row-template",null);
                                
                                    row.find(':input').prop('disabled', false); 
                                    row.find(".apcode").text(data[i].code || "-");
                                    row.find(".suppliername").text(data[i].suppliername || "-");
                                    row.find(".refcode").text(data[i].refcode || "-");
                                    row.find(".jocode").text(data[i].jocode || "-");
                                    row.find(".trdate").text( data[i].trdate ? moment(data[i].trdate).format(_DATE_FORMAT_) : "-");
                                    row.find(".jodate").text( data[i].jodate ? moment(data[i].jodate).format(_DATE_FORMAT_) : "-");
                                    row.find(".apamount").text(Number(data[i].amount || 0).toLocaleString("id-ID") || 0);
                                    row.find(".outstanding").text(Number(data[i].outstanding || 0).toLocaleString("id-ID") || 0);
                                    row.find(".amount").text(Number(data[i].outstanding || 0).toLocaleString("id-ID") || 0);


                                    row.find("[name='hidDetailKey[]']").prop("disabled", false);
                                    row.find("[name='hidAPKey[]']").val(data[i].pkey || 0);
                                    row.find("[name='amount[]']").val(data[i].outstanding || 0);
                                    row.find("[name='outstanding[]']").val(data[i].outstanding || 0);

                                    //row.find("[name='chkPick[]']").prop("checked", true);

                                    fragment.append(row);

                            }

                            $rows.append(fragment);
                     }else{
                            for(i=0;i<data.length;i++){  
                                    var arrPostValue = []; 
                                    arrPostValue.push({"selector":"hidAPKey", "value":data[i].pkey});
                                    arrPostValue.push({"selector":"apCode", "value":data[i].code}); 
                                    arrPostValue.push({"selector":"refCode", "value":data[i].refcode}); 
                                    arrPostValue.push({"selector":"refDate", "value":moment(data[i].refdate).format(_DATE_FORMAT_)}); 
                                    arrPostValue.push({"selector":"apAmount", "value":data[i].amount}); 
                                    arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding}); 
                                    arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                                    addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                            }


                         // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                         tabObj.find(".inputnumber").change().blur();
                         tabObj.find(".inputdecimal").change().blur();
                     }
                    
                    
					 tabObj.find("[name='chkPick-master']").val(1).change(); 
                    
					 thisObj.rebindEl(); 
					 decreaseActiveAjaxConnections(thisObj); 
					 thisObj.calculateTotal();
					  
                } ,
                 error: function(xhr, errDesc, exception) {
                     decreaseActiveAjaxConnections(thisObj); 
				 }
            }); 
	    }
	  
      this.updateNTPN = function updateNTPN(){
            var pkey =  tabObj.find("[name=hidId]").val();
            var ntpn =  tabObj.find("[name=ntpn]").val();
            $.ajax({
                    type: "post",
                    url:  'ajax-ap-payable-tax.php',
                    async: false,
                    data: "action=updateNTPN&ntpn="+ntpn+"&pkey=" + pkey ,  
                }).done(function( data ) { 
                    alert(phpLang['dataHasBeenSuccessfullyUpdated']);
            }); 
         
        }    
	  
        this.onChangeChk = function onChangeChk(){ 
            thisObj.calculateTotal();
        }
 
       this.updateSupplierInformation = function updateSupplierInformation(event, ui){
            var obj = this; 
            if (tabObj.find("[name=hidCurrentSupplierKey]" ).val() != ''){
                $( "#dialog-message" ).html("Merubah pemasok akan mereset detail transaksi.");
                $( "#dialog-message" ).dialog({
                  width: 300,
                  modal: true,
                  title:"Konfirmasi Perubahan Data Pemasok", 
                  open: function() {
                      $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                  },
                  close:function() {
                        tabObj.find("[name=hidSupplierKey]" ).val(tabObj.find("[name=hidCurrentSupplierKey]" ).val());
                        tabObj.find("[name=supplierName]" ).val(tabObj.find("[name=hidCurrentSupplierName]" ).val());
                        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                        thisObj.rebindEl(); 
                  },
                  buttons : {
                      OK : function (){  
                             if (ui.item == null) { 
                                clearAutoCompleteInput(obj,'hidSupplierKey');	
                                tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                                tabObj.find("[name=hidCurrentSupplierName]" ).val(''); 
                             }else{
                                tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                                tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value);  
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
                    clearAutoCompleteInput(obj,'hidSupplierKey');	
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(''); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(''); 
                 }else{ 
                    tabObj.find("[name=hidCurrentSupplierKey]" ).val(ui.item.pkey); 
                    tabObj.find("[name=hidCurrentSupplierName]" ).val(ui.item.value); 
                     
                 } 	
                
                 thisObj.rebindEl();  
                          
            } 	 
    }

		
	/// > sampe sini
 
 	 this.onChangeChk = function onChangeChk(){   
        thisObj.calculateTotal();
    }
 
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
     thisObj.calculateTotal(); 
    }
  
    this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
        thisObj.calculateTotal(); 
    }

    this.rebindEl = function rebindEl(){  
		bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
        bindEl(tabObj.find("[name='amount[]']"),'change', function() { thisObj.calculateTotal(); });  
        bindAutoCompleteForTransactionDetail('apCode[]',objAndValueForDetailAutoComplete,'ajax-ap-payable-tax.php?action=searchData&supplierkey=' + tabObj.find("[name=hidSupplierKey]").val() + '&warehousekey='  +  tabObj.find("[name=selWarehouseKey]" ).val(),thisObj.updateDetail);  

    
        // utk voucher kepake, biar bisa auto add new row
        var tablePaymentMethodDetail = tabObj.find(".mnv-payment-method");   
        bindEl(tablePaymentMethodDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
        bindEl(tablePaymentMethodDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tablePaymentMethodDetail, 'payment-method-row-template'); });
      }

    this.loadOnReady = function loadOnReady(){ 
  
        if(thisObj.useStorage){
            
        }else{
          
			if(id){    
				for($i=0;$i<rsFile.length;$i++) 
					arrFile.push(rsFile[$i].file); 

				createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  

			}else{
				createFileUploader(fileUploaderTarget,fileFolder, "", "", false);
			}  
        }
		
                 
        tabObj.find("[name=chkDatePeriod]").bind( "change", function(event) { 
            var checked = ($(this).val() == 1) ? true : false;
            var dateObj = tabObj.find("[name=trStartDate], [name=trEndDate]");
            
            dateObj.removeClass("force-readonly");
             
            dateObj.datepicker((checked) ? "enable" : "disable"); 
            
            if(!checked) dateObj.addClass("force-readonly");
         });
        tabObj.find("[name=chkDatePeriod]").change();
        
        
        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})  
        tabObj.find("[name=btnImport]").on('click', function() {thisObj.importData(); }); 
        tabObj.find(" [name=btnUpdateNTPN]").on('click', function() {thisObj.updateNTPN(); }); 
        tabObj.find("[name=selWarehouseKey]").on('change', function() {thisObj.rebindEl(); });

        tabObj.find("[name='chkPick-master']").val(1).change();    
        thisObj.rebindEl(); 
         
    }
}
