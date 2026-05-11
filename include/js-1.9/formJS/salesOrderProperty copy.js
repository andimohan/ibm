function SalesOrderProperty(tabID,rs) {  
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabObj = tabObj;
		this.rs = (rs.length > 0) ? rs[0] : null;
    
   		var objAndValue = new Array;   
        objAndValue.push({object:'hidDownpaymentKey[]', value :'pkey'}); 
        objAndValue.push({object:'downpaymentAmount[]', value :'outstanding'});  
        var objAndValueForDPDetailAutoComplete = objAndValue;
 
        this.tabID = tabID;    
     
        this.calculateTotalDownpayment = function calculateTotalDownpayment(){
            var totalDP = 0; 
            tabObj.find("[name='downpaymentAmount[]']").each(function() { totalDP += parseInt(unformatCurrency($(this).val())) || 0;   })
            tabObj.find("[name='totalDownpayment']").val(totalDP).blur(); 
             
            return totalDP;
         }
	
	    this.calculateTotal = function calculateTotal(){  
               	var basicAgentAdminFee = 0;
			
				var transactionTotal = parseFloat(unformatCurrency(tabObj.find("[name='transactionTotal']").val())) || 0 ;
				var bankTotal = parseFloat(unformatCurrency(tabObj.find("[name='bankTotal']").val())) || 0 ;
				var closingFeeTotal = parseFloat(unformatCurrency(tabObj.find("[name='closingFee']").val())) || 0 ;
				var cashRewardTotal = parseFloat(unformatCurrency(tabObj.find("[name='cashReward']").val())) || 0 ;
				var totalDP = thisObj.calculateTotalDownpayment();
				var agencyFeePercentage =  parseFloat(unformatCurrency(tabObj.find("[name='agencyPercentage']").val())) || 0 ; 
				var agentFeePercentage =  parseFloat(unformatCurrency(tabObj.find("[name='agentFeePercentage']").val())) || 0 ; 
				var officeFeePercentage =  parseFloat(unformatCurrency(tabObj.find("[name='officeFeePercentage']").val())) || 0 ; 
				var adminFeePercentage =  parseFloat(unformatCurrency(tabObj.find("[name='adminFeePercentage']").val())) || 0 ; 
				var taxFeePercentage =  parseFloat(unformatCurrency(tabObj.find("[name='taxFeePercentage']").val())) || 0 ; 
				var orLeadPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='orLeadPercentage']").val())) || 0 ; 
                var bankProvisionPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='bankProvisionPercentage']").val())) || 0 ; 
                var agentFeeBankPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='agentFeeBankPercentage']").val())) || 0 ; 
                var officeFeeBankPercentage =  parseFloat(unformatCurrency(tabObj.find("[name='officeFeeBankPercentage']").val())) || 0 ; 

				var agencyFeeTotal = transactionTotal * agencyFeePercentage / 100; // komisi penjualan 
                var orLeadTotal = transactionTotal * orLeadPercentage / 100 ;
        
				var basicAgentTotal = agencyFeeTotal;
				var officeFeeCommTotal = basicAgentTotal * officeFeePercentage / 100;
				var agentFeeCommTotal = basicAgentTotal * agentFeePercentage / 100; 
            
                var bankProvisionTotal = bankTotal * bankProvisionPercentage / 100 ;    
                var officeFeeBankTotal = bankProvisionTotal * officeFeeBankPercentage / 100;
				var agentFeeBankTotal = bankProvisionTotal * agentFeeBankPercentage / 100; 

				var taxFeeTotal = officeFeeCommTotal * taxFeePercentage / 100 ;
			
				basicAgentAdminFee = agentFeeCommTotal; // + agentFeeBankTotal;
                var adminFeeTotal = basicAgentAdminFee * adminFeePercentage / 100 ;

                var totalCompanyRevenue =   officeFeeCommTotal + officeFeeBankTotal + adminFeeTotal + orLeadTotal;
                var totalAgentRevenue =   agentFeeCommTotal + agentFeeBankTotal + closingFeeTotal + cashRewardTotal;

				tabObj.find("[name='agencyFee']").val(agencyFeeTotal).blur(); 
				tabObj.find("[name='officeFee']").val(officeFeeCommTotal).blur(); 
				tabObj.find("[name='agentFee']").val(agentFeeCommTotal).blur(); 
            
                tabObj.find("[name='bankProvision']").val(bankProvisionTotal).blur(); 
                tabObj.find("[name='officeFeeBank']").val(officeFeeBankTotal).blur(); 
                tabObj.find("[name='agentFeeBank']").val(agentFeeBankTotal).blur(); 
            
                tabObj.find("[name='totalCommissionCompany']").val(officeFeeCommTotal).blur(); 
                tabObj.find("[name='totalBankProvisionCompany']").val(officeFeeBankTotal).blur(); 
                tabObj.find("[name='totalCommissionAgent']").val(agentFeeCommTotal).blur(); 
                tabObj.find("[name='totalBankProvisionAgent']").val(agentFeeBankTotal).blur();
            
				tabObj.find("[name='adminFee']").val(adminFeeTotal).blur(); 
				tabObj.find("[name='orLead']").val(orLeadTotal).blur(); 
                tabObj.find("[name='taxFee']").val(taxFeeTotal).blur(); 

                tabObj.find("[name='totalCompanyRevenue']").val(totalCompanyRevenue).blur();
                tabObj.find("[name='totalAgentRevenue']").val(totalAgentRevenue).blur();
			
				var downpaymentSettlement = (totalDP > 0) ? totalDP - (agencyFeeTotal+orLeadTotal) : 0;
                tabObj.find("[name='downpaymentSettlement']").val(downpaymentSettlement).blur();


				var balance = transactionTotal - totalDP; 

				tabObj.find("[name='balance']").val(balance).blur();

	       }
 	
		this.updatePercentageAgent = function updatePercentageAgent(){

            var agentkey = tabObj.find("[name=hidEmployeeKey]" ).val();  
    

            $.ajax({
                type: "GET",
                url:  'ajax-employee.php',
                async: false,
                data: "action=getDataRowById&pkey=" + agentkey ,  
            }).done(function( data ) {  

                if (!data ) return;

                data = JSON.parse(data) ; 
                data = data[0];
                if ( data.length  == 0  ) return; 

                tabObj.find("[name=agentFeePercentage]").val(data.commissionpercentage).change().blur(); 
                tabObj.find("[name=agentFeeBankPercentage]").val(data.commissionpercentage).change().blur(); 
           }); 
            
            
        }
        
        this.updatePercentageType = function updatePercentageType(){

            var typekey = tabObj.find("[name=selType]" ).val();  
    
            $.ajax({
                type: "GET",
                url:  'ajax-sales-order-property-type.php',
                async: false,
                data: "action=getDataRowById&pkey=" + typekey ,  
            }).done(function( data ) {  
                if (!data ) return;

                data = JSON.parse(data) ;  
                data = data[0];

                if ( data.length  == 0  ) return;

                tabObj.find("[name=agencyPercentage]").val(data.percentagevalue).change().blur();  
				
           }); 
            
            
        }
		  
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         	thisObj.calculateTotal(); 
        }
        
        this.onChangeBuyer = function onChangeBuyer(){  
			thisObj.rebindDownpayment();
		}
		
		this.afterAddNewTemplateRowHandler = function afterAddNewTemplateRowHandler(){
			thisObj.rebindDownpayment(); 
		}
		
        this.rebindDownpayment = function rebindDownpayment(){ 
            var customerkey = tabObj.find("[name=hidBuyerKey]").val() || 0;    
            bindAutoCompleteForTransactionDetail('downpaymentCode[]',objAndValueForDPDetailAutoComplete,'ajax-customer-downpayment.php?action=searchData&customerkey='+customerkey);  
		 	tabObj.find("[name=\"downpaymentAmount[]\"]").bind( "change", function(event) {  thisObj.calculateTotal(); })  
		}

        this.rebindEl = function rebindEl(){  
              
            var tableDownPaymentDetail = tabObj.find(".mnv-downpayment");   
            bindEl(tableDownPaymentDetail.find('.mnv-detail-field'),'change',function(){ onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
            bindEl(tableDownPaymentDetail.find('.remove-button'),'click',function(){ removeDetailRows(this);  onChangePaymentMethodHandler(thisObj,tableDownPaymentDetail, 'downpayment-row-template'); });
 
            thisObj.rebindDownpayment();   
        }
        
        this.loadOnReady = function loadOnReady(){
              
            tabObj.find("[name=transactionTotal], [name=agencyPercentage], [name=officeFeePercentage],[name=agentFeePercentage], [name=adminFeePercentage], [name=orLeadPercentage], [name=bankProvisionPercentage],[name=taxFeePercentage],[name=officeFeeBankPercentage],[name=officeFeeBank],[name=bankTotal],[name=agentFeeBankPercentage],[name=agentFeeBank],[name=totalCommissionCompany],[name=totalBankProvisionCompany],[name=totalCompanyRevenue],[name=totalCommissionAgent],[name=totalBankProvisionAgent],[name=closingFee],[name=cashReward],[name=totalAgentRevenue]" ).change(function(){thisObj.calculateTotal(this)}) ;
		
            tabObj.find("[name=selType]").change(function() { thisObj.updatePercentageType(); });	
			tabObj.find("[name=officeFeePercentage]").change(function(){ 
					if($(this).attr("dont-change") && $(this).attr("dont-change") == 1){
						$(this).attr("dont-change",0);
						return;	
					} 
					tabObj.find("[name=agentFeePercentage]").attr("dont-change",1);
					var thisPercentage =  parseFloat(unformatCurrency($(this).val())) || 0 ;  
					if(thisPercentage > 100) thisPercentage = 100;
					if(thisPercentage < 0) thisPercentage = 0;
					tabObj.find("[name=agentFeePercentage]").val(100 - thisPercentage).change().blur();
			});
			
			tabObj.find("[name=agentFeePercentage]").change(function(){ 
					if($(this).attr("dont-change") && $(this).attr("dont-change") == 1){
						$(this).attr("dont-change",0);
						return;	
					} 
				
					tabObj.find("[name=officeFeePercentage]").attr("dont-change",1);
					var thisPercentage =  parseFloat(unformatCurrency($(this).val())) || 0 ;   
					if(thisPercentage > 100) thisPercentage = 100;
					if(thisPercentage < 0) thisPercentage = 0;
					tabObj.find("[name=officeFeePercentage]").val(100 - thisPercentage).change().blur();
			}) ;
            
            
            tabObj.find("[name=officeFeeBankPercentage]").change(function(){ 
					if($(this).attr("dont-change") && $(this).attr("dont-change") == 1){
						$(this).attr("dont-change",0);
						return;	
					} 
					tabObj.find("[name=agentFeeBankPercentage]").attr("dont-change",1);
					var thisPercentage =  parseFloat(unformatCurrency($(this).val())) || 0 ;  
					if(thisPercentage > 100) thisPercentage = 100;
					if(thisPercentage < 0) thisPercentage = 0;
					tabObj.find("[name=agentFeeBankPercentage]").val(100 - thisPercentage).change().blur();
			});
			
            tabObj.find("[name=agentFeeBankPercentage]").change(function(){ 
					if($(this).attr("dont-change")  && $(this).attr("dont-change") == 1){
						$(this).attr("dont-change",0);
						return;	
					} 
					tabObj.find("[name=officeFeeBankPercentage]").attr("dont-change",1);
					var thisPercentage =  parseFloat(unformatCurrency($(this).val())) || 0 ;  
					if(thisPercentage > 100) thisPercentage = 100;
					if(thisPercentage < 0) thisPercentage = 0;
					tabObj.find("[name=officeFeeBankPercentage]").val(100 - thisPercentage).change().blur();
			});
			
			// kalo pas awal kechange, nilai komisi akan selalu load ulang, jd tidak sama dengan yg disave di DB
			if(!this.rs)
				tabObj.find("[name=selType]").change();
		     
			if(tabObj.find(".downpayment-row").length == 0)
            	addNewTemplateRow("downpayment-row-template");   
			
            thisObj.rebindEl(); 
  
        } 
     }
