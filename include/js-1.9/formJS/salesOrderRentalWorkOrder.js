function SalesOrderRentalWorkOrder(tabID, rs) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
        this.customCodeCache=[];
    
		var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	//objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		//objAndValue.push({object:'selTimeUnit[]', value :'timeunitkey'}); 
		//objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
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
            
                updateAvailableUnit(itemKeyObj, selUnitObj);
                //thisObj.updateUnitPrice(selUnitObj);
             
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
 
         }
        
	    /*this.calculateDetail = function calculateDetail(obj){      
               
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
	       }*/
	
	    this.calculateTotal = function calculateTotal(){  
         
                    var total = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ total += parseInt(unformatCurrency($(this).val())) || 0;  })
               
                    //var totalGramasi = 0; 
                    //tabObj.find("[name='hidGramasiSubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
                    //tabObj.find(".total-weight").html(Math.ceil(totalGramasi/1000));
              
                    tabObj.find("[name='total']").val(total).blur();
		 
	       }
        
        
   
       this.updateSalesOrderDetail = function updateSalesOrderDetail(){ 

                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;
 
                var sokey = tabObj.find("[name=hidSalesOrderKey]").val(); 

                var ajaxData = "action=getDetailById&pkey=" + sokey;

                $.ajax({
                    type: "GET",
                    url:  'ajax-sales-order-rental.php',
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
                                    if(data[i].outstanding<1)
                                        continue;
                                
                                    var arrPostValue = []; 
                                    arrPostValue.push({"selector":"hidRefSODetailKey", "value":data[i].pkey});
                                    arrPostValue.push({"selector":"hidItemKey", "value":data[i].itemkey});
                                    arrPostValue.push({"selector":"itemName", "value":data[i].itemname}); 
                                    arrPostValue.push({"selector":"qty", "value":data[i].outstanding}); 
                                    arrPostValue.push({"selector":"selUnit", "value":data[i].baseunitkey});

                                    addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));   
                            }
                         
                        tabObj.find("[name=\"selTimeUnit[]\"]").find("option:selected").attr('disabled', false);
                        tabObj.find("[name=\"selTimeUnit[]\"]").find("option:not(:selected)").attr('disabled', true);

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
        
        this.updateSalesOrderInformation = function updateSalesOrderInformation(){

            var sokey = tabObj.find("[name=hidSalesOrderKey]").val(); 
 
            $.ajax({
                type: "GET",
                url:  'ajax-sales-order-rental.php',
                async: false,
                data: "action=getDataRowById&pkey=" + sokey ,  
            }).done(function( data ) {  

                data = JSON.parse(data) ; 
                data = data[0]; 
                thisObj.updateSalesOrderDetail();
                thisObj.updateCustomerCity(data.customerkey);
                tabObj.find("[name=locationName]").val(data.locationname);  
                tabObj.find("[name=hidLocationKey]").val(data.locationkey);  
                tabObj.find("[name=recipientName]").val(data.recipientname);  
                tabObj.find("[name=hidRecipientKey]").val(data.customerkey);  
                tabObj.find("[name=recipientEmail]").val(data.recipientemail);  
                tabObj.find("[name=recipientPhone]").val(data.recipientphone);  
                tabObj.find("[name=recipientAddress]").val(data.recipientaddress);  
 
            }); 
 
        }  
        
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         //thisObj.calculateTotal(); 
        }
          
           
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
            //bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='totalDays[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); });  
            //bindEl(tabObj.find("[name='selTimeUnit[]']"),'change',function(){ thisObj.updateTimeUnit(this); });  
        }
        
        this.loadOnReady = function loadOnReady(){
               
            //tabObj.find("[name=\"selTimeUnit[]\"]").find("option:not(:selected)").attr('disabled', true);
            
            thisObj.rebindEl(); 
  
        } 
     }
