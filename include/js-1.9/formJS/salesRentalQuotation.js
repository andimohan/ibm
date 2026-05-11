function SalesRentalQuotation(tabID, rs) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
        this.customCodeCache=[];
    
		var objAndValue = new Array;  
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
		objAndValue.push({object:'selTimeUnit[]', value :'timeunitkey'}); 
		objAndValue.push({object:'hidGramasi[]', value :'gramasi'}); 
        var objAndValueForDetailAutoComplete = objAndValue;  
        
        this.tabID = tabID;    
    
        this.rs = (rs.length > 0) ? rs[0] : null;
    
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
            
                        
                detailRow.find("[name=\"selTimeUnit[]\"]").find("option:not(:selected)").attr('disabled', true);
                detailRow.find("[name=\"selTimeUnit[]\"]").change();
            
                updateAvailableUnit(itemKeyObj, selUnitObj);
                //thisObj.updateUnitPrice(selUnitObj);
				thisObj.updateAvailableTimeUnit(itemKeyObj, selTimeUnitObj);
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
 
                                tabObj.find("[name=recipientName]").val(data.name);
                                tabObj.find("[name=recipientPhone]").val(data.phone);
                                tabObj.find("[name=recipientEmail]").val(data.email);
                                tabObj.find("[name=recipientAddress]").val(data.address);  
                                tabObj.find("[name=hidRecipientCityKey]").val(data.citykey);  
                                tabObj.find("[name=recipientCityName]").val(data.cityandcategoryname);  
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
                data: "action=getUnitSellingPrice&itemkey="+itemKey+"&unitkey=" + unitKey ,  
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
     
        this.updateRecipients = function updateRecipients(){
          
                    var isDropship = false;

                    var recipientName =  "";
                    var recipientPhone = "";
                    var recipientEmail = "";
                    var recipientAddress =  ""; 

                    recipientName = tabObj.find("[name=recipientName]" ).val(); 
                    recipientPhone = tabObj.find("[name=recipientPhone]" ).val(); 
                    recipientEmail = tabObj.find("[name=recipientEmail]" ).val(); 
                    recipientAddress = tabObj.find("[name=recipientAddress]" ).val();  

                    var informationExist  = false;

                     if (recipientName != "" || recipientPhone  != "" || recipientEmail != "" || recipientAddress != "" )
                         informationExist = true;

                    if (informationExist == false){
                       thisObj.updateCustomerInformation();
                    } else{

                        var obj = this; 
                        $( "#dialog-message" ).html("Apakah Anda ingin mengganti data pengiriman dengan data default untuk pelanggan ini ?");
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
        
        this.updateTimeUnit = function updateTimeUnit(obj){ 
            $(obj).closest(".transaction-detail-row").find(".time-unit").html($(obj).find('option:selected').text());
        }   
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        } 
           
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='totalDays[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); thisObj.calculateDetail(this); });  
            bindEl(tabObj.find("[name='selTimeUnit[]']"),'change',function(){ thisObj.updateTimeUnitPrice(this); thisObj.calculateDetail(this); thisObj.updateTimeUnit(this); });  
        }
        
        this.loadOnReady = function loadOnReady(){
               
            thisObj.rebindEl(); 
  
        } 
     }
