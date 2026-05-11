function InvoiceTax(tabID,varConstant,opt) {   
	var thisObj = this;
	var tabObj = $("#" + tabID);    
	this.tabID = tabID;  
    
    this.useStorage = varConstant.USE_STORAGE;  
        
    var id = tabObj.find("[name=hidId]").val();

    var fileFolder = opt.fileFolder;
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = (opt.arrFile) ? opt.arrFile : Array();

	this.updateTransactionType = function updateTransactionType(){  
		var transactionType = tabObj.find("[name=selType]").val(); 
 
		 tabObj.find(".transtype").hide();
		 tabObj.find(".transtype-" + transactionType).show();
  
	}         

 	this.updateTruckingInvoice = function updateTruckingInvoice(){
     
			  $.ajax({
                type: "GET",
                url:  'ajax-trucking-service-order-invoice.php', 
				asyc: false, 
                data: "action=getTaxPercentageType&pkey=" +  tabObj.find("[name=hidRefTruckingInvoiceHeaderKey]" ).val() ,  
                success: function(data){ 
					var selectOpt = JSON.parse(data);  

					var selTax = tabObj.find("[name=\"selTaxPercentage\"]");
					reInsertSelectBox(selTax,selectOpt, {"key" : "pkey", "label" : "taxpercentage"} ); 
					selTax.val(tabObj.find("[name=\"selTaxPercentage\"] option:first").val()).change();
                }
              }) 
			 
    }    
    
      this.updateEMKLInvoice = function updateEMKLInvoice(){
 
                  $.ajax({
                    type: "GET",
                    url:  'ajax-emkl-order-invoice.php', 
                    asyc: false, 
                    data: "action=getTaxPercentageType&pkey=" +  tabObj.find("[name=hidRefEMKLInvoiceHeaderKey]" ).val(),  
                    success: function(data){  
                        var selectOpt = JSON.parse(data);   
                        var selTax = tabObj.find("[name=\"selTaxPercentage\"]");
                        reInsertSelectBox(selTax,selectOpt, {"key" : "pkey", "label" : "taxpercentage"} ); 
                        selTax.val(tabObj.find("[name=\"selTaxPercentage\"] option:first").val()).change();
                    }
                  })


       }  
	  
	this.rebindEl = function rebindEl(){

	}


	this.loadOnReady = function loadOnReady(){  
        
        if(thisObj.useStorage){
            
        }else{ 

             if (fileFolder) {

                if (id) {
                    createFileUploader(fileUploaderTarget, fileFolder, id, arrFile, true);
                } else {
                    createFileUploader(fileUploaderTarget, fileFolder, "", "", true);
                }

                tabObj.find(".file-list").sortable({
                    placeholder: "sortable-placeholder",
                    stop: function (event, ui) {
                        updateItemFileArray(opt.fileUploaderTarget);
                    }
                });
                tabObj.find(".file-list").disableSelection();

            }
            
        }
        
		tabObj.find("[name=selType]").change(function() { thisObj.updateTransactionType(); }); 
		tabObj.find("[name=selType]" ).change();   

	   thisObj.rebindEl(); 
	}
}
