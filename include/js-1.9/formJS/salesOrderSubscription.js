function SalesOrderSubscription(tabID, rs) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
    
        		
        var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
        objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
        var objAndValueForDetailAutoComplete = objAndValue; 
    
       
        var objAndValue = new Array;  
		objAndValue.push({object:'hidItemMonthlyKey[]', value :'pkey'}); 
        objAndValue.push({object:'priceInUnitMonthly[]', value :'sellingprice'}); 
        var objAndValueForDetailMonthlyAutoComplete = objAndValue; 
     
        this.tabID = tabID;    

        this.rs = (rs.length > 0) ? rs[0] : null;
        var arrDetails = {}; 
     
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
                        tabObj.find("[name=selMedia]").val(data.mediakey);
                        tabObj.find("[name=phone]").val(data.phone);
                        tabObj.find("[name=address]").val(data.address);
                        tabObj.find("[name=attention]").val(data.attention);
                        tabObj.find("[name=locationName]").val(data.locationname);

                });
        }
      
        this.updateDetail = function updateDetail(target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
                for(i=0;i<objAndValue.length;i++){ 
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
//                thisObj.calculateDetail(itemKeyObj);
         }
        
        this.updateDetailMonthly = function updateDetailMonthly(target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
                var itemMonthlyKeyObj = detailRow.find("[name=\"hidItemMonthlyKey[]\"]").first();
                for(i=0;i<objAndValue.length;i++){   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 
            
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemMonthlyName[]\"]").first().val(ui.item['value']); 
                
//                thisObj.calculateDetail(itemMonthlyKeyObj);
 
         }
        
          this.calculateDetail = function calculateDetail(obj){      
               
                    var row =  $(obj).closest(".transaction-detail-row");   
                
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());

                    var subtotal = qty  * priceInUnit;
                    row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 
                    
                    //monthly
                    var qtymonthly =  unformatCurrency(row.find("[name='qtyMonthly[]']").val());
                    var priceInUnitMonthly =  unformatCurrency(row.find("[name='priceInUnitMonthly[]']").val());
              
                    var subtotalmonthly = qtymonthly  * priceInUnitMonthly;
                    row.find("[name='detailSubtotalMonthly[]']").val(subtotalmonthly).blur(); 

                    thisObj.calculateTotal();
                    thisObj.calculateTotalMonthly();
	       }
	 
	    this.calculateTotalMonthly = function calculateTotalMonthly(){  
         
                    var subtotalmonthly = 0; 
                    tabObj.find("[name='detailSubtotalMonthly[]']").each(function(){ subtotalmonthly += parseInt(unformatCurrency($(this).val())) || 0;  })
                    tabObj.find("[name='subtotalMonthly']").val(subtotalmonthly).blur();
                
                    var totalGramasi = 0; 
                    tabObj.find("[name='hidGramasiMonthlySubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
                    
                    var includeTax =   tabObj.find("[name='chkIncludeTaxMonthly']").val();
                    var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentageMonthly']").val())) || 0 ; 
        
                    
                    tabObj.find("[name='beforeTaxTotalMonthly']").val(subtotalmonthly).blur();

                    var taxValue = 0;
                    if (includeTax == 0) {
                            taxValue = subtotalmonthly * taxPercentage / 100;
                            subtotalmonthly += taxValue;
                    }else{
                            taxValue = (taxPercentage/(100 + taxPercentage)) * subtotalmonthly; 
                            tabObj.find("[name='beforeTaxTotalMonthly']").val(subtotalmonthly - taxValue).blur(); 
                    }

                    tabObj.find("[name='taxValueMonthly']").val(taxValue).blur(); 
            
                    var total = subtotalmonthly;
                    tabObj.find("[name='totalMonthly']").val(total).blur();
                    
                
		 
	       }
        
        this.calculateTotal = function calculateTotal(){  
         
                    var subtotal = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
                    tabObj.find("[name='subtotal']").val(subtotal).blur();
                
                    var totalGramasi = 0; 
                    tabObj.find("[name='hidGramasiSubtotal[]']").each(function(){ totalGramasi += parseFloat($(this).val()) || 0;  })
                    
                    var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
                    var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ; 
        
                    
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
		 
	       } 
		
		this.showRecurring = function showRecurring(){  			
             if (tabObj.find("[name='selInvoiceRecurring']").val() == 1){ 
                 tabObj.find(" .recurring").show();
             }else{ 
                 tabObj.find(" .recurring").hide();
             } 
         }
       
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
            thisObj.calculateTotalMonthly(); 
        }
             
          
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
            bindAutoCompleteForTransactionDetail('itemMonthlyName[]',objAndValueForDetailMonthlyAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetailMonthly);
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='qtyMonthly[]'], [name='priceInUnitMonthly[]']" ), 'change',  function(){ thisObj.calculateDetail(this) });  
 
        }
              
        this.loadOnReady = function loadOnReady(){
            if (!thisObj.rs)
                addNewTemplateRow("monthly-row-template",null,null,thisObj.rebindEl);
            
			tabObj.find("[name=selInvoiceRecurring]").change(function() { thisObj.showRecurring(); });
			tabObj.find("[name=selInvoiceRecurring]").change();
            tabObj.find("[name=beforeTaxTotal], [name=chkIncludeTax],[name=taxPercentage]" ).change(function(){thisObj.calculateTotal(this)}) 
            tabObj.find("[name=beforeTaxTotalMonthly], [name=chkIncludeTaxMonthly],[name=taxPercentageMonthly]" ).change(function(){thisObj.calculateTotalMonthly(this)}) 
            tabObj.find("[name=btnAddRows2]").on('click', function() { addNewTemplateRow("monthly-row-template",null,null,thisObj.rebindEl); });
            thisObj.rebindEl(); 
   
        } 
     } 
 
