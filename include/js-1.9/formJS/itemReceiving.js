function ItemReceiving(tabID, fileUpload){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

    var fileFolder = fileUpload.uploadFolder;
	var fileUploaderTarget = fileUpload.uploaderTarget;
	var rsFile = fileUpload.rsFile;	 
    var id = tabObj.find("[name=hidId]").val();

	var arrFile = Array();

    // var objAndValue = new Array;  
	// objAndValue.push({object:'hidItemDetailKey[]', value :'pkey'}); 
    // objAndValue.push({object:'itemDetailName[]', value :'name'}); 
    // var objAndValueForDetailAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailTypeKey[]', value :'pkey'}); 
    objAndValue.push({object:'detailType[]', value :'name'}); 
    var objAndValueForDetailCategoryItemAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailCountryKey[]', value :'pkey'}); 
    objAndValue.push({object:'countryOfOriginId[]', value :'name'}); 
    var objAndValueForDetailCountryAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailBrandKey[]', value :'pkey'}); 
    objAndValue.push({object:'brandName[]', value :'name'}); 
    var objAndValueForDetailBrandAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailBrandKey[]', value :'pkey'}); 
    objAndValue.push({object:'itemCategoryDetailName[]', value :'name'}); 
    var objAndValueForCategoryDetailAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidPackagingDetailKey[]', value :'pkey'}); 
    objAndValue.push({object:'packagingDetailName[]', value :'name'}); 
    var objAndValueForPackagingDetailAutoComplete = objAndValue;  

    this.updateDetail = function updateDetail(target,objAndValue,ui){
             
        var detailRow = $(target).closest(".transaction-detail-row");
        var itemKeyObj = detailRow.find("[name=\"hidItemDetailKey[]\"]").first();

        for(i=0;i<objAndValue.length;i++){   
            detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
        } 

        // harus handle manual utk obj autosearch
        detailRow.find("[name=\"itemDetailName[]\"]").first().val(ui.item['value']);

    }

    this.resetHeader = function resetHeader(){
        tabObj.find("[name=selWarehouseKey]")
        .find('option:first')
        .prop('selected', true)
        .closest('select')
        .change(); 

        tabObj.find("[name=selWarehouseLayoutKey]")
            .find('option:first')
            .prop('selected', true)
            .closest('select')
            .change(); 

            tabObj.find("[name=hidCustomerKey]").val(""); 
            tabObj.find("[name=customerName]").val("");

            tabObj.find("[name=hidSupplierKey]").val(""); 
            tabObj.find("[name=supplierName]").val("");

            tabObj.find("[name=hidShipperKey]").val(""); 
            tabObj.find("[name=shipperName]").val("");

            tabObj.find("[name=selDocumentType]").val("").find('option:first')
            .prop('selected', true)
            .closest('select')
            .change();  

            tabObj.find("[name=submissionNumber]").val(""); 
            tabObj.find("[name=submissionDate]").val(moment().format('DD / MM / YYYY')); 
            tabObj.find("[name=invoiceNumber]").val(""); 
            tabObj.find("[name=invoiceDate]").val(moment().format('DD / MM / YYYY')); 
            tabObj.find("[name=blNumber]").val(""); 
            tabObj.find("[name=blDate]").val(moment().format('DD / MM / YYYY')); 
            tabObj.find("[name=registrationNumber]").val(""); 
            tabObj.find("[name=registrationDate]").val(moment().format('DD / MM / YYYY')); 

            tabObj.find("[name=selCurrency]").val("").find('option:first')
            .prop('selected', true)
            .closest('select')
            .change();  

            tabObj.find("[name=valueType]").val("");
            tabObj.find("[name=trDesc]").val("");
    }

    this.updateItemReceivingPlanInformation = function updateItemReceivingPlanInformation(obj,event, ui){
            console.log("OK");
            if (tabObj.find("[name=hidCurrentItemReceivingPlanKey]" ).val() != ''){
					$( "#dialog-message" ).html("Merubah Rencana Penerimaan Barang akan mereset detail transaksi.");
					$( "#dialog-message" ).dialog({
					  width: 300,
					  modal: true,
					  title:"Konfirmasi Perubahan Data Rencana Penerimaan Barang", 
					  open: function() {
						  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
					  },
					  close:function() {
							tabObj.find("[name=hidItemReceivingPlanKey]" ).val(tabObj.find("[name=hidCurrentItemReceivingPlanKey]" ).val());
							tabObj.find("[name=itemReceivingPlanPlanCode]" ).val(tabObj.find("[name=hidCurrentItemReceivingPlanCode]" ).val());
                          
                          	// $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));  
                          
                            thisObj.rebindEl(); // harus taro didalam, kalo gk, async, variable belum sempet berubah
                            
					  },
					  buttons : {
						  OK : function (){  
						  		 if (ui.item == null) { 
									clearAutoCompleteInput(obj,'hidItemReceivingPlanKey',false);	
									tabObj.find("[name=hidCurrentItemReceivingPlanKey]" ).val(''); 
									tabObj.find("[name=hidCurrentItemReceivingPlanCode]" ).val(''); 
                                    
								 }else{
									tabObj.find("[name=hidCurrentItemReceivingPlanKey]" ).val(ui.item.pkey); 
									tabObj.find("[name=hidCurrentItemReceivingPlanCode]" ).val(ui.item.value);

                                    thisObj.updateDataFromItemReceivingPlan();
								 } 
								
                                thisObj.resetHeader();
								thisObj.resetDetails(); 

                                addNewTemplateRow("detail-row-template");

								$( this ).dialog( "close" );
						  },
						  Cancel : function (){  
						  		$( this ).dialog( "close" );
						  }
					  },
					});	 
				}else{ 
					 if (ui.item == null) {
						clearAutoCompleteInput(obj,'hidItemReceivingPlanKey',false);	
						tabObj.find("[name=hidCurrentItemReceivingPlanKey]" ).val(''); 
						tabObj.find("[name=hidCurrentItemReceivingPlanCode]" ).val(''); 
					 }else{ 
						tabObj.find("[name=hidCurrentItemReceivingPlanKey]" ).val(ui.item.pkey); 
						tabObj.find("[name=hidCurrentItemReceivingPlanCode]" ).val(ui.item.value);

                        thisObj.updateDataFromItemReceivingPlan();
                         
					 } 	
					  
                    thisObj.rebindEl();
				} 	    
             
    }

    this.updateDataFromItemReceivingPlan = function updateDataFromItemReceiving() {
        
        var pkey = tabObj.find("[name=hidItemReceivingPlanKey]").val();
    
        if (!pkey) return;
        console.log('   update');
        $.ajax({
            type: "GET",
            url:  'ajax-item-receiving-plan.php',
            async: false,
            data: "action=getDataForItemReceiving&pkey=" + pkey ,  
        }).done(function(data) { 

            data = JSON.parse(data) ; 
                     
            if(data.length == 0){ 
                alert(phpErrorMsg[213])
                return;
            };

            data = data[0];

            
            tabObj.find("[name=selWarehouseKey]").val(data.warehousekey).change(); 


            thisObj.updateWarehouseLayout(function() {
                tabObj.find("[name=selWarehouseLayoutKey]")
                    .val(data.warehouselayoutkey)
                    .trigger('change');
            });

            tabObj.find("[name=hidCustomerKey]").val(data.customerkey); 
            tabObj.find("[name=customerName]").val(data.customername);

            tabObj.find("[name=hidSupplierKey]").val(data.supplierkey); 
            tabObj.find("[name=supplierName]").val(data.suppliername);

            tabObj.find("[name=hidShipperKey]").val(data.shipperkey); 
            tabObj.find("[name=shipperName]").val(data.shippername);

            tabObj.find("[name=selDocumentType]").val(data.documenttype).change(); 

            tabObj.find("[name=submissionNumber]").val(data.submissionnumber); 
            tabObj.find("[name=submissionDate]").val(moment(data.submissiondate).format(_DATE_FORMAT_)); 
            tabObj.find("[name=invoiceNumber]").val(data.invoicenumber); 
            tabObj.find("[name=invoiceDate]").val(moment(data.invoicedate).format(_DATE_FORMAT_)); 
            tabObj.find("[name=blNumber]").val(data.blnumber); 
            tabObj.find("[name=blDate]").val(moment(data.bldate).format(_DATE_FORMAT_)); 
            tabObj.find("[name=registrationNumber]").val(data.registrationnumber); 
            tabObj.find("[name=registrationDate]").val(moment(data.registrationdate).format(_DATE_FORMAT_)); 

            tabObj.find("[name=selCurrency]").val(data.currencykey).change(); 
            tabObj.find("[name=valueType]").val(data.valuetype);
            tabObj.find("[name=trDesc]").val(data.trdesc) 

            var detail = data.details;

            if(detail.length > 0) {

                
                thisObj.resetDetails(); 
                
                var i;
                for(i=0;i<detail.length;i++){  

                    var arrPostValue = []; 
                    arrPostValue.push({"selector":"itemDetailCode", "value":detail[i].itemcode});
                    arrPostValue.push({"selector":"itemDetailName", "value":detail[i].itemname}); 
                    arrPostValue.push({"selector":"mililiter", "value":detail[i].mililiter}); 
                    arrPostValue.push({"selector":"hidDetailBrandKey", "value":detail[i].brandkey});
                    arrPostValue.push({"selector":"brandName", "value":detail[i].brandname}); 
                    arrPostValue.push({"selector":"hidDetailTypeKey", "value":detail[i].typekey}); 
                    arrPostValue.push({"selector":"detailType", "value":detail[i].typename}); 
                    arrPostValue.push({"selector":"qtyCarton", "value":detail[i].qtycarton}); 
                    arrPostValue.push({"selector":"qtyPackage", "value":detail[i].qtypackage}); 
                    arrPostValue.push({"selector":"qty", "value":detail[i].qty}); 
                    arrPostValue.push({"selector":"alcoholContent", "value":detail[i].alcoholcontent}); 
                    arrPostValue.push({"selector":"amount", "value":detail[i].amount}); 
                    arrPostValue.push({"selector":"label", "value":detail[i].label}); 
                    arrPostValue.push({"selector":"hs", "value":detail[i].hs}); 
                    arrPostValue.push({"selector":"selTransactionType", "value":detail[i].transactiontypekey}); 
                    arrPostValue.push({"selector":"category", "value":detail[i].category}); 
                    arrPostValue.push({"selector":"selUnit", "value":detail[i].unit}); 
                    arrPostValue.push({"selector":"packagingName", "value":detail[i].packaging}); 
                    arrPostValue.push({"selector":"hidDetailCountryKey", "value":detail[i].countrykey}); 
                    arrPostValue.push({"selector":"countryOfOriginId", "value":detail[i].countryoforiginid}); 
                    arrPostValue.push({"selector":"containerNumber", "value":detail[i].containernumber}); 
                    arrPostValue.push({"selector":"containerType", "value":detail[i].containertype}); 
                    arrPostValue.push({"selector":"containerSize", "value":detail[i].containersize}); 
                    arrPostValue.push({"selector":"containerType", "value":detail[i].containertype}); 
                         
                    addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                }

            } else {
                addNewTemplateRow("detail-row-template");
            }

        });

        thisObj.rebindEl();
    }

    this.resetDetails = function resetDetails(){   
        clearAllRows(tabObj.find(".mnv-transaction"));
        thisObj.rebindEl();
        thisObj.calculateTotal(); 

    } 
        
    this.updateWarehouseLayout = function updateWarehouseLayout(callback) {
        var selWarehouseKey = tabObj.find("[name=selWarehouseKey]").val();

        if (!selWarehouseKey) {
            if(callback) callback(); 
            return;
        }

        $.ajax({
            type: "GET",
            url: "ajax-warehouse-layout.php",
            data: "action=getDataLayout&warehousekey=" + selWarehouseKey + '&istransit=1',
            success: function (data) {
                if (!data) {
                    if(callback) callback();
                    return;
                }

                var data = parseJSON(data);
                var i;
                var newOptions = {};
                
                for (i = 0; i < data.length; i++) {
                    if (data[i].name) {
                        newOptions[data[i].pkey] = data[i].name; 
                    }
                }
                
                var select = $("#" + tabID + " [name=selWarehouseLayoutKey]");
                var oldValue = select.val(); 

                if (select.prop) {
                    var options = select.prop('options');
                } else {
                    var options = select.attr('options');
                }

                $('option', select).remove();

                $.each(newOptions, function(val, text) {
                    options[options.length] = new Option(text, val);
                });

                var optionExists = false;
                if(oldValue) {
                    if(select.find("option[value='" + oldValue + "']").length > 0) {
                        optionExists = true;
                    }
                }

                if (optionExists) {
                    select.val(oldValue).change();
                } else {
                    select.find('option:eq(0)').prop('selected', true).change();
                }

                if(callback) callback();
            }
        });
    }

    // this.updateWarehouseLayout = function updateWarehouseLayout()
    // {
    //     var selWarehouseKey = tabObj.find("[name=selWarehouseKey]").val();
    //     // var selWarehouseLayoutKey = tabObj.find("[name=selWarehouseLayoutKey]").val();

    //     if (!selWarehouseKey ) {
    //         return;
    //     }

    //     $.ajax({
    //         type: "GET",
    //         url: "ajax-warehouse-layout.php",
    //         data: "action=getDataLayout&warehousekey="+selWarehouseKey+'&istransit=1',
    //         success: function (data) {
    //             if (!data) return;

    //             var data = parseJSON(data);
    //             var i;
    //             var newOptions = {};
                
    //             //  tabObj.find("[name=selCurrentWarehouseLayoutKey]" ).val(data[0].pkey); 
    //             for (i = 0; i < data.length; i++) {
    //                 if (data[i].name) {
    //                     newOptions[data[i].pkey] =  data[i].name; 
    //                 }
    //             }
                
    //             var select = $("#" + tabID + " [name=selWarehouseLayoutKey]");
                
    //             var oldValue = select.val(); 

    //             if (select.prop) {
    //                 var options = select.prop('options');
    //             } else {
    //                 var options = select.attr('options');
    //             }

    //             $('option', select).remove();

    //             $.each(newOptions, function(val, text) {
    //                 options[options.length] = new Option(text, val);
    //             });

            
    //             var optionExists = false;
    //             if(oldValue){
    //                 if(select.find("option[value='"+oldValue+"']").length > 0){
    //                     optionExists = true;
    //                 }
    //             }

    //             if (optionExists) {
    //                 select.val(oldValue).change();
    //             } else {
    //                 select.find('option:eq(0)').prop('selected', true).change();
    //             }

    //         }
    //     });
    
    //}

    this.calculateTotal = function calculateTotal()
    {

    }

    this.updateTotalQty = function updateTotalQty(obj)
    {
        var serviceRow = $(obj).closest(".transaction-detail-row");  

        var qtyPackage =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyPackage[]']").val())) || 0;
        var qtyCarton =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyCarton[]']").val())) || 0;

        var total = qtyPackage * qtyCarton;

        serviceRow.find("[name='qty[]']").val(total).blur(); 
    }

    this.updateLabel = function updateLabel(obj){
        var serviceRow = $(obj).closest(".transaction-detail-row");  
          
        // var qty =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyDetail[]']").val())) || 0;
        // var price = parseFloat(unformatCurrency(serviceRow.find("[name='priceInUnitDetail[]']").val())) || 0;
        // var discount =  unformatCurrency(serviceRow.find("[name='discountValueDetail[]']").val());
        var itemName =  serviceRow.find("[name='itemDetailName[]']").val();
        // var milimeter =  serviceRow.find("[name='mililiter[]']").val() || '';
        var milimeter =  parseFloat(unformatCurrency(serviceRow.find("[name='mililiter[]']").val())) || 0;
        var brand =  serviceRow.find("[name='brandName[]']").val();
        var itemType =  serviceRow.find("[name='detailType[]']").val();
        var qtyCarton =  serviceRow.find("[name='qtyCarton[]']").val();
        var alcohol =  serviceRow.find("[name='alcoholContent[]']").val();

        var  merk = '';
        var  size = '';
        var  sizeInfo = '';
        if (brand != '') {
            merk = ' Merk : ' + brand;
        }

        if (itemType != '') {
            itemType = ', Tipe : ' + itemType;
        }

        if (milimeter == 0) {
            size = '';
        } else {
            size = ' ' + milimeter + ' ML'
        }

        if (milimeter != 0 && qtyCarton != 0) {
            sizeInfo = ', Ukuran : '+qtyCarton+ ' X ' +milimeter;
        }

        if (alcohol == 0) {
            alcoholContent = '';
        } else {
            alcoholContent = ', Spesifikasi lain: '+ alcohol + '%'
        }

        
        var label = itemName + size + merk+itemType + sizeInfo + alcoholContent;
        serviceRow.find("[name='label[]']").val(label); 
    }

    this.cloneRowDetailValue = function cloneRowDetailValue() {
        // var detailRow = $(obj).closest('.transaction-detail-row');
        var detailRow = tabObj.find('.transaction-detail-row').last();

        var alcohol =  detailRow.find("[name='alcoholContent[]']").val();

        var hasValue = false;
        var temp     = [];   // simpan dulu

        detailRow.find('input[name], select[name], textarea[name]').each(function () {
            var $field = $(this);
            var name   = $field.attr('name');
            
            if (!name) return;

            var val = $field.val();

            if (val != "" && val != null && val != undefined && val != 0) {
                hasValue = true;
            }
        
            if (name === 'hidDetailKey[]' || name.endsWith('[hidDetailKey[]]')) {
                val = 0;   
            }

            temp.push({
                selector: name.replace(/\[\]$/, ''), 
                value   : val
            });
        });

        var arr = hasValue ? JSON.stringify(temp) : null;

        addNewTemplateRow(
            "detail-row-template",
            arr
        );

        thisObj.rebindEl();
    }
      
    this.rebindEl = function rebindEl(){   
        // bindAutoCompleteForTransactionDetail('itemDetailName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&limit=25');
        bindAutoCompleteForTransactionDetail('brandName[]',objAndValueForDetailBrandAutoComplete,'ajax-brand.php?action=searchData&limit=25');
        bindAutoCompleteForTransactionDetail('countryOfOriginId[]',objAndValueForDetailCountryAutoComplete,'ajax-country.php?action=searchData&limit=25');
        bindAutoCompleteForTransactionDetail('detailType[]',objAndValueForDetailCategoryItemAutoComplete,'ajax-item-category.php?action=searchData&limit=25');

        bindEl(tabObj.find("[name='itemDetailName[]'], [name='mililiter[]'], [name='brandName[]'], [name='detailType[]'], [name='qtyCarton[]'], [name='alcoholContent[]']"),'change', function() { thisObj.updateLabel(this); });
        bindEl(tabObj.find("[name='qtyPackage[]'], [name='qtyCarton[]']"),'change', function() { thisObj.updateTotalQty(this); });
    } 
     
    this.loadOnReady = function loadOnReady(){ 
        if(tabObj.find(".file-uploader").length > 0){
             if(id){    
                for($i=0;$i<rsFile.length;$i++)  arrFile.push(rsFile[$i].file); 
                    createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  

                }else{
                    createFileUploader(fileUploaderTarget,fileFolder, "", "", false);
                }

          }
        
        tabObj.find("[name=selWarehouseKey]").change(function() { thisObj.updateWarehouseLayout(); });
        // tabObj.find("[name=selWarehouseKey]").change();

        
        thisObj.rebindEl(); 

        tabObj
            .off('click', "[name='btnAddRow']")
            .on('click', "[name='btnAddRow']", function (e) {
                thisObj.cloneRowDetailValue();
            });

    }
    
}
