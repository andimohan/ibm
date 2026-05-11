function SalesOrderRental(tabID, rs,isunlimited) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
    
		var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'selTimeUnit[]', value :'timeunitkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
        var objAndValueForDetailAutoComplete = objAndValue;  
        
        this.tabID = tabID;    
    
        this.rs = (rs.length > 0) ? rs[0] : null;
	
		this.isunlimited = isunlimited;   
    
        this.updateDetail = function updateDetail(target,objAndValue,ui){

                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first(); 
                var selTimeUnitObj = detailRow.find("[name=\"selTimeUnit[]\"]").first(); 
              
                for(i=0;i<objAndValue.length;i++){    
                    //overwrite kalo kg
                    if(objAndValue[i].object == 'hidGramasi[]' && ui.item['weightunitkey'] == 2)
                        ui.item[objAndValue[i].value] *= 1000; 
                    
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
            
                updateAvailableUnit(itemKeyObj, selUnitObj); 
                thisObj.updateAvailableTimeUnit(itemKeyObj, selTimeUnitObj);
                //thisObj.updateUnitPrice(selUnitObj);
                thisObj.updateTimeUnitPrice(selTimeUnitObj);
              
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
                 
                thisObj.calculateDetail(itemKeyObj);
 
         }
		
		
        this.updateAvailableTimeUnit =  function updateAvailableTimeUnit(itemKeyObj, selUnitObj){     
             var row =  $(itemKeyObj).closest(".transaction-detail-row"); 
             var itemKey = $(itemKeyObj).val();

            if(!itemKey)
                return;

            $.ajax({
                    type: "GET",
                    url:  'ajax-item.php',
                    async: false,
                    data: "action=getAvailableTimeUnit&itemkey=" + itemKey ,  
                    success: function(data){ 

                        data = JSON.parse(data) ; 
                        var newOptions = {};
                        for(i=0;i<data.length;i++)   
                            newOptions[data[i].timeunitkey] =  data[i].timename;       

                        var options = (selUnitObj.prop) ? selUnitObj.prop('options') : selUnitObj.attr('options');  

                        $('option', selUnitObj).remove();

                        $.each(newOptions, function(val, text) {
                            options[options.length] = new Option(text, val);
                        });

                       selUnitObj.find('option:eq(0)').prop('selected', true).change();     

                    }
            });
        }
		 
		
        
	    this.calculateDetail = function calculateDetail(obj){      
               
                    var row =  $(obj).closest(".transaction-detail-row");   
                    var itemkey =  row.find("[name='hidItemKey[]']").val();
                
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
                    var totalDays =  unformatCurrency(row.find("[name='totalDays[]']").val());
                    var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
                    var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
            
                    var selUnitObj = row.find("[name='selUnit[]']");
                    var unitkey =  selUnitObj.val();
                    var conversionmultiplier =  parseFloat(selUnitObj.find("option:selected").attr('relconversionmultiplier'));
            
                    var gramasi =  parseFloat(row.find("[name='hidGramasi[]']").val());
                
                    var subtotal = qty  *  priceInUnit * totalDays;
                    row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 
                    row.find("[name='hidGramasiSubtotal[]']").val(gramasi * qty * conversionmultiplier).blur(); 

                    thisObj.calculateTotal();
	       }
	
	    this.calculateTotal = function calculateTotal(){  
         
                    var total = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ total += parseInt(unformatCurrency($(this).val())) || 0;  })
               
                    //var totalGramasi = 0; 
                    //tabObj.find("[name='hidGramasiSubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
                    //tabObj.find(".total-weight").html(Math.ceil(totalGramasi/1000));
              
                    tabObj.find("[name='total']").val(total).blur();
		 
	       }
         
        this.updateCustomerInformation =  function updateCustomerInformation(){ 
                      var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  
                       
                        if(!customerkey)
                            return;

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
                                    tabObj.find("[name=hidRecipientCityKey]").val(data.citykey);   
                                    tabObj.find("[name=recipientCityName]").val(data.cityandcategoryname);  
                                }
 
                                if (tabObj.find("[name=selTermOfPaymentKey] option[value='" + data.termofpaymentkey + "']").length > 0)
                                    tabObj.find("[name=selTermOfPaymentKey]").val(data.termofpaymentkey).change(); 
                        });
        }
        
        this.updateUnitPrice = function updateUnitPrice(obj){ 
            var row = $(obj).closest(".transaction-detail-row");
            var unitKey = $(obj).val();
            var itemKey = row.find("[name='\hidItemKey[]\']").val();
          
            $.ajax({
                type: "GET",
                url:  'ajax-item.php',
                async: false,
                data: "action=getTimeUnitSellingPrice&itemkey="+itemKey+"&unitkey=" + unitKey ,  
            }).done(function( data ) {  
                   data = JSON.parse(data) ; 
                   row.find("[name=\'priceInUnit[]\']").val(data).blur();
            });
        }
		
		this.updateTimeUnitPrice = function updateTimeUnitPrice(obj){ 
            var row = $(obj).closest(".transaction-detail-row");
            var unitKey = $(obj).val();
            var itemKey = row.find("[name='\hidItemKey[]\']").val();
          
            $.ajax({
                type: "GET",
                url:  'ajax-item.php',
                async: false,
                data: "action=getTimeUnitSellingPrice&itemkey="+itemKey+"&timeunitkey=" + unitKey ,   
            }).done(function( data ) {  
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
        
        this.updateQuotationInformation = function updateQuotationInformation(){ 
            var sokey = tabObj.find("[name=hidSalesQuotationKey]").val(); 
            $.ajax({
                type: "GET",
                url:  'ajax-sales-rental-quotation.php',
                data: "action=getDataRowById&pkey=" + sokey ,  
            }).done(function( data ) {  

                data = JSON.parse(data) ;  
                data = data[0];
                
                console.log(data);
                
                tabObj.find("[name=hidCustomerKey]").val(data.customerkey);  
                tabObj.find("[name=customerName]").val(data.customername);    
                tabObj.find("[name=hidLocationKey]").val(data.locationkey);    
                tabObj.find("[name=locationName]").val(data.locationname);    
                tabObj.find("[name=selWarehouseKey]").val(data.warehousekey);    
                tabObj.find("[name=quotationName]").val(data.name);    
                tabObj.find("[name=trDesc]").val(data.trdesc);    
                thisObj.updateSalesman();
                thisObj.updateRecipients();
 
            }); 
 
        }
   
       this.updateQuotationDetail = function updateQuotationDetail(){ 

                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;

                var sokey = tabObj.find("[name=hidSalesQuotationKey]").val(); 

                var ajaxData = "action=getDetailById&pkey=" + sokey;

                $.ajax({
                    type: "GET",
                    url:  'ajax-sales-rental-quotation.php',
                    beforeSend:function (xhr){ 
                        // hanya reset yg di table transaksi, downpayment, cost dan payment method gk perlu direset
                        clearAllRows(tabObj.find(".mnv-transaction"));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: ajaxData,
                    success: function(data){ 
                            var data = JSON.parse(data);  
                            var i;
                            for(i=0;i<data.length;i++){  
                                    
                                    var arrPostValue = []; 
                                    arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                                    arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                                    arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                                    arrPostValue.push({"selector":"selUnit", "value":data[i].unitkey});  
                                    arrPostValue.push({"selector":"priceInUnit", "value":data[i].priceinunit}); 
                                    arrPostValue.push({"selector":"selTimeUnit", "value":data[i].timeunitkey}); 
                                    arrPostValue.push({"selector":"totalDays", "value":data[i].totaldays}); 
                                    arrPostValue.push({"selector":"detailSubtotal", "value":data[i].total}); 

                                    addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));   
                            }
                        
//                        tabObj.find("[name=\"selTimeUnit[]\"]").find("option:selected").attr('disabled', false);
//                        tabObj.find("[name=\"selTimeUnit[]\"]").find("option:not(:selected)").attr('disabled', true);

                        thisObj.rebindEl(); 
                        tabObj.find(".inputnumber").change().blur();
                        tabObj.find(".inputdecimal").change().blur();
                        decreaseActiveAjaxConnections(thisObj); 

                    } ,
                     error: function(xhr, errDesc, exception) {
                         decreaseActiveAjaxConnections(thisObj); 
                    }
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

                    // sementara selalu update
                    thisObj.updateCustomerInformation();
        }
 
        this.updateTimeUnit = function updateTimeUnit(obj){ 
            $(obj).closest(".transaction-detail-row").find(".time-unit").html($(obj).find('option:selected').text());
        }
		
		this.updateIsUnlimited = function updateIsUnlimited(){ 
           var isUnlimited = tabObj.find("[name=chkIsUnlimited]").val();  
			if(isUnlimited==0){ 
				tabObj.find("[name='totalDays[]']").prop("readonly", false); 
				tabObj.find("[name='totalDays[]']").val(1).blur();  
			}else{
				tabObj.find("[name='totalDays[]']").prop("readonly", true); 
				tabObj.find("[name='totalDays[]']").val(1).blur();
			}
			
			tabObj.find("[name='totalDays[]']").each(function(){  
				thisObj.calculateDetail($(this));
			}) 
			
				    
        }    
		 
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        } 
		
		this.rebindItem = function rebindItem(){  
       	 	var ajaxitem = '';
			
			var itemtype = [1,3];
			ajaxitem = 'ajax-item.php?action=searchData&itemtype='+itemtype+'&serviceCost=0';
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,ajaxitem,thisObj.updateDetail);
    
		} 
          
           
        this.rebindEl = function rebindEl(){  
			bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='totalDays[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
//            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); thisObj.calculateDetail(this); });  
            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.calculateDetail(this); });  
            bindEl(tabObj.find("[name='selTimeUnit[]']"),'change',function(){ thisObj.updateTimeUnitPrice(this); thisObj.calculateDetail(this); thisObj.updateTimeUnit(this); });  
        
			thisObj.rebindItem();
		} 
        
        this.loadOnReady = function loadOnReady(){
			if(this.isunlimited){
				tabObj.find("[name=chkIsUnlimited]").change(function(){thisObj.updateIsUnlimited()});   
				tabObj.find("[name=chkIsUnlimited]" ).change(); 
			}
			 
               
            
            thisObj.rebindEl(); 
  
        } 
     }
