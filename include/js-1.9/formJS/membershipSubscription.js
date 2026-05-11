function MembershipSubscription(tabID, rs, cashTOP) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
//        this.customCodeCache=[];
    
//		var objAndValue = new Array;  
//		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
//	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'});  
//        var objAndValueForDetailAutoComplete = objAndValue;  
        
	 
        this.tabID = tabID;    
    
        this.rs = (rs.length > 0) ? rs[0] : null;
      
//		this.calculateVoucherAmount = function calculateVoucherAmount(target,objAndValue,ui){
//			 	var detailRow = $(target).closest(".transaction-detail-row");  
//               
//                for(i=0;i<objAndValue.length;i++){     
//					
//					if(objAndValue[i].object == 'voucherAmount[]'){
//			 			thisObj.recalculateVoucherAmount(detailRow);
//						continue;
//					}
//					
//                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
//					
//                } 
//			
//                // harus handle manual utk obj autosearch
//                detailRow.find("[name=\"voucherCode[]\"]").first().val(ui.item['value']); 
//               
//                thisObj.calculateTotal(false);
//		}
		 
        
//	    this.calculateDetail = function calculateDetail(obj){      
//               
//                    var row =  $(obj).closest(".transaction-detail-row");   
//                    var itemkey =  row.find("[name='hidItemKey[]']").val();
//                
//                    var qty =  unformatCurrency(row.find("[name='qty[]']").val());
//                    var priceInUnit =  unformatCurrency(row.find("[name='priceInUnit[]']").val());
//                    var discount =  unformatCurrency(row.find("[name='discountValueInUnit[]']").val());
//                    var discountType =  unformatCurrency(row.find("[name='selDiscountType[]']").val());
//            
//                    var selUnitObj = row.find("[name='selUnit[]']");
//                 
//					if (discount != 0 && discountType == 2)  discount = discount/100 * priceInUnit;
//
//                    var subtotal = qty  *  (priceInUnit - discount);
//                    row.find("[name='detailSubtotal[]']").val(subtotal).blur();
//			
//                    thisObj.calculateTotal();
//	       }
	
	    this.calculateTotal = function calculateTotal(recalculateVoucher){  
         			
					if(!recalculateVoucher) recalculateVoucher = true;
			
//                    var subtotal = 0; 
//                    tabObj.find("[name='detailSubtotal[]']").each(function(){ subtotal += parseInt(unformatCurrency($(this).val())) || 0;  })
//                    tabObj.find("[name='subtotal']").val(subtotal).blur();
			
					// hitung ulang nilai voucher
//					voucherList = tabObj.find("[name='voucherAmount[]']");
//					var voucherValue = 0;
//					
//					voucherList.each(function(){ 
//						// asumsi cuma satu baris saja dulu
//						var amount = thisObj.recalculateVoucherAmount($(this).closest(".transaction-detail-row"));
//						amount =  parseFloat(amount) || 0 ;
//						voucherValue += amount;
//					});

					var subtotal = parseFloat(unformatCurrency(tabObj.find("[name='subtotal']").val())) || 0 ;
                    var finalDiscount = parseFloat(unformatCurrency(tabObj.find("[name='finalDiscount']").val())) || 0 ;
                    var finalDiscountType = parseInt(unformatCurrency(tabObj.find("[name='selFinalDiscountType']").val())) || 0 ;
                    var pointValue = parseInt(unformatCurrency(tabObj.find("[name='pointValue']").val())) || 0 ;
                    var shipmentFee = parseInt(unformatCurrency(tabObj.find("[name='shipmentFee']").val())) || 0 ; 
                    var etcCost = parseInt(unformatCurrency(tabObj.find("[name='etcCost']").val())) || 0 ; 
                    var includeTax =   tabObj.find("[name='chkIncludeTax']").val();
                    var taxPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxPercentage']").val())) || 0 ; 
        
                    if (finalDiscount != 0 && finalDiscountType == 2)  finalDiscount = finalDiscount/100 * subtotal; 

                    subtotal -= finalDiscount; 
                    //subtotal -= voucherValue;
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
		 
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
          	thisObj.calculateTotal();   
        }
                    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         	thisObj.calculateTotal(); 
        }
		 
		this.updatePointValue = function updatePointValue(){
			  $.ajax({
                type: "GET",
                url:  'ajax-point.php?action=getPointValue&point='+tabObj.find("[name=point]").val(), 
                success: function(data){  
                        if (!data) return; 
						tabObj.find("[name='pointValue']").val(data).blur();
						thisObj.calculateTotal(); 
                } ,
                error: function(xhr, errDesc, exception) {
                      
                }
            }); 	
		}
		
		this.updatePrice = function updatePrice(){
			  
			  $.ajax({
                type: "GET",
                url:  'ajax-membership-subscription.php?action=getMembershipLevel&membershipLevelKey='+tabObj.find("[name=selMembershipLevel]").val(),
                beforeSend:function (xhr){  
               
                }, 
                success: function(data){ 
					
                        if (!data) return;
                        var data = JSON.parse(data);   
					 
						var price = (data[0]['sellingprice']) ? data[0]['sellingprice'] : 0;
					
						tabObj.find("[name='subtotal']").val(price).blur();
						thisObj.calculateTotal(); 
                } ,
                error: function(xhr, errDesc, exception) {
                      
                }
            }); 
			
			
		}
		
        this.rebindEl = function rebindEl(){  
           // bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-membership-level.php?action=searchData');
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]']" ), 'change',  function(){ thisObj.calculateTotal()}); 
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateTotal() });  
        	bindEl(tabObj.find("[name='selMembershipLevel']"),'change',function(){ thisObj.updatePrice() });  
        	bindEl(tabObj.find("[name='point']"),'change',function(){ thisObj.updatePointValue(); });  
        	 
            //bindEl($('.voucher-row .remove-button'),'click',function(){ removeDetailRows(this);  thisObj.calculateTotal(); }); 
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
 
       
            tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax],[name=shipmentFee], [name=etcCost], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal()}) 
            tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)})
			
           // customCodeHandler(thisObj);
            
            thisObj.rebindEl(); 
  
        } 
     }
