function SalesOrderDumperInvoice(tabID, rs, cashTOP) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
    
        		
        var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
        objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
        var objAndValueForDetailAutoComplete = objAndValue; 
      
        this.tabID = tabID;    

        this.rs = (rs.length > 0) ? rs[0] : null;
        var arrDetails = {};
     
   this.updateProjectInformation =  function updateProjectInformation(val){ 
              /* var customerkey = val;  
 
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
                        tabObj.find("[name=selMedia]").val(data.mediakey);
                        tabObj.find("[name=sidCode]").val(data.sid);
                        tabObj.find("[name=phone]").val(data.phone);
                        tabObj.find("[name=address]").val(data.address);
                        tabObj.find("[name=attention]").val(data.attention);
                        tabObj.find("[name=locationName]").val(data.locationname);

                });*/
        }
        
        this.importData =  function importData(){  
                clearAllRows(tabObj.find(".mnv-transaction"));
 
                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;
                var dateParam = "";
                var startdate = convertDateToStandartFormat(tabObj.find("[name=trStartDate]").val());
                var enddate = convertDateToStandartFormat(tabObj.find("[name=trEndDate]").val());
                dateParam = "&startdate="+startdate+"&enddate="+enddate;
 
                $.ajax({
                    type: "GET",
                    url:  'ajax-project-dumper.php',
                    beforeSend:function (xhr){
                        clearAllRows(tabObj.find(".mnv-transaction"));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=searchDataForInvoice&pkey=" +  tabObj.find("[name=hidProjectKey]" ).val()+dateParam,  
                    success: function(data){  
                        
                        if (!data) return;
                       
                        var data = JSON.parse(data);   
                          
                        for(i=0;i<data.length;i++){  
                             
                                        var arrPostValue = []; 
                                        arrPostValue.push({"selector":"hidRefSOKey", "value":data[i].sokey});
                                        arrPostValue.push({"selector":"description", "value":data[i].socode});
                                        arrPostValue.push({"selector":"hidItemKey", "value":data[i].locationkey});
                                        arrPostValue.push({"selector":"itemName", "value":data[i].locationname}); 
                                        arrPostValue.push({"selector":"ritase", "value":data[i].totalritase}); 
                                        arrPostValue.push({"selector":"weight", "value":data[i].totalweight}); 
                                        arrPostValue.push({"selector":"distance", "value":data[i].totaldistance});
                                        arrPostValue.push({"selector":"detailSubtotal", "value":data[i].grandtotal});
                                        //arrPostValue.push({"selector":"selUnit", "value":data[i].unitkey});
                                        $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                                        //$newRow.find(".baseitemunit").first().html(data[i].baseunitname); 
                                        thisObj.calculateDetail($newRow);
                                        
                            } 
                            

                        thisObj.rebindEl(); 
                        tabObj.find(".inputnumber, .inputdecimal").blur();
                        decreaseActiveAjaxConnections(thisObj);

                    } ,
                    complete:function() {  
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                });

            thisObj.calculateTotal(); 
        }
         
        this.updateProjectInformation =  function updateProjectInformation(){ 
              var sokey = tabObj.find("[name=hidProjectKey]" ).val();  

                if(!sokey)
                    return;

               $.ajax({
                    type: "GET",
                    url:  'ajax-project-dumper.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + sokey ,  
                }).done(function( data ) {  

                        data = JSON.parse(data) ; 
                        data = data[0];
                        tabObj.find("[name=customerName]").val(data.customername);
                        tabObj.find("[name=hidCustomerKey]").val(data.customerkey);
                        //thisObj.getCustomerInformation(data.customerkey);
                        thisObj.importData();
                        //thisObj.importData2();

                });
        }
    
         
     
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
             
                //updateAvailableUnit(itemKeyObj, selUnitObj);
                //thisObj.updateUnitPrice(selUnitObj);
            
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
                
                thisObj.calculateDetail(itemKeyObj);
  
         }
        
        
          this.calculateDetail = function calculateDetail(obj){      
               
                    /*var row =  $(obj).closest(".transaction-detail-row");   
                
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
              
                    var selUnitObj = row.find("[name='selUnit[]']");
                    var unitkey =  selUnitObj.val();
                    var conversionmultiplier =  parseFloat(selUnitObj.find("option:selected").attr('relconversionmultiplier'));
            
                    //var gramasi =  parseFloat(row.find("[name='hidGramasi[]']").val());

                    var subtotal = qty  * priceInUnit;
                    row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 
                    //row.find("[name='hidGramasiSubtotal[]']").val(gramasi * qty * conversionmultiplier).blur(); 
                    */
                    thisObj.calculateTotal();
	       }
        
        this.calculateTotal = function calculateTotal(){  
         
                    var subtotal = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
                    tabObj.find("[name='subtotal']").val(subtotal).blur();
            
                    var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0 ;
                    var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ; 
                
                    //var totalGramasi = 0; 
                    //tabObj.find("[name='hidGramasiSubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
                    
                    var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
                    var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ; 
                    
                    if (finalDiscount != 0){
                        if (finalDiscountType == 2)
                            finalDiscount = finalDiscount/100 * subtotal;
                    }

                    subtotal -= finalDiscount;
                    
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
            
                    var total = subtotal;
                    tabObj.find("[name='total']").val(total).blur();
                    
                    var totalPayment = 0; 
                    tabObj.find("[name='paymentMethodValue[]']").each(function() {   
                            totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
                    }) 
                    tabObj.find("[name='totalPayment']").val(totalPayment).blur();


                    var balance = totalPayment - total ;  
                    tabObj.find("[name='balance']").val(balance).blur();
		 
	       }
        
        this.updateUnitPrice = function updateUnitPrice(obj){ 
           /* var row = $(obj).closest(".transaction-detail-row");
            var unitKey = $(obj).val();
            var itemKey = row.find("[name='\hidItemKey[]\']").val();
          
            $.ajax({
                type: "GET",
                url:  'ajax-item.php',
                async: false,
                data: "action=getUnitSellingPrice&itemkey="+itemKey+"&unitkey=" + unitKey ,  
            }).done(function( data ) {  
                   data = JSON.parse(data) ; 
                   row.find("[name=\'priceInUnit[]\']").val(data).blur();
            });*/
        }
        
      
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
         
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal(); ;   
        }
          
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
            //bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
            //bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.calculateDetail(this); });  
 
        }
              
        this.loadOnReady = function loadOnReady(){
            tabObj.find("[name=selTermOfPayment]" ).change(function() {
            
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){   
                        tabObj.find(".payment-detail-row.transaction-detail-row").find(".remove-button").each(function() {$(this).click()}); 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

               tabObj.find(".cashTOP").show();
            }); 
            tabObj.find("[name=selTermOfPayment]" ).change(); 
            
            tabObj.find("[name=beforeTaxTotal], [name=chkIncludeTax],[name=taxPercentage], [name=selFinalDiscountType], [name=finalDiscount]" ).change(function(){thisObj.calculateTotal(this)}) 
            thisObj.rebindEl(); 
   
        } 
     } 
  
