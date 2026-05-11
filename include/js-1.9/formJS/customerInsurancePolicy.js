function CustomerInsurancePolicy(tabID, opt){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID; 
    var id = tabObj.find("[name=hidId]").val();

    var objAndValue = new Array;  
    objAndValue.push({object:'selSupplier', value :'pkey'});
    var objAndValueForDetailAutoComplete = objAndValue; 

    this.updateCustomer = function updateCustomer() {
        var customerkey = tabObj.find("[name=hidRefKey]").val();
        if (!customerkey)
            return;

		// update content
        $.ajax({
            type: "GET",
            url: 'ajax-customer.php',
            async: false,
            data: "action=getDataRowById&pkey=" + customerkey
        }).done(function (data) {

            data = JSON.parse(data);
            data = data[0];
			
			tabObj.find("[name=categoryName]").val(data.categoryname);
			tabObj.find("[name=selCountry]").val(data.countrykey);
			
			// sisanya gk perlu dulu, karena individu tdk bisa add manual
			  
        });
		
		// update insurance company
		
		 $.ajax({
            type: "GET",
            url: 'ajax-customer.php',
            async: false,
            data: "action=getInsuranceCompanyDetail&pkey=" + customerkey
        }).done(function (data) {

            data = JSON.parse(data);
			 
			 // update combobox services
			var newOptions = {};
			for(i=0;i<data.length;i++)  
				newOptions[data[i].supplierkey] =  data[i].suppliername;       
			 
			var selInsuranceObj = tabObj.find("[name=selInsuranceCompany]"); 
			var options = (selInsuranceObj.prop) ? selInsuranceObj.prop('options') : selInsuranceObj.attr('options');   
			$('option', selInsuranceObj).remove();

			$.each(newOptions, function(val, text) {
				options[options.length] = new Option(text, val);
			});
   
        });
		
    }
 
    this.removeReadonly = function removeReadonly() {
        var categorykey = tabObj.find("[name=selCategory]").val();
        if (categorykey == 1) {
            tabObj.find("[name=name]").prop("readonly", true);
            tabObj.find("[name=policyNumber]").prop("readonly", true);
            tabObj.find("[name=IDNumber]").prop("readonly", true);
            tabObj.find("[name=hidNationalityKey]").prop("readonly", true);
            tabObj.find("[name=nationality]").prop("readonly", true);
            tabObj.find("[name=dateOfBirth]").prop("readonly", true);
            tabObj.find("[name=phone]").prop("readonly", true);
            tabObj.find("[name=mobile]").prop("readonly", true);
            tabObj.find("[name=email]").prop("readonly", true);
            tabObj.find("[name=hidSupplierKey]").prop("readonly", true);
            tabObj.find("[name=supplierName]").prop("readonly", true); 
        } else {
            tabObj.find("[name=name]").removeAttr("readonly");
            tabObj.find("[name=policyNumber]").removeAttr("readonly");
            tabObj.find("[name=IDNumber]").removeAttr("readonly");
            tabObj.find("[name=nationality]").removeAttr("readonly");
            tabObj.find("[name=hidNationalityKey]");
            tabObj.find("[name=dateOfBirth]").removeAttr("readonly");
            tabObj.find("[name=phone]").removeAttr("readonly");
            tabObj.find("[name=mobile]").removeAttr("readonly");
            tabObj.find("[name=email]").removeAttr("readonly");
            tabObj.find("[name=hidSupplierKey]");
            tabObj.find("[name=supplierName]").removeAttr("readonly");  
        }
    }


    this.rebindEl = function rebindEl(){     
    }   
    
    this.loadOnReady = function loadOnReady(){
        //tabObj.find("[name=customerName]").change();
        // tabObj.find("[name=customerName]").change(function() { thisObj.updateInsuranceCompany(); }); 
        if (id) {
            thisObj.removeReadonly()
        }
        thisObj.rebindEl(); 
    }
    
} 
