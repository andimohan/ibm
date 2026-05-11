function SalesOrderCarService(tabID, varConstant,cashTOP) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
      
        var objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'});  
	  	objAndValue.push({object:'isPackage[]', value :'ispackage'});  
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue; 


	    var objAndValue = new Array;
		objAndValue.push({object:'hidDetailSalesKey[]', value :'pkey'}); 
        var objAndValueForDetailSalesAutoComplete = objAndValue; 
	  	 	
	    var objAndValue = new Array;
		objAndValue.push({object:'hidDetailWarehouseKey[]', value :'pkey'}); 
        var objAndValueForDetailWarehouseAutoComplete = objAndValue; 

	  	 	   
        this.tabID = tabID;    

    
    
        this.rewritePackageDetail = function rewritePackageDetail(rowsDetail,data){
             var HTML = ""; 
             
             if (data.length > 0 ){ 
                    HTML += '<div class="div-table" style="width: 400px">';
                    HTML += '<div class="div-table-caption">'+phpLang.itemPackage+'</div>';

                   for(i=0;i<data.length;i++) 
                        HTML += '<div class="div-table-row"><div class="div-table-col">'+data[i].itemname+'</div><div class="div-table-col input-number" style="width:40px;  text-align:right">'+data[i].qty+'</div><div class="div-table-col" style="width:100px">'+data[i].unitname+'</div></div>'; 

                    HTML += '</div>'; 
             } 

            rowsDetail.html(HTML);
            rowsDetail.find(".input-number").formatCurrency(); 
         }
        
        this.updateRowsDetails = function updateRowsDetails(row,detailkey,onload){
             
             // gk bisa pake fungsi yg sama, meskipun edit, kalo add new row, tetep ahrus ambil dr mater item paket
             
             var rowsDetail = row.find(".options-row .summary-panel");
             var itemkey = row.find("[name=\"hidItemKey[]\"]").first().val() 
    
             var HTML = ""; 
             rowsDetail.html(_LOADING_ICON_SMALL_).show(); 
             var url = 'ajax-item-package.php', 
                 action =  'action=getDetailById&pkey='+ detailkey;
               
             if (onload){
                 url = 'ajax-sales-order-car-service.php';
                 action = 'action=getPackageDetail&detailkey='+ detailkey;
             }
             
              $.ajax({
                    type: "GET", 
                    url:  url,
                    data: action,  
                }).done(function( data ) {   
                   data = JSON.parse(data) ;    
                   thisObj.rewritePackageDetail(rowsDetail,data); 
                }); 
              
             
         }

        this.updateConversionVariable = function updateConversionVariable(itemkey,ispackage){ 
             
                var url = "ajax-item.php";
                if (ispackage == 1)
                    url = "ajax-item-package.php";
            
            
                $.ajax({
                    type: "GET",
                    async:false,
                    url:  url,
                    data: "action=getAvailableConversion&itemkey=" +  itemkey ,  
                }).done(function( data ) { 
                        data = JSON.parse(data) ;   

                        thisObj.conversion[itemkey] = {};
                        for (i=0;i<data.length;i++){  
                            thisObj.conversion[itemkey][data[i].conversionunitkey] =  data[i].conversionmultiplier; 
                        }
                    
                       // console.log(salesOrderCarService.conversion[itemkey]);
                }); 
             
        }
	  	  
        
        this.updateDetail = function updateDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row");
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
            var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();

            for(i=0;i<objAndValue.length;i++){   
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
            } 


            updateAvailableUnit(itemKeyObj, selUnitObj);

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
 
            if(ui.item['ispackage'] == 1)
                thisObj.updateRowsDetails(detailRow, ui.item['pkey']);  

              thisObj.calculateDetail(itemKeyObj);
 
         }
         
        
        this.calculateDetail = function calculateDetail(obj){     
            //console.log("calculate detail")
            
            var row =  $(obj).closest(".transaction-detail-row");   
            var itemkey =  row.find("[name='hidItemKey[]']").val();
            var qty =  unformatCurrency(row.find("[name='qty[]']").val());
            var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
            var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
            var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
            
   
                
            var unitkey =  row.find("[name='selUnit[]']").val();

            if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit;

            var subtotal = qty  *  (priceInUnit - discount); 
            
             row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 

            thisObj.calculateTotal();
        }
	
        this.calculateTotal = function calculateTotal(){  

                var subtotal = 0;   
                var subtotaltax23 = 0;   
		        tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0; });
                

                tabObj.find(" [name='subtotal']").val(subtotal).blur();

                var finalDiscount = parseFloat(unformatCurrency(tabObj.find(" [name='finalDiscount']").val())) || 0 ;
                var finalDiscountType = parseInt(unformatCurrency(tabObj.find(" [name='selFinalDiscountType']").val())) || 0 ;
                var pointValue = parseInt(unformatCurrency(tabObj.find(" [name='pointValue']").val())) || 0 ; 
                var shipmentFee = parseInt(unformatCurrency(tabObj.find(" [name='shipmentFee']").val())) || 0 ;
                var etcCost = parseInt(unformatCurrency(tabObj.find(" [name='etcCost']").val())) || 0 ; 
                var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ;  
                var includeTax =   tabObj.find(" [name='chkIncludeTax']").val();
                var taxValue = 0;  

                if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 
                subtotal -= finalDiscount;
                subtotal -= pointValue;

                tabObj.find("[name='beforeTaxTotal']").val(subtotal).blur();
                    if (includeTax == 0) {
                            taxValue = subtotal * taxPercentage / 100;
                            subtotal += taxValue;
                    }else{
                            taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                            tabObj.find("[name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
                    }

                    tabObj.find("[name='taxValue']").val(taxValue).blur(); 
                var total = subtotal +  shipmentFee + etcCost;
                tabObj.find(" [name='total']").val(total).blur();
            
            
                 var totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));

               /* var totalPayment = 0; 
                    tabObj.find(" [name='paymentMethodValue[]']").each(function() {   
                            totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
                    }) */


                var balance = totalPayment - total; 

                tabObj.find(" [name='balance']").val(balance).blur();
                thisObj.calculateTax23();


       }
        
                
        this.calculateTax23 = function calculateTax23(){
            var tax23Percentage =  parseFloat(unformatCurrency(tabObj.find("[name='tax23Percentage']").val())) || 0 ; 
            var beforeTaxTotal =   0 ; 
            tabObj.find("[name='chkIsTax23[]']").each(function(){
                
                    if ($(this).val() != 1 )  return;

                    row = $(this).closest(".transaction-detail-row");
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
                    var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
                    var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
            
                    var unitkey =  row.find("[name='selUnit[]']").val();

                    if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit;

                    var subtotal = qty  *  (priceInUnit - discount); 

                    beforeTaxTotal += subtotal;

            })
            // utk transaksi normal
            var useTax23 = 1;
            var tax23Value = 0; 

            if (useTax23 != 0 && tax23Percentage > 0) { 
                var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
                var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ;

                if (includeTax == 1) 
                    beforeTaxTotal = beforeTaxTotal - (taxPercentage/(100 + taxPercentage)) * beforeTaxTotal;    

                tax23Value = (tax23Percentage/100) * beforeTaxTotal; 
            }
            

            tabObj.find("[name='tax23Value']").val(tax23Value).blur(); 
        }
        
        
        
        this.updateCarInformation =  function updateCarInformation(){  
          
            tabObj.find(" [name=policeNumber]").val("");
            tabObj.find(" [name=year]").val("");
            tabObj.find(" [name=carSeriesName]").val("");
            tabObj.find(" [name=capacity]").val("");
            tabObj.find(" [name=fuelType]").val("");
            tabObj.find(" [name=hidCustomerKey]").val("");
            tabObj.find(" [name=customerName]").val("");
            tabObj.find(" [name=phone]").val("");
            tabObj.find(" [name=mobile]").val("");
            tabObj.find(" [name=email]").val("");
    
            var carkey = tabObj.find(" [name=hidCarKey]" ).val();  
        
            if(!carkey)
                return;
        
              $.ajax({
                    type: "GET",
                    url:  'ajax-car.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + carkey ,  
                }).done(function( data ) { 
                        data = JSON.parse(data) ; 
                   
                        if (data.length != 0){   
                            data = data[0];  
                              
                            tabObj.find(" [name=policeNumber]").val(data.policenumber);
                            tabObj.find(" [name=year]").val(data.year);
                            tabObj.find(" [name=carSeriesName]").val(data.seriesname);
                            tabObj.find(" [name=capacity]").val(data.capacity).blur();
                            tabObj.find(" [name=fuelType]").val(data.fueltype); 
                            tabObj.find(" [name=hidCustomerKey]").val(data.customerkey); 
                            
                            thisObj.updateCustomerInformation();
                        } 
 
                });
                
        }
        this.updateCustomerInformation = function updateCustomerInformation(){
                tabObj.find(" [name=customerName]").val("");
                tabObj.find(" [name=phone]").val("");
                tabObj.find(" [name=mobile]").val("");
                tabObj.find(" [name=email]").val("");

                var customerkey = tabObj.find(" [name=hidCustomerKey]" ).val();

                if(!customerkey)
                    return;

                $.ajax({
                        type: "GET",
                        url:  'ajax-customer.php',
                        async: true,
                        data: "action=getDataRowById&pkey=" + customerkey ,  
                    }).done(function( data ) { 
                            data = JSON.parse(data) ;  
                            if (data.length != 0){   
                                data = data[0];  

                                tabObj.find(" [name=hidCustomerKey]").val(data.pkey); 
                                tabObj.find(" [name=customerName]").val(data.name);
                                tabObj.find(" [name=phone]").val(data.phone);
                                tabObj.find(" [name=mobile]").val(data.mobile);
                                tabObj.find(" [name=email]").val(data.email);
                                
                                var tax23Percentage =  (data.taxid) ? 2 : 4; 
                                tabObj.find("[name='tax23Percentage']").val(tax23Percentage).change().blur();
                                     

                            }  
                    });    
         }

        this.updateRecipientsInformation =  function updateRecipientsInformation(data){

                var address = "";
                address = data.address ;
                if (data.address2 != "")
                    address += "\n" + data.address2;  

                tabObj.find(" [name=hidCreditLimit]").val(data.creditlimit);  

                tabObj.find(" [name=recipientName]").val(data.name);
                tabObj.find(" [name=recipientPhone]").val(data.phone);
                tabObj.find(" [name=recipientEmail]").val(data.email);
                tabObj.find(" [name=recipientAddress]").val(address);   

                if (tabObj.find(" [name=selTermOfPaymentKey] option[value='" + data.termofpaymentkey + "']").length > 0)
                    tabObj.find(" [name=selTermOfPaymentKey]").val(data.termofpaymentkey).change();

        }
        
        
        this.importPayment =  function importPayment(){ 
        
            var total = tabObj.find(" [name=total]" ).val();

            // search semua list payment method... 
            tabObj.find(" [name='paymentMethodValue[]']").each(function(i) {
                    if (i > 0){ // ini utk cek, baris pertama jgn dihapus 
                        $(this).closest(".transaction-detail-row").remove(); 
                    }
            });
            
            tabObj.find(" [name='paymentMethodValue[]']").first().val(total).change().blur();  

            thisObj.calculateTotal();

        }
        
        this.updateCustomerAndRecipients = function updateCustomerAndRecipients(){ 
                    
            var customerkey = tabObj.find(" [name=hidCustomerKey]" ).val();  
            tabObj.find(" [name=hidCustomerKey]").val("");
            tabObj.find(" [name=customerName]").val(""); 

            if(!customerkey)
                return;

            $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async: false,
                data: "action=getDataRowById&pkey=" + customerkey ,  
            }).done(function( data ) { 

                    data = JSON.parse(data) ; 

                    if (data.length == 0){  

                    }else{  
                        data = data[0];  

                        tabObj.find(" [name=hidCustomerKey]").val(data.pkey);
                        tabObj.find(" [name=customerName]").val(data.name); 
                        tabObj.find('form').bootstrapValidator('revalidateField', 'customerName'); 
                    } 

                    // update recipients
                    var informationExist  = false;

                    var recipientName = tabObj.find(" [name=recipientName]" ).val(); 
                    var recipientPhone = tabObj.find(" [name=recipientPhone]" ).val(); 
                    var recipientEmail = tabObj.find(" [name=recipientEmail]" ).val(); 
                    var recipientAddress = tabObj.find(" [name=recipientAddress]" ).val();  


                     if (recipientName != "" || recipientPhone  != "" || recipientEmail != "" || recipientAddress != "" )
                         informationExist = true;


                    if (informationExist == false){
                       thisObj.updateRecipientsInformation(data);
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
                                    thisObj.updateRecipientsInformation(data);
                                    $( this ).dialog( "close" );
                              },
                              Cancel : function (){  
                                    $( this ).dialog( "close" );
                              }
                          },
                        });	  
                    } 
            }); 
        }
        
        this.onCLickAddPayment = function onCLickAddPayment(){
            $newRow = addNewTemplateRow("payment-method-row-template"); 
        }

        
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
            thisObj.calculateTotal(); 
        }
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }


        this.rebindEl = function rebindEl(){
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail); 
            bindAutoCompleteForTransactionDetail('detailSalesName[]',objAndValueForDetailSalesAutoComplete,'ajax-employee.php?action=searchData&issales=1');
            bindAutoCompleteForTransactionDetail('detailWarehouseName[]',objAndValueForDetailWarehouseAutoComplete,'ajax-warehouse.php?action=searchData');
            
            
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]'],  [name='selUnit[]']"), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='chkIsTax23[]']"), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });  
            bindEl(tabObj.find(".btn-more-options"),'click', function() {mnvOptionsRowOnClick($(this)); });   

        }

        this.loadOnReady = function loadOnReady(){ 
        		
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
            
            tabObj.find(".form-detail-field").toggle();  

            tabObj.find(".form-detail-button").click(function() {  

                tabObj.find(".form-detail-field").toggle( "highlight" );
                var temp = tabObj.find(".form-detail-button").attr("relalt");   
                tabObj.find(".form-detail-button").attr("relalt",tabObj.find(".form-detail-button").text());
                tabObj.find(".form-detail-button").text(temp);

            }); 
               
            tabObj.find("[name=chkIsTax23], [name=tax23Percentage]").change(function(){thisObj.calculateTax23()}); 

            tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost],[name=pointValue],[name=taxValue], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal(this)}) 
            tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)}) 
            tabObj.find(" [name=btnSaveEmail]").click(function() {  
                tabObj.find(" [name=hidSendEmail]").val(1);
                tabObj.find(" #defaultForm").submit();
            }); 

            tabObj.find(" .btn-pay").on('click', function() { thisObj.importPayment(); });

                               
            thisObj.rebindEl(); 
             
        }
        
}
