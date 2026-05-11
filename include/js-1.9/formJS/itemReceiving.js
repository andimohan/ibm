function ItemReceiving(tabID, fileUpload){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

    var fileFolder = fileUpload.uploadFolder;
	var fileUploaderTarget = fileUpload.uploaderTarget;
	var rsFile = fileUpload.rsFile;	 
    var id = tabObj.find("[name=hidId]").val();

	var arrFile = Array();

    // var objAndValue = new Array;  
	// objAndValue.push({object:'hidItemDetailKey[]', value :'pkey'}); 
    // objAndValue.push({object:'itemDetailName[]', value :'name'}); 
    // var objAndValueForDetailAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailTypeKey[]', value :'pkey'}); 
    objAndValue.push({object:'detailType[]', value :'name'}); 
    var objAndValueForDetailCategoryItemAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailCountryKey[]', value :'pkey'}); 
    objAndValue.push({object:'countryOfOriginId[]', value :'name'}); 
    var objAndValueForDetailCountryAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailBrandKey[]', value :'pkey'}); 
    objAndValue.push({object:'brandName[]', value :'name'}); 
    var objAndValueForDetailBrandAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidDetailBrandKey[]', value :'pkey'}); 
    objAndValue.push({object:'itemCategoryDetailName[]', value :'name'}); 
    var objAndValueForCategoryDetailAutoComplete = objAndValue;  

    var objAndValue = new Array;  
	objAndValue.push({object:'hidPackagingDetailKey[]', value :'pkey'}); 
    objAndValue.push({object:'packagingDetailName[]', value :'name'}); 
    var objAndValueForPackagingDetailAutoComplete = objAndValue;  

    this.updateDetail = function updateDetail(target,objAndValue,ui){
             
        var detailRow = $(target).closest(".transaction-detail-row");
        var itemKeyObj = detailRow.find("[name=\"hidItemDetailKey[]\"]").first();

        for(i=0;i<objAndValue.length;i++){   
            detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
        } 

        // harus handle manual utk obj autosearch
        detailRow.find("[name=\"itemDetailName[]\"]").first().val(ui.item['value']);

    }

    this.updateWarehouseLayout = function updateWarehouseLayout()
    {
        var selWarehouseKey = tabObj.find("[name=selWarehouseKey]").val();
        // var selWarehouseLayoutKey = tabObj.find("[name=selWarehouseLayoutKey]").val();

        if (!selWarehouseKey ) {
            return;
        }

        $.ajax({
            type: "GET",
            url: "ajax-warehouse-layout.php",
            data: "action=getDataLayout&warehousekey="+selWarehouseKey+'&istransit=1',
            success: function (data) {
                if (!data) return;

                var data = parseJSON(data);
                var i;
                var newOptions = {};
                
                //  tabObj.find("[name=selCurrentWarehouseLayoutKey]" ).val(data[0].pkey); 
                for (i = 0; i < data.length; i++) {
                    if (data[i].name) {
                        newOptions[data[i].pkey] =  data[i].name; 
                    }
                }
                
                var select = $("#" + tabID + " [name=selWarehouseLayoutKey]");
                
                var oldValue = select.val(); 

                if (select.prop) {
                    var options = select.prop('options');
                } else {
                    var options = select.attr('options');
                }

                $('option', select).remove();

                $.each(newOptions, function(val, text) {
                    options[options.length] = new Option(text, val);
                });

            
                var optionExists = false;
                if(oldValue){
                    if(select.find("option[value='"+oldValue+"']").length > 0){
                        optionExists = true;
                    }
                }

                if (optionExists) {
                    select.val(oldValue).change();
                } else {
                    select.find('option:eq(0)').prop('selected', true).change();
                }

            }
        });
    


    }

    this.calculateTotal = function calculateTotal()
    {

    }

    this.updateTotalQty = function updateTotalQty(obj)
    {
        var serviceRow = $(obj).closest(".transaction-detail-row");  

        var qtyPackage =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyPackage[]']").val())) || 0;
        var qtyCarton =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyCarton[]']").val())) || 0;

        var total = qtyPackage * qtyCarton;

        serviceRow.find("[name='qty[]']").val(total).blur(); 
    }

    this.updateLabel = function updateLabel(obj){
        var serviceRow = $(obj).closest(".transaction-detail-row");  
          
        // var qty =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyDetail[]']").val())) || 0;
        // var price = parseFloat(unformatCurrency(serviceRow.find("[name='priceInUnitDetail[]']").val())) || 0;
        // var discount =  unformatCurrency(serviceRow.find("[name='discountValueDetail[]']").val());
        var itemName =  serviceRow.find("[name='itemDetailName[]']").val();
        // var milimeter =  serviceRow.find("[name='mililiter[]']").val() || '';
        var milimeter =  parseFloat(unformatCurrency(serviceRow.find("[name='mililiter[]']").val())) || 0;
        var brand =  serviceRow.find("[name='brandName[]']").val();
        var itemType =  serviceRow.find("[name='detailType[]']").val();
        var qtyCarton =  serviceRow.find("[name='qtyCarton[]']").val();
        var alcohol =  serviceRow.find("[name='alcoholContent[]']").val();

        var  merk = '';
        var  size = '';
        var  sizeInfo = '';
        if (brand != '') {
            merk = ' Merk : ' + brand;
        }

        if (itemType != '') {
            itemType = ', Tipe : ' + itemType;
        }

        if (milimeter == 0) {
            size = '';
        } else {
            size = ' ' + milimeter + ' ML'
        }

        if (milimeter != 0 && qtyCarton != 0) {
            sizeInfo = ', Ukuran : '+qtyCarton+ ' X ' +milimeter;
        }

        if (alcohol == 0) {
            alcoholContent = '';
        } else {
            alcoholContent = ', Spesifikasi lain: '+ alcohol + '%'
        }

        
        var label = itemName + size + merk+itemType + sizeInfo + alcoholContent;
        serviceRow.find("[name='label[]']").val(label); 
    }

    this.cloneRowDetailValue = function cloneRowDetailValue() {
        // var detailRow = $(obj).closest('.transaction-detail-row');
        var detailRow = tabObj.find('.transaction-detail-row').last();

        var alcohol =  detailRow.find("[name='alcoholContent[]']").val();

        var hasValue = false;
        var temp     = [];   // simpan dulu

        detailRow.find('input[name], select[name], textarea[name]').each(function () {
            var $field = $(this);
            var name   = $field.attr('name');
            
            if (!name) return;

            var val = $field.val();

            if (val != "" && val != null && val != undefined && val != 0) {
                hasValue = true;
            }
        
            if (name === 'hidDetailKey[]' || name.endsWith('[hidDetailKey[]]')) {
                val = 0;   
            }

            temp.push({
                selector: name.replace(/\[\]$/, ''), 
                value   : val
            });
        });

        var arr = hasValue ? JSON.stringify(temp) : null;

        addNewTemplateRow(
            "detail-row-template",
            arr
        );

        thisObj.rebindEl();
    }
      
    this.rebindEl = function rebindEl(){   
        // bindAutoCompleteForTransactionDetail('itemDetailName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&limit=25');
        bindAutoCompleteForTransactionDetail('brandName[]',objAndValueForDetailBrandAutoComplete,'ajax-brand.php?action=searchData&limit=25');
        bindAutoCompleteForTransactionDetail('countryOfOriginId[]',objAndValueForDetailCountryAutoComplete,'ajax-country.php?action=searchData&limit=25');
        bindAutoCompleteForTransactionDetail('detailType[]',objAndValueForDetailCategoryItemAutoComplete,'ajax-item-category.php?action=searchData&limit=25');

        bindEl(tabObj.find("[name='itemDetailName[]'], [name='mililiter[]'], [name='brandName[]'], [name='detailType[]'], [name='qtyCarton[]'], [name='alcoholContent[]']"),'change', function() { thisObj.updateLabel(this); });
        bindEl(tabObj.find("[name='qtyPackage[]'], [name='qtyCarton[]']"),'change', function() { thisObj.updateTotalQty(this); });
    } 
     
    this.loadOnReady = function loadOnReady(){ 
        if(tabObj.find(".file-uploader").length > 0){
             if(id){    
                for($i=0;$i<rsFile.length;$i++)  arrFile.push(rsFile[$i].file); 
                    createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,false);  

                }else{
                    createFileUploader(fileUploaderTarget,fileFolder, "", "", false);
                }

          }
        
        tabObj.find("[name=selWarehouseKey]").change(function() { thisObj.updateWarehouseLayout(); });
        // tabObj.find("[name=selWarehouseKey]").change();

        
        thisObj.rebindEl(); 

        tabObj
            .off('click', "[name='btnAddRow']")
            .on('click', "[name='btnAddRow']", function (e) {
                thisObj.cloneRowDetailValue();
            });

    }
    
}
