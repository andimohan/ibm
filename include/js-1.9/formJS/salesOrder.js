function SalesOrder(tabID, rs, cashTOP, arrVoucher,varConstant,  uploadFolder, rsFile) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
    	var id = tabObj.find("[name=hidId]").val();
    
        this.tabObj = tabObj;
    	this.tablekey = varConstant.TABLEKEY;  
        this.useStorage = varConstant.USE_STORAGE;  
        this.weightCalc = varConstant.WEIGHT_CALCULATION;  
     
    
        this.rs = (rs.length > 0) ? rs[0] : null;
        this.customCodeCache=[];
     
		var fileFolder = varConstant.uploadFileFolder;
		var fileUploaderTarget = "item-file-uploader";
		var rsFile = varConstant.rsFile || Array(); 
		var arrFile = Array(); 

		var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
        objAndValue.push({object: 'hidNeedSN[]',value: 'needsn'});
        var objAndValueForDetailAutoComplete = objAndValue;  
        
	
		var objAndValue = new Array;   
        objAndValue.push({object:'hidVoucherKey[]', value :'pkey'}); 
        //objAndValue.push({object:'voucherCode[]', value :'code'});   
        //objAndValue.push({object:'hidVoucherDiscountType[]', value :'discounttype'});  
        //objAndValue.push({object:'hidMaxDiscount[]', value :'maxdiscount'});  
        objAndValue.push({object:'voucherAmount[]', value :'vouchervalue'});  
        var objAndValueForVoucherDetailAutoComplete = objAndValue;
 
        this.tabID = tabID;    
     
        this.snRegex = varConstant.SN_REGEX;

		var arrVoucherKey = new Array;
		$.each( arrVoucher, function( key, value ) {
		  arrVoucherKey.push(value['voucherkey']);
		});
	
		arrVoucherKey = JSON.stringify(arrVoucherKey);
	
        this.updateDetail = function updateDetail(target,objAndValue,ui){

                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first(); 
              
                disabledButton(detailRow.find("[name=btnMoreOptions]"));
                detailRow.find(".options-row").hide();


                for(i=0;i<objAndValue.length;i++){    
                    //overwrite kalo kg
                    if(objAndValue[i].object == 'hidGramasi[]' && ui.item['weightunitkey'] == 2)
                        ui.item[objAndValue[i].value] *= 1000;  
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
            
                        

                updateAvailableUnit(itemKeyObj, selUnitObj);
                thisObj.updateUnitPrice(selUnitObj);
            
                // klao ad detail packaging, utk jewelry
                if (tabObj.find("[name='selPackagingCode[]']").length > 0)
                    thisObj.getAvailablePackaging(itemKeyObj.val(), detailRow);
            
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
                
                if (ui.item['needsn'] == 1) {
                    calculateSNNeeded(tabObj, target);
                }

                thisObj.calculateDetail(itemKeyObj);

                updateSNOptions(tabObj, detailRow);
 
         }
		
		this.calculateVoucherAmount = function calculateVoucherAmount(target,objAndValue,ui){
			 	var detailRow = $(target).closest(".transaction-detail-row");  
               
                for(i=0;i<objAndValue.length;i++){     
					
					if(objAndValue[i].object == 'voucherAmount[]'){
			 			thisObj.recalculateVoucherAmount(detailRow);
						continue;
					}
					
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
					
                } 
			
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"voucherCode[]\"]").first().val(ui.item['value']); 
               
                thisObj.updateVoucherInformation();
                thisObj.calculateTotal(false);
		}
		
		this.recalculateVoucherAmount = function recalculateVoucherAmount(detailRow){
			// sementara voucher cuma 1 baris saja dulu
			
			var totalVoucher = 0;
			
			var subtotal = parseFloat(unformatCurrency(tabObj.find("[name='subtotal']").val())) || 0 ;
			var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0 ;
			var finalDiscount2 = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount2']").val())) || 0 ;
			var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
			var finalDiscountType2 = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
            
			if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 
			
			// diskon level 2
			if (finalDiscount2 > 0){
				var subtotal2 = subtotal - finalDiscount; 
				if (finalDiscount2 != 0 && finalDiscountType2 == 2)  finalDiscount2 = finalDiscount2/100 * subtotal2;  
				finalDiscount += finalDiscount2;
			}
			
			subtotal -= finalDiscount;
            
            // hitung voucher
            var voucherkey = $('[name=\'hidVoucherKey[]\']').val() || 0;
            var vouchertype = $('[name=\'hidVoucherType[]\']').val() || 0; 

//            console.log(voucherkey);
            
            var salesVoucherValue = 0;
            var salesShippingValue = 0;

			$.ajax({
				type: "GET",
				url:  'ajax-voucher-transaction.php',
				async: false,
				data: "action=calculateVoucherValue&voucherkey="+voucherkey+"&vouchertype="+vouchertype+"&totalsales="+totalValue+'&totalshipment='+shipping,  
			}).done(function( data ) {   
				    if(!data) return;  
                    data = JSON.parse(data);
                
                    // sementara sales dulu
                    if (data.categorykey == 2){ // voucher penjualan
                       salesVoucherValue = (parseFloat(data['amount']) || 0) * -1; 
                       detailRow.find("[name='voucherAmount[]']").val(salesVoucherValue).blur();  
                    } 
                    else if (data.categorykey == 3){ // voucher ongkir
					   salesShippingValue = (parseFloat(data['amount']) || 0) * -1;
//                       if (salesShippingValue != 0)   $('.voucher-category-' + data.categorykey).addClass("show");
                    }
                
				    return data;
			});
			
			return;
		}
        
	    this.calculateDetail = function calculateDetail(obj){      
               
                    var row =  $(obj).closest(".transaction-detail-row");   
                    var itemkey =  row.find("[name='hidItemKey[]']").val();
                
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
                    var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
                    var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
                    var selUnitObj = row.find("[name='selUnit[]']");
                    var unitkey =  selUnitObj.val();
                    var conversionmultiplier =  parseFloat(selUnitObj.find("option:selected").attr('relconversionmultiplier'));
                    var gramasi =  parseFloat(row.find("[name='hidGramasi[]']").val());
  
                    if(typeof thisObj.weightCalc !== 'undefined' ){
                        
                        var autoCalculateWeight = thisObj.weightCalc['autoCalculate'] || false;
                        var readonlyWeight = thisObj.weightCalc['readonlyWeight'] || false;
                        
                        if(autoCalculateWeight){ 
                            var totalWeight = qty * gramasi;
                            row.find("[name='qtyInPcs[]']").val(totalWeight).blur();
                        }
                        
                        if(readonlyWeight)
                            row.find("[name='qtyInPcs[]']").attr("readonly",true);
                    }
                    
            
                    // utk jewelry
                    if(tabObj.find("[name='priceInPcs[]']").length > 0){
                        
                        //console.log("in");
                        
                        var priceInPcs = parseFloat(unformatCurrency(row.find("[name='priceInPcs[]']").val())) || 0;
                        var qtyInPcs =  parseFloat(unformatCurrency(row.find("[name='qtyInPcs[]']").val())) || 0;
                        var isPriceInPcs = row.find("[name='chkPriceInPcs[]']").val();
                        
                        var subtotal = 0;
                        var priceInBaseUnit = 0;
                        var priceInPcsVal = 0; 

                        if(isPriceInPcs == 1) {
                            if(qty > 0) {
                                priceInBaseUnit = (qtyInPcs * priceInPcs) / qty;
                            } 

                            row.find("[name='priceInUnit[]']").val(priceInBaseUnit).blur(); 

                            if (discount != 0 && discountType == 2) discount = discount / 100 * priceInPcs; 
                            subtotal = qtyInPcs  *  (priceInPcs - discount);
                        } else {

                            if(qtyInPcs > 0) {
                                priceInPcsVal = (qty * priceInUnit) / qtyInPcs;
                            }

                            row.find("[name='priceInPcs[]']").val(priceInPcsVal).blur(); 

                            if (discount != 0 && discountType == 2) discount = discount / 100 * priceInUnit; 
                            subtotal = qty  *  (priceInUnit - discount);
                        }
               
                   

                    }else{
                        // normal
                        
                        //console.log("normal");
                        
                        if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit; 
                        var subtotal = qty  *  (priceInUnit - discount); 
                    }
               
            
                    row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 
                    row.find("[name='hidGramasiSubtotal[]']").val(gramasi * qty * conversionmultiplier).blur(); 

                    thisObj.calculateTotal();
	       }
	
	    this.calculateTotal = function calculateTotal(recalculateVoucher){  
         			
					if(!recalculateVoucher) recalculateVoucher = true;
			 
                    var subtotal = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
                    tabObj.find("[name='subtotal']").val(subtotal).blur();
			
					// hitung ulang nilai voucher
					var voucherList = tabObj.find("[name='voucherAmount[]']");
					var voucherSalesValue = 0;
                    voucherList.each(function(){
                        voucherSalesValue+= parseFloat( unformatCurrency($(this).val()) ) || 0; 
                    });
            
                    // gk perlu hitung ulaang lg ribet, lagin di class jg dihitugn ulang
            
//					voucherList.each(function(){ 
//						// asumsi cuma satu baris saja dulu
//						var voucherInformation = thisObj.recalculateVoucherAmount($(this).closest(".transaction-detail-row"));
//						amount =  parseFloat(voucherInformation[][]) || 0 ;
//						voucherSalesValue += amount;
//					});

					//console.log(voucherValue);
			
                    var totalGramasi = 0; 
                    tabObj.find("[name='hidGramasiSubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
                    tabObj.find(".total-weight").html(Math.ceil(totalGramasi/1000));
             

					var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0 ;
					var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
					var finalDiscount2 = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount2']").val())) || 0 ;
					var finalDiscountType2 = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType2']").val())) || 0 ;
					var pointValue = parseInt(unformatCurrency(tabObj.find("[name='pointValue']").val())) || 0 ;
					var shipmentFee = parseInt(unformatCurrency(tabObj.find("[name='shipmentFee']").val())) || 0 ; 
					var etcCost = parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0 ; 
					var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
					var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ; 
        
                    
                    if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 

					// level 2
					if(finalDiscount2 > 0){
						var subtotal2 =  subtotal - finalDiscount; 
						if (finalDiscount2 != 0 && finalDiscountType2 == 2)  finalDiscount2 = finalDiscount2/100 * subtotal2; 
						finalDiscount += finalDiscount2;						
					}


                    subtotal -= finalDiscount; 
                    subtotal -= voucherSalesValue;
                    subtotal -= pointValue;

                    tabObj.find("[name='beforeTaxTotal']").val(subtotal).blur();

                    var taxValue = 0;
                    if (includeTax == 0) {
                            taxValue = subtotal * taxPercentage / 100;
                            subtotal += taxValue;
                    }else{
                            taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                            tabObj.find("[name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
                    }

                    tabObj.find("[name='taxValue']").val(taxValue).blur(); 

                    var total = subtotal +  shipmentFee + etcCost;
                    tabObj.find("[name='total']").val(total).blur();
                    
                    var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

                    var balance = totalPayment - total; 
		            tabObj.find("[name='balance']").val(balance).blur();
		 
	       }
         
        this.updateCustomerInformation = function updateCustomerInformation(){ 
			  var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  
 	
			  if(!customerkey)  return;

			   $.ajax({
					type: "GET",
					url:  'ajax-customer.php',
					async: false,
					data: "action=getDataRowById&pkey=" + customerkey ,  
				}).done(function( data ) {  

						data = JSON.parse(data) ; 
						data = data[0];

						var address = data.address ;  

						tabObj.find("[name=hidCreditLimit]").val(data.creditlimit);

						if ($( "#" + tabID +  " [name=chkIsDropship]" ).prop("checked")){ 
							tabObj.find("[name=dropshiperName]").val(data.name);
							tabObj.find("[name=dropshiperPhone]").val(data.phone); 
							tabObj.find("[name=dropshiperAddress]").val(address);
						}else{

							tabObj.find("[name=recipientName]").val(data.name);
							tabObj.find("[name=recipientPhone]").val(data.phone);
							tabObj.find("[name=recipientEmail]").val(data.email);
							tabObj.find("[name=recipientAddress]").val(address);  
							tabObj.find("[name=vaNumber]").val(data.virtualaccount);  
							tabObj.find("[name=hidRecipientCityKey]").val(data.recipientcitykey);  
							tabObj.find("[name=recipientCityName]").val(data.cityandcategoryname);  
							tabObj.find("[name=recipientZipcode]").val(data.zipcode);   
						}

						if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + data.termofpaymentkey + "']").length > 0)
							tabObj.find("[name=selTermOfPaymentKey]").val(data.termofpaymentkey).change(); 

						thisObj.rebindVoucher();  
				});
        }
        
        this.updateUnitPrice = function updateUnitPrice(obj){ 
            var row = $(obj).closest(".transaction-detail-row");
            var unitKey = $(obj).val();
            var itemKey = row.find("[name='\hidItemKey[]\']").val();
            var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;
            
            $.ajax({
                type: "GET",
                url:  'ajax-item.php',
                async: false,
                data: "action=getUnitSellingPrice&itemkey="+itemKey+"&unitkey=" + unitKey +"&lastsellingprice=1&customerkey="+customerkey,  
            }).done(function( data ) {  
				   
				  if (data == '') return;
				
                   data = JSON.parse(data) ; 
                   row.find("[name=\'priceInUnit[]\']").val(data).blur();
            });
        }
        
        this.updateSalesman = function updateSalesman(){

            var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  
            
            //update salesman
            tabObj.find("[name=hidSalesKey]").val("");  
            tabObj.find("[name=salesName]").val("");  

            $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async: false,
                data: "action=getSalesman&pkey=" + customerkey ,  
            }).done(function( data ) {  
                if (!data ) return;

                data = JSON.parse(data) ;  
                if ( data.length  == 0  ) return;

                tabObj.find("[name=hidSalesKey]").val(data.pkey);  
                tabObj.find("[name=salesName]").val(data.name);    

            }); 
 
        }
     
        this.updateRecipients = function updateRecipients(){
          
                    var isDropship = false;

                    var recipientName =  "";
                    var recipientPhone = "";
                    var recipientEmail = "";
                    var recipientAddress =  ""; 


                    if ($( "#" + tabID +  " [name=chkIsDropship]" ).prop("checked")){
                        recipientName = tabObj.find("[name=dropshiperName]" ).val(); 
                        recipientPhone = tabObj.find("[name=dropshiperPhone]" ).val();  
                        recipientAddress = tabObj.find("[name=dropshiperAddress]" ).val();  
                    }else{
                        recipientName = tabObj.find("[name=recipientName]" ).val(); 
                        recipientPhone = tabObj.find("[name=recipientPhone]" ).val(); 
                        recipientEmail = tabObj.find("[name=recipientEmail]" ).val(); 
                        recipientAddress = tabObj.find("[name=recipientAddress]" ).val();  
                    }

                    var informationExist  = false;

                     if (recipientName != "" || recipientPhone  != "" || recipientEmail != "" || recipientAddress != "" )
                         informationExist = true;

                    if (informationExist == false){
                       thisObj.updateCustomerInformation();
                    } else{

                        var obj = this; 
                        $( "#dialog-message" ).html("Apakah Anda ingin mengganti data pengiriman dan pembayaran dengan data default untuk pelanggan ini ?");
                        $( "#dialog-message" ).dialog({
                          width: 300,
                          modal: true,
                          title:"Konfirmasi Perubahan Data Pelanggan", 
                          open: function() {
                              $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                          },
                          close:function() {
                                $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name")); 
                          },
                          buttons : {
                              OK : function (){  
                                    thisObj.updateCustomerInformation();
                                    $( this ).dialog( "close" );
                              },
                              Cancel : function (){  
                                    $( this ).dialog( "close" );
                              }
                          },
                        });	  
                    } 
        }

        this.updateVoucherInformation = function updateVoucherInformation(){
            // update informsi voucher per kategori
            var totalVoucherAmount = Array(); 
            
             tabObj.find("[name='voucherCode[]']").each(function() {
                var categorykey = $(this).closest('.div-table-row').find('[name=\'hidVoucherCategoryKey[]\']').val(); 
                var amount = parseFloat(unformatCurrency($(this).closest('.div-table-row').find('[name=\'voucherAmount[]\']').val())) || 0 ;  
                if (totalVoucherAmount[categorykey] == undefined) totalVoucherAmount[categorykey] = 0; 
                totalVoucherAmount[categorykey] += amount;
             });
            
            var totalSalesDiscount = totalVoucherAmount[2];
            var totalShipmentDiscount = totalVoucherAmount[3]; 
 
            tabObj.find("[name=voucherSalesAmount]").val(totalSalesDiscount * -1).blur();
            tabObj.find("[name=voucherShipmentAmount]").val(totalShipmentDiscount * -1).blur();
            
        }
        
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          	thisObj.calculateTotal();  
          	thisObj.rebindVoucher();  
        }
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         	thisObj.calculateTotal(); 
        }
		
		this.rebindVoucher = function rebindVoucher(){ 
            var customerkey = tabObj.find("[name=hidCustomerKey]").val() || 0;   
            bindAutoCompleteForTransactionDetail('voucherCode[]',objAndValueForVoucherDetailAutoComplete,'ajax-voucher-transaction.php?action=searchData&statuskey=2&customerkey='+customerkey,thisObj.calculateVoucherAmount);  
		}
        

        this.onChangePriceInPcs = function onChangePriceInPcs(obj) {
            
            var row = $(obj).closest(".transaction-detail-row"); 
            var priceInPcs = row.find("[name='chkPriceInPcs[]']").val();

            if (priceInPcs == 0) {
                row.find("[name='priceInUnit[]']").attr("readonly", false); 
                row.find("[name='priceInPcs[]']").attr("readonly", true); 
            } else {
                row.find("[name='priceInUnit[]']").attr("readonly", true);  
                row.find("[name='priceInPcs[]']").attr("readonly", false);
            }

        }

        this.onChangeReadyOrIndent = function onChangeReadyOrIndent()
        {
            var chkValue = tabObj.find("[name=chkIsFullDeliver]").val() ;   
            
            if (chkValue == 1) {
                tabObj.find(".isindent").show();
                tabObj.find(".isready").hide();
            } else {
                tabObj.find(".isindent").hide();
                tabObj.find(".isready").show();
            }
        }

        this.getAvailablePackaging = function getAvailablePackaging(itemKey, row)
        {
            
            $.ajax({
                type: "GET",
                url: 'ajax-packaging-code.php',
                async: false,
                data: "action=getAvailablePackagingByItem&pkey=" + itemKey,
            }).done(function (data) {

                var data = parseJSON(data);
                if (!data || data.length === 0) 
                    return; 

                var selPackagingCodeObj = row.find("[name='selPackagingCode[]']");     
 
                var arrData = [];
                for(var i=0; i < data.length; i++) {
                    var obj = {};
                    obj['pkey'] = data[i]['pkey'];
                    obj['value'] = decodeHTMLEntities(data[i]['value']);
                    arrData.push(obj);
                }
                var selectOpt = arrData;
                reInsertSelectBox(selPackagingCodeObj, selectOpt, { "key": "pkey", "label": "value" });

            });
        }        
    
        this.rebindEl = function rebindEl(){   
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&limit=25',thisObj.updateDetail);
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]'], [name='chkPriceInPcs[]'], [name='priceInPcs[]'], [name='qtyInPcs[]']" ), 'change',  function(){ calculateSNNeeded(tabObj,this); thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });  
            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); thisObj.calculateDetail(this); }); 
			
            // bindEl($("[name='voucherCode[]']"),'change',function(){ thisObj.calculateTotal(); });
            bindEl($('.voucher-row .remove-button'),'click',function(){ removeDetailRows(this);  thisObj.calculateTotal(); });
            bindEl(tabObj.find("[name='chkPriceInPcs[]']"),'change',function(){ thisObj.onChangePriceInPcs($(this)) }); 
            bindEl(tabObj.find(".btn-sn-options"), 'click', function () {
                SNOptHander(tabObj, this, thisObj.snRegex);
                mnvOptionsRowOnClick($(this));
            });
            thisObj.rebindVoucher();  
        }
        
        this.loadOnReady = function loadOnReady(){
 
            // buat form yg tdk ad upload filenya 
            if(typeof fileFolder !== 'undefined'){

                if(thisObj.useStorage){

                }else{ 
                    if(id){    
                        for($i=0;$i<rsFile.length;$i++) 
                            arrFile.push(rsFile[$i].file); 

                        createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,true);  

                    }else{  
                         createFileUploader(fileUploaderTarget, fileFolder, "", "", true);
                    }
                }
            }

            tabObj.find("[name=selTermOfPaymentKey]" ).change(function() {
           
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

               tabObj.find(".cashTOP").show();
            });   
                 
            tabObj.find("[name=selTermOfPaymentKey]" ).change();   

            tabObj.find("[name=chkIsFullDeliver]").on('change', function () {
                updateSNOptions(tabObj);
            });
            tabObj.find(".transaction-detail-row").each(function () { calculateSNNeeded(tabObj, $(this)); });
           
            tabObj.find(" [name=chkIsFullDeliver]" ).change(); 
            tabObj.find(".form-detail-field").toggle(); 

            tabObj.find(".form-detail-button").click(function() {   
                 tabObj.find(".form-detail-field").toggle( "highlight" );
                 var temp =  tabObj.find(".form-detail-button").attr("relalt");   
                 tabObj.find(".form-detail-button").attr("relalt", tabObj.find(".form-detail-button").text());
                 tabObj.find(".form-detail-button").text(temp); 
            }); 
       
            tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=selFinalDiscountType2], [name=finalDiscount2], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal()}) 
            tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)}) 
            tabObj.find("[name=selFinalDiscountType2]").change(function(){updateFinalDiscountDecimal(this,tabObj.find("[name=finalDiscount2]"));}) 
                        

            tabObj.find("[name=btnSaveEmail]").click(function() {  
                tabObj.find("[name=hidSendEmail]").val(1);
                tabObj.find("#defaultForm").submit();
            }); 
            

            tabObj.find("[name=btnUpdate]").click(function() {  
                $.ajax({
                    type: "POST",
                    url:  'updateShipmentTracking.php',
                    data: "value=" + $("#" + tabID + " [name=shipmentTracking]").val() +"&id=" + $("#" + tabID + " [name=hidId]").val() ,  
                }).done(function( data ) {   
                        data = JSON.parse(data) 

                        var error = ""; 
                        for (i=0;i<data.length;i++) { 
                             error = error +   data[i].message + '\n';   
                        }
                    
                        alert(error);
                });
	        });

            tabObj.find("[name=chkIsDropship]" ).change(function() { 
                tabObj.find(".dropship-information").toggle(); 
                thisObj.updateCustomerInformation();
            });
 
            if (thisObj.rs && thisObj.rs.isdropship == 1) 
                tabObj.find(".dropship-information").toggle();  
            
			// sementara cuma 1 baris saja dulu
            thisObj.updateVoucherInformation();
			var voucherList = tabObj.find("[name='voucherAmount[]']"); 
			if(voucherList.length <= 1)
            	addNewTemplateRow("voucher-row-template");  
			
            customCodeHandler(thisObj);
            
            thisObj.rebindEl(); 
  
        } 
     }
