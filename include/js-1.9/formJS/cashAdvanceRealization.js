function CashAdvanceRealization(tabID, rs){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
        this.tabID = tabID;    
     	this.rs = (rs.length > 0) ? rs[0] : null;
        var objAndValue = new Array;
		objAndValue.push({object:'hidCashAdvanceKey[]', value :'pkey'}); 
		objAndValue.push({object:'cashAdvanceAmount[]', value :'amount'}); 
		objAndValue.push({object:'cashAdvanceRecipient[]', value :'employeename'}); 
        var objAndValueForDetailCashAutoComplete = objAndValue;
	   
		var objAndValue = new Array; 
		objAndValue.push({object:'hidServiceKey[]', value :'pkey'});   
        var objAndValueForDetailServiceAutoComplete = objAndValue;
    
    	var objAndValue = new Array;
		objAndValue.push({object:'hidJobOrderKey[]', value :'pkey'});  
        var objAndValueForDetailJobAutoComplete = objAndValue;
      	
		var objAndValue = new Array;
		objAndValue.push({object:'hidJobHeaderKey[]', value :'pkey'});  
        var objAndValueForDetailJobHeaderAutoComplete = objAndValue;
         
        var objAndValue = new Array;
        objAndValue.push({object:'hidSupplierKey[]', value :'pkey'});  
        var objAndValueForSupplierAutoComplete = objAndValue;
            
    	var objAndValue = new Array;
		objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'});  
        var objAndValueForDetailContainerAutoComplete = objAndValue;
	 
		var objAndValue = new Array;
		objAndValue.push({object:'hidContainerHeaderDetailKey[]', value :'pkey'});  
        var objAndValueForDetailContainerHeaderAutoComplete = objAndValue;
    
        var objAndValue = new Array;

		objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'});  
        var objAndValueForDetailDownpaymentAutoComplete = objAndValue;
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidCOAKey[]', value :'pkey'});  
        var objAndValueForDetailCOAAutoComplete = objAndValue;
    
        var  objAndValue = new Array;
        objAndValue.push({object:'hidCostKey[]', value :'pkey'}); 
        var objAndValueForCostDetailAutoComplete  = objAndValue; 
    
    
        var id = tabObj.find("[name=hidId]").val();  
    
    
        this.updateCashAdvanceInformation = function updateCashAdvanceInformation(){
            var cashKey = tabObj.find("[name=hidCashAdvanceKey]").val();
              
             $.ajax({
                    type: "GET",
                    url:  'ajax-cash-advance.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + cashKey ,  
                }).done(function( data ) { 
 
                        if(!data) return;
                 
                        data = JSON.parse(data) ; 
                        data = data[0];
  
                        tabObj.find("[name=recipient]").val(data.employeename);
                        tabObj.find("[name=amount]").val(data.amount);
                        
                        tabObj.find(".inputnumber").change().blur(); 
                        thisObj.calculateTotal(); 
                 
                });
  
        } 
         
        this.updateJobType = function updateJobType(obj){
            var jobType = $(obj).val();
              
            $row = $(obj).closest(".transaction-detail-row");
              
            $row.find("[class*=type-]").hide();   
            $row.find(".type-"+jobType).show();
	  
            // kalo ad reftranskey, sudah ad transaksi (readonly), jd harus return
            var hasRefTransKey =  $row.find("[name='hidRefTransKey[]']").val() || 0;
            if(hasRefTransKey != 0 ) return;
            
	  		if(jobType==1 || jobType==4){
                $row.find("[name='taxPercentage[]']").prop("readonly",false);  
                $row.find("[name='chkIncludeTax[]']").prop("readonly",false);   
                $row.find("[name='dummychkIncludeTax[]']").prop("readonly",false);   
                
                if(jobType==1)
                    $row.find("[name='jobOrderCode[]']").prop("readonly",false);   
                else
                    $row.find("[name='jobHeaderCode[]']").prop("readonly",false);   
                    
                $row.find("[name='containerDetailName[]']").prop("readonly",false);   
                $row.find("[name='serviceName[]']").prop("readonly",false);   
                $row.find("[name='supplierName[]']").prop("readonly",false);   
                $row.find("[name='refCode[]']").prop("readonly",false);   
             }else if(jobType==2){ 
                $row.find("[name='taxPercentage[]']").prop("readonly",true);   
                $row.find("[name='chkIncludeTax[]']").prop("readonly",true);   
                $row.find("[name='dummychkIncludeTax[]']").prop("readonly",true);   
                $row.find("[name='jobOrderCode[]']").prop("readonly",true);   
                $row.find("[name='containerDetailName[]']").prop("readonly",true);   
                $row.find("[name='serviceName[]']").prop("readonly",true); 
				$row.find("[name='refCode[]']").prop("readonly",true);  
                $row.find("[name='supplierName[]']").prop("readonly",false);  
             }else{
                $row.find("[name='taxPercentage[]']").prop("readonly",true);   
                $row.find("[name='chkIncludeTax[]']").prop("readonly",true);   
                $row.find("[name='dummychkIncludeTax[]']").prop("readonly",true);   
                $row.find("[name='supplierName[]']").prop("readonly",true); 
				$row.find("[name='serviceName[]']").prop("readonly",true); 
				$row.find("[name='refCode[]']").prop("readonly",true); 
             }     
 
        }

        
         this.calculateTotalCost = function calculateTotalCost(){
            var totalCost = 0; 
            tabObj.find("[name='costAmount[]']").each(function() { totalCost += parseFloat(unformatCurrency($(this).val())) || 0;   })
            tabObj.find("[name='totalCost']").val(totalCost).blur(); 

            return totalCost;
        }     
        this.calculateDetail = function calculateDetail(obj){   
     
            var rowObj =  $(obj).closest(".transaction-detail-row");  
            
            var qty = parseFloat(unformatCurrency(rowObj.find("[name='qty[]']").val())) || 0;
            var priceInUnit = parseFloat(unformatCurrency(rowObj.find("[name='amountDetail[]']").val())) || 0;   
            var taxPercentage = parseFloat(unformatCurrency(rowObj.find("[name='taxPercentage[]']").val())) || 0;    
            var includeTax = rowObj.find("[name='chkIncludeTax[]']").val() 
            
            var subtotal = qty * priceInUnit; 
            var total = 0;
            if (includeTax == 0) {

                taxValue = subtotal * taxPercentage / 100;
                subtotal += taxValue;
            }

            total = subtotal;
            rowObj.find("[name='subtotal[]']").val(total).blur();

            thisObj.calculateTotal();
        } 
        
        this.calculateTotal = function calculateTotal(){
               
            var amount = 0; 
            var totalCash = 0; 
            var totalCost = 0;
            var totalPPH = 0;
            
			tabObj.find("[name='cashAdvanceAmount[]']").each(function(){   
                    totalCash += parseFloat(unformatCurrency($(this).val())) || 0; 
            })
			tabObj.find("[name='amount']").val(totalCash).blur();  

            tabObj.find("[name='subtotal[]']").each(function(){   
                    amount += parseFloat(unformatCurrency($(this).val())) || 0; 
            })     
            
			tabObj.find("[name='pphAmount[]']").each(function(){   
                    totalPPH += parseFloat(unformatCurrency($(this).val())) || 0; 
            })
            

            var totalCost = thisObj.calculateTotalCost();
            amount += totalCost;

            var balance = totalCash-amount+totalPPH;
            tabObj.find("[name='total']").val(amount).blur();  
            tabObj.find("[name='totalPPH']").val(totalPPH).blur();  
            tabObj.find("[name='balance']").val(balance).blur();  
              
              
        } 
        
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
               
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
            thisObj.calculateTotal(); 

            // gk boleh kereset harusnya, data yang sudah keisi jd kereset jg

            thisObj.rebindCost();
        }        
               
        this.rebindCost = function rebindCost(){   
            bindAutoCompleteForTransactionDetail('costName[]',objAndValueForCostDetailAutoComplete,'ajax-cost-cash-out.php?action=searchData'); 
        }         

         this.rebindEl = function rebindEl(){  
         var tableCostDetail = tabObj.find(".mnv-cost");   
            bindEl(tableCostDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
            bindEl(tableCostDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableCostDetail, 'cost-row-template'); });
           
            bindEl(tabObj.find("[name='cashAdvanceAmount[]'],[name='qty[]'], [name='amountDetail[]'], [name='taxPercentage[]'], [name='chkIncludeTax[]']"),'change',function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='pphAmount[]']"),'change',function(){ thisObj.calculateTotal() }); 
            bindEl(tabObj.find("[name='selJobType[]']"),'change',function(){ thisObj.updateJobType(this) }); 
            bindAutoCompleteForTransactionDetail('supplierName[]',objAndValueForSupplierAutoComplete,'ajax-supplier.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('containerDetailName[]',objAndValueForDetailContainerAutoComplete,'ajax-container.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForDetailServiceAutoComplete,'ajax-item.php?action=searchData&itemtype=3');     
            bindAutoCompleteForTransactionDetail('jobOrderCode[]',objAndValueForDetailJobAutoComplete,'ajax-emkl-job-order.php?action=searchData&statuskey=(1,2,3)&limit=5');     
            bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDetailDownpaymentAutoComplete,'ajax-supplier-downpayment.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailCOAAutoComplete,'ajax-coa.php?action=searchData');
			bindAutoCompleteForTransactionDetail('jobHeaderCode[]',objAndValueForDetailJobHeaderAutoComplete,'ajax-emkl-job-order-header.php?action=searchData&statuskey=1&limit=5'); 
			bindAutoCompleteForTransactionDetail('containerHeaderDetailName[]',objAndValueForDetailContainerHeaderAutoComplete,'ajax-container.php?action=searchData');  
			bindAutoCompleteForTransactionDetail('cashAdvanceCode[]',objAndValueForDetailCashAutoComplete,'ajax-cash-advance.php?action=searchDataAdvance');  

            thisObj.rebindCost();
        }   
          
            
        this.loadOnReady = function loadOnReady(){   
			tabObj.find("[name=btnAddDetailRow]").on('click', function() { addNewTemplateRow("job-row-template",null,null,thisObj.rebindEl); }); 
			if (!thisObj.rs)
                addNewTemplateRow("job-row-template",null,null,thisObj.rebindEl);
			
			var totalRowTemplate = tabObj.find("[name='hidDetailItemKey[]']").length;
			if(totalRowTemplate <= 1)
				addNewTemplateRow("detail-row-template",null,null,thisObj.rebindEl);
			
			

				addNewTemplateRow("cost-row-template",null,null,thisObj.rebindEl);

			thisObj.rebindEl(); 
			tabObj.find("[name='selJobType[]']").change();  
 
        } 
        
}
