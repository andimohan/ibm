function OfferSimulator(tabID, rs) {  
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
                thisObj.updateUnitPrice(selUnitObj);
             
                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 
                
                thisObj.calculateDetail(itemKeyObj);
 
         }
        
	    this.calculateDetail = function calculateDetail(obj){      
               
                    var row =  $(obj).closest(".transaction-detail-row");   
                    var itemkey =  row.find("[name='hidItemKey[]']").val();
                
                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
            
                    var selUnitObj = row.find("[name='selUnit[]']");
                    var unitkey =  selUnitObj.val();
                    var conversionmultiplier =  parseFloat(selUnitObj.find("option:selected").attr('relconversionmultiplier'));
            
                    var gramasi =  parseFloat(row.find("[name='hidGramasi[]']").val());
               

                    var subtotal = qty  *  priceInUnit ;
                    row.find("[name='detailSubtotal[]']").val(subtotal).blur(); 
                    row.find("[name='hidGramasiSubtotal[]']").val(gramasi * qty * conversionmultiplier).blur(); 

                    thisObj.calculateTotal();
	       }
	
	    this.calculateTotal = function calculateTotal(){  
         
                    var subtotal = 0; 
                    tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })

		            tabObj.find("[name='total']").val(subtotal).blur();
		 
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
         
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          thisObj.calculateTotal();  
        }
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         thisObj.calculateTotal(); 
        }
           
        
        this.rebindEl = function rebindEl(){  
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData',thisObj.updateDetail);
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='selUnit[]']"),'change',function(){ thisObj.updateUnitPrice(this); thisObj.calculateDetail(this); });  
        }
        
        this.loadOnReady = function loadOnReady(){ 
            thisObj.rebindEl();  
        } 
     }
