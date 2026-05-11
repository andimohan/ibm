function VatOut(tabID, data, uploadFileFolder, rsFile, varConstant){   
	 
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    
    var id = tabObj.find("[name=hidId]").val();

    var fileFolder = uploadFileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = Array(); 
	
	var  objAndValue = new Array;
    objAndValue.push({object:'hidInvoiceKey[]', value :'pkey'});
//    objAndValue.push({object:'hidTaxInvoiceKey[]', value :'reftaxinvoicekey'});
//    objAndValue.push({object:'taxInvoiceNumber[]', value :'taxinvoicenumber', preventhandling : true}); 
    objAndValue.push({object:'selCurrency[]', value :'currencykey'});   
    objAndValue.push({object:'invoiceDate[]', value :'trdate', type : 'date'});   
    objAndValue.push({object:'total[]', value :'aftertaxdetailvalue'});   
    objAndValue.push({object:'beforeTaxTotal[]', value :'beforetaxtotal'});   
    objAndValue.push({object:'taxValue[]', value :'taxvalue'});   
    objAndValue.push({object:'customerName[]', value :'customername'});   
    objAndValue.push({object:'address[]', value :'customeraddress'});   
    objAndValue.push({object:'npwp[]', value :'npwp'});   
    objAndValue.push({object:'selDetailTransactionTypeCodeKey[]', value :'transactiontypekey'});   
    var objAndValueForDetailAutoComplete = objAndValue;
	
	
	var  objAndValue = new Array;
    objAndValue.push({object:'hidTaxInvoiceKey[]', value :'pkey'});  
    var objAndValueForTaxDetailAutoComplete = objAndValue;
	
    this.importData = function importData() {
        loadOverlayScreen({ content: _LOADING_TEMPLATE_ });
        thisObj.activeAjaxConnections = 0;

        var warehousekey = tabObj.find("[name=selWarehouseKey]").val();
        var businessUnitKey = tabObj.find("[name=selBusinessUnitKey]").val();
        var taxPeriod = tabObj.find("[name=taxPeriod]").val(); 
        var taxPercentage  =  parseFloat(tabObj.find("[name=selTransactionTypeCodeKey]").find('option:selected').attr('rel-percentage')) || 0 ; // perlu ambil nilai -1 takutnya
 
    var url = '';
        if (varConstant.PLAN_TYPE.categorykey == varConstant.COMPANY_TYPE.trucking) {
            //TRUCKING
            url = 'ajax-trucking-service-order-invoice.php';
        } else {
            //FORWARDING
           url = 'ajax-emkl-order-invoice.php';
        }

//         url = 'ajax-emkl-order-invoice.php';
        
        var ajaxData = "action=searchDataForVatOut&taxType=" + taxPercentage + "&warehouseKey=" + warehousekey  + "&period=" + taxPeriod ;

        $.ajax({
            type: "GET",
            url:url,
            beforeSend: function (xhr) {
 				thisObj.resetDetails();
                thisObj.activeAjaxConnections++;
            },
            data: ajaxData,
            success: function (data) {

				if (!data) return;
				
                var data = JSON.parse(data);
  
                var i;
                for (i = 0; i < data.length; i++) {
                    var arrPostValue = [];
                    arrPostValue.push({ "selector": "hidInvoiceKey", "value": data[i].pkey });
                    arrPostValue.push({ "selector": "invoiceNumber", "value": data[i].code });
                    arrPostValue.push({ "selector": "transactionType", "value": data[i].invoicetype });
                    arrPostValue.push({ "selector": "selCurrency", "value": data[i].currencykey });
                    arrPostValue.push({ "selector": "invoiceDate", "value": moment(data[i].trdate).format("DD / MM / YYYY") });
                    
					if (data[i].npwp != '')
						arrPostValue.push({ "selector": "npwp", "value": data[i].npwp });
					else if (data[i].nik != '')
						arrPostValue.push({ "selector": "npwp", "value": data[i].nik });
					else if (data[i].passport != '')
						arrPostValue.push({ "selector": "npwp", "value": data[i].passport });
					
                    arrPostValue.push({ "selector": "customerName", "value": data[i].customername });
                    arrPostValue.push({ "selector": "address", "value": data[i].customeraddress});
                    arrPostValue.push({ "selector": "taxPercentage", "value": data[i].taxpercentage });
                    arrPostValue.push({ "selector": "total", "value": data[i].aftertaxdetailvalue });
                    arrPostValue.push({ "selector": "beforeTaxTotal", "value": data[i].beforetaxtotal });
                    arrPostValue.push({ "selector": "taxValue", "value": data[i].taxvalue });

                    addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));
                }
                    
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

				thisObj.rebindEl();
                decreaseActiveAjaxConnections(thisObj);
                
            },
            error: function (xhr, errDesc, exception) {
                decreaseActiveAjaxConnections(thisObj);
            }
        });
        
    }

	this.resetDetails = function resetDetails(){  
        clearAllRows(tabObj.find(".mnv-transaction")); 
    }

	
	this.onChangeTaxPercentage = function onChangeTaxPercentage(){

		// kalo masih sama jensi pajaknya, return langsung saja
		var currTaxPercentage = $("[name=hidCurrentTaxPercentage]").val();
		var taxPercentage = $("[name=selTransactionTypeCodeKey]").find("option:selected").attr("rel-percentage");
		  
		console.log(currTaxPercentage +"=="+ taxPercentage);
		
		if (currTaxPercentage == taxPercentage) {
			tabObj.find("[name=hidCurrentTransactionTypeCodeKey]").val(tabObj.find("[name=selTransactionTypeCodeKey]").val());
			$("[name=hidCurrentTaxPercentage]").val(taxPercentage);
			return;
		}
		
        $( "#dialog-message" ).html("Jenis pajak yang Anda pilih berbeda dengan sebelumnya.<br>Semua detail transaksi akan dihapus jika Anda mengganti jenis pajak.");
        $( "#dialog-message" ).dialog({
          width: 300,
          modal: true,
          title:"Konfirmasi Perubahan Jenis Pajak", 
          close:function() {
                tabObj.find("[name=selTransactionTypeCodeKey]").val(tabObj.find("[name=hidCurrentTransactionTypeCodeKey]" ).val()); 
          }, 
          open: function() {
              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
          }, 
          buttons : {
              OK : function (){     
                    tabObj.find("[name=hidCurrentTransactionTypeCodeKey]").val(tabObj.find("[name=selTransactionTypeCodeKey]").val());
                  	$("[name=hidCurrentTaxPercentage]").val(taxPercentage);
				  	thisObj.resetDetails(); 
 
					if (taxPercentage < 0 ) taxPercentage = 0;

					$("[name=taxPercentage]").val(taxPercentage).blur();

                   $( this ).dialog( "close" );
              },
              Cancel : function (){  
                    tabObj.find("[name=selTransactionTypeCodeKey]").val(tabObj.find("[name=hidCurrentTransactionTypeCodeKey]" ).val()); 
                    $( this ).dialog( "close" );
              }
          } 

        });	
    }
	 
	this.updateDetail = function updateDetail(target,objAndValue,ui){   
 
		var detailRow = $(target).closest(".transaction-detail-row");

		for(i=0;i<objAndValue.length;i++){   
			
			if (objAndValue[i].type == "date")
			   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);

			var detailRowObj = detailRow.find("[name='" + objAndValue[i].object +"']").first();
			detailRowObj.val(ui.item[objAndValue[i].value]);
			 
			if ( objAndValue[i].preventhandling == undefined || objAndValue[i].preventhandling == false){ 
				detailRowObj.change().blur();
			}
				
			// utk npwp 
			if (ui.item['npwp'] == ''){
				$altNPWP = '';
				
				if (ui.item['nik'] != '')
					$altNPWP = ui.item['nik'];
				else if (ui.item['passport'] != '')  
					$altNPWP = ui.item['passport'];
 
				detailRow.find("[name=\"npwp[]\"]").first().val($altNPWP);   
			}
				 
		}

		// biar gk looping forever
		detailRow.find("[name=\"hidTaxInvoiceKey[]\"]").first().val(ui.item['reftaxinvoicekey']);  
		detailRow.find("[name=\"taxInvoiceNumber[]\"]").first().val(ui.item['taxinvoicenumber']);  
		detailRow.find("[name=\"invoiceNumber[]\"]").first().val(ui.item['code']); 
    }

	this.updateTransactionType = function updateTransactionType(){
		var transactionType = tabObj.find("[name=selVatOutType]").val();
	    tabObj.find("[name='taxInvoiceNumber[]']").attr("readonly", (transactionType == 2 || transactionType == 3) ? true : false ); 
		tabObj.find(".type-filter").hide()
		tabObj.find(".type-" + transactionType).show();
		
		thisObj.rebindInvoice();   
	}
	
   this.rebindInvoice = function rebindInvoice(){
	    var warehousekey = tabObj.find("[name='selWarehouseKey']").val();
	    var businessunitkey = tabObj.find("[name='selBusinessUnitKey']").val();
	    var periodName = tabObj.find("[name='taxPeriod']").val();
	  	var taxPercentage =  parseFloat(tabObj.find("[name=selTransactionTypeCodeKey]").find('option:selected').attr('rel-percentage')) || 0 ; // perlu ambil nilai -1 takutnya
	  	
	   var transactionType = tabObj.find("[name=selVatOutType]").val();
	   
	   // kalo add FP, maka perlu kirim periode dana jenis pajak
	   var additionlParam = '';
	   if (transactionType == 1)
		   additionlParam = '&period='+periodName+'&taxType='+taxPercentage;
	   else
		   additionlParam = '&hastaxinvoice=1';

	   	var ajaxUrl = '';
 		if (varConstant.PLAN_TYPE.categorykey == varConstant.COMPANY_TYPE.trucking) {
           ajaxUrl = 'ajax-trucking-service-order-invoice.php';
        } else {
             ajaxUrl = 'ajax-emkl-order-invoice.php';
        }
		

	  	bindAutoCompleteForTransactionDetail('invoiceNumber[]',  objAndValueForDetailAutoComplete,ajaxUrl+'?action=searchDataForVatOut&warehouseKey='+warehousekey+additionlParam,thisObj.updateDetail); 
  }

    this.rebindTaxInvoice = function rebindTaxInvoice(){
	    var warehousekey = tabObj.find("[name='selWarehouseKey']").val();
	    var businessunitkey = tabObj.find("[name='selBusinessUnitKey']").val();
	    var periodName = tabObj.find("[name='taxPeriod']").val();
	 
	  	bindAutoCompleteForTransactionDetail('taxInvoiceNumber[]',  objAndValueForTaxDetailAutoComplete,'ajax-tax-invoice-number.php?action=searchAvailableTaxNumber&warehouseKey='+warehousekey+'&period='+periodName); 
  }

    this.rebindEl = function rebindEl(){   
         bindEl(tabObj.find("[name='dummychkPick[]']"),'change', function() { updateChkMaster(this,thisObj.onChangeChk); });   
         bindEl(tabObj.find("[name='selWarehouseKey'],[name='selBusinessUnitKey'],[name='taxPeriod']"),'change', function() { thisObj.rebindInvoice(); thisObj.rebindTaxInvoice() });   
		 bindEl(tabObj.find("[name='selTransactionTypeCodeKey']"),'change', function() {  thisObj.onChangeTaxPercentage(); thisObj.rebindInvoice(); thisObj.rebindTaxInvoice() });   
	
		thisObj.rebindInvoice();   
		thisObj.rebindTaxInvoice();   
    } 
     
    this.loadOnReady = function loadOnReady() { 
		
		
//        if(id){    
//            for($i=0;$i<rsFile.length;$i++) 
//                arrFile.push(rsFile[$i].file); 
//            
//            createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  
//            
//        }else{  
//             createFileUploader(fileUploaderTarget, fileFolder, "", "", false);
//        }
//		
        
//        tabObj.find("[name=dummychkPick-master]").change(function(){updateChkPick(this,thisObj.onChangeChk)})   
//        tabObj.find("[name='chkPick-master']").val(1).change();  
		
		tabObj.find("[name=selVatOutType]").change(function(){thisObj.updateTransactionType()}) 
		tabObj.find("[name=selVatOutType]").change();
		 
        thisObj.rebindEl();   
		
        tabObj.find("[name=btnImport]").on('click', function () { thisObj.importData(); });
        
//         if (!data['rsDetail'] || data['rsDetail'].length <= 0)
//            addNewTemplateRow("vat-out-row-template", null, null, thisObj.rebindEl);
        
    }
    
}
