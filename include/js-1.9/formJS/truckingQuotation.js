function TruckingQuotation(tabID, data){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    
 
   
    this.calculateDetail = function calculateDetail(obj)
    {
        var row = $(obj).closest(".transaction-detail-row");
 

        var qty = parseInt(unformatCurrency(row.find("[name='qty[]']").val()));
        var price = parseInt(unformatCurrency(row.find("[name='price[]']").val()));

        var subtotal = qty * price;

        row.find("[name='subtotal[]']").val(subtotal).blur();

        thisObj.calculateTotal();
    }

    this.calculateTotal = function calculateTotal()
    {
            var total = 0;   
             
            tabObj.find("[name='subtotal[]']").each(function(){   
                total += parseInt(unformatCurrency($(this).val())) || 0; 
            })     

           tabObj.find("[name='total']").val(total).blur(); 
    
    }
    this.afterRemoveRowHandler = function afterRemoveRowHandler()
    {
        thisObj.calculateTotal();
    }
 
      
    this.rebindEl = function rebindEl(){   
//        bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=2&serviceCost=0', thisObj.updateDetail);
  
        bindEl(tabObj.find("[name='qty[]']"), 'change', function () { thisObj.calculateDetail(this) });
        bindEl(tabObj.find("[name='price[]']"), 'change', function () { thisObj.calculateDetail(this) });

    } 
     
    this.loadOnReady = function loadOnReady(){ 
        thisObj.rebindEl(); 

         tabObj.find("[name='price[]'], [name='qty[]']").change(function() { 
            thisObj.calculateDetail(this); 
        });
    
        tabObj.find("[name=btnAddRows]").on('click', function () {
            addNewTemplateRow("quotation-row-template", null, null, thisObj.rebindEl  );
        }); 

         if (!data['rsDetail'] || data['rsDetail'].length <= 0)
            addNewTemplateRow("quotation-row-template", null, null, thisObj.rebindEl);

//          thisObj.calculateDetail(); 
    }
    
}
