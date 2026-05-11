
function CarServiceMaintenance(tabID,tablekey,varConstant,cashTOP) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);       
        this.tabID = tabID;   
        this.tablekey = tablekey;  
    
        var lang = varConstant.LANG;
        this.useStorage = varConstant.USE_STORAGE;  
        var id = tabObj.find("[name=hidId]").val();
    
    
        var fileFolder = varConstant.UPLOAD_FILE_FOLDER;
        var rsFile =varConstant.RS_ITEM_FILE;
        var fileUploaderTarget = "item-file-uploader"; 
        var arrFile = Array();  
    
        this.conversion = {};
    
        var  objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});
		objAndValue.push({object:'hidItemType[]', value :'itemtype'});
	  	objAndValue.push({object:'priceInUnit[]', value :'cogs'}); 
	  	objAndValue.push({object:'hidCOGS[]', value :'cogs'});  
	  	objAndValue.push({object:'isPackage[]', value :'ispackage'});  
		objAndValue.push({object:'selUnit[]', value :'deftransunitkey'}); 
        var objAndValueForDetailAutoComplete  = objAndValue; 
	  	  
        var  objAndValue = new Array;
		objAndValue.push({object:'hidNewSNKey[]', value :'pkey'});
		objAndValue.push({object:'newSN[]', value :'serialnumber'});
        var objAndValueForDetailSNAutoComplete  = objAndValue; 
	  	  
        this.updateDetail = function updateDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row");
            var itemKeyObj = detailRow.find("[name=\"hidItemKey[]\"]").first();
            var selUnitObj = detailRow.find("[name=\"selUnit[]\"]").first();
            var isoutsource =  tabObj.find(" [name=chkIsOutsource]").val(); 

            for(i=0;i<objAndValue.length;i++){   
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
            } 

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);  
 
            if (ui.item['needsn'] == 1) { 
                detailRow.find("[name=\"newSN[]\"]").prop("readonly", false);
                detailRow.find("[name=\"lastSN[]\"]").prop("readonly", false);
                detailRow.find("[name=\"qty[]\"]").prop("readonly", true).val(1);
                //detailRow.find("[name=\"selItemPosition[]\"]").prop("disabled", false);
            } else {
                detailRow.find("[name=\"newSN[]\"]").prop("readonly", true);
                detailRow.find("[name=\"lastSN[]\"]").prop("readonly", true);
                //detailRow.find("[name=\"selItemPosition[]\"]").prop("disabled", true);
                detailRow.find("[name=\"qty[]\"]").prop("readonly", false).val(0);
            }

            updateAvailableUnit(itemKeyObj, selUnitObj);
            
            //hanya update kalo inhouse  
            if(isoutsource == 1 || ui.item['itemtype'] == varConstant.SERVICE)   
                detailRow.find("[name=\"priceInUnit[]\"]").first().val(0);
          
    
            thisObj.updateReadonlyDetailRow(detailRow); 
            thisObj.updateConversionVariable(detailRow);
            thisObj.getItemPosition(detailRow);
            
            detailRow.find("[name=\"priceInUnit[]\"]").change(); // dibawah, agar keupdate dulu konversi unit nya
         }
        
        this.updateConversionVariable = function updateConversionVariable(detailRow){
            //alert(itemkey);
            var itemkey = detailRow.find("[name='hidItemKey[]']").val();
            
            $.ajax({
                type: "GET",
                async:false,
                url:  'ajax-item.php',
                data: "action=getAvailableConversion&itemkey=" +  itemkey ,  
            }).done(function( data ) { 
                    data = JSON.parse(data) ;   
                    thisObj.conversion[itemkey] = {};
                    for (i=0;i<data.length;i++){  
                        thisObj.conversion[itemkey][data[i].conversionunitkey] =  data[i].conversionmultiplier; 
                    } 
            }); 
        }
        
        this.updateReadonlyDetailRow = function updateReadonlyDetailRow(detailRow){
          
            var isoutsource =  tabObj.find(" [name=chkIsOutsource]").val();  
            var supplierNameObj = tabObj.find("[name='supplierName']");
            var supplierKeyObj = tabObj.find("[name='hidSupplierKey']");
             
            var itemtype = detailRow.find("[name='hidItemType[]']").val();
            var priceInUnitObj = detailRow.find("[name='priceInUnit[]']");
               
            if (isoutsource == 1){  
                setReadonly(priceInUnitObj,false);
            }else{
               supplierNameObj.val("");
               supplierKeyObj.val("");
                
               if(itemtype == varConstant.SERVICE){  
                    setReadonly(priceInUnitObj,false); 
                }else{  
                    setReadonly(priceInUnitObj,true); 
                }
            }                      
        }

         
        this.updateCarInformation =  function updateCarInformation(){  

                tabObj.find("[name=policeNumber]").val("");
                tabObj.find("[name=year]").val("");
                tabObj.find("[name=carSeriesName]").val(""); 
                tabObj.find("[name=fuelType]").val("");
            
                var carkey = tabObj.find("[name=hidCarKey]").val();  

                if(!carkey) return;

                  $.ajax({
                        type: "GET",
                        url:  'ajax-car.php',
                        async: false,
                        data: "action=searchData&pkey=" + carkey ,  
                    }).done(function( data ) {  
                            data = JSON.parse(data) ; 

                            if (data.length != 0){   
                                data = data[0];  

                                tabObj.find("[name=policeNumber]").val(data.value);
                                tabObj.find("[name=year]").val(data.year);
//                                tabObj.find("[name=carSeriesName]").val(data.seriesname);
//                                tabObj.find("[name=fuelType]").val(data.fueltype); 
                                tabObj.find("[name=hidDriverKey]").val(data.driverkey); 
                                tabObj.find("[name=driverName]").val(data.drivername); 
                            } 

                    });
                thisObj.updateItemPositionInformation();

         }  
        this.updateItemPositionInformation = function updateItemPositionInformation()
        {
            var rows = tabObj.find(".transaction-detail-row");
        
            rows.each(function () {
                thisObj.getItemPosition($(this));
            });
        }
  
        this.getItemPosition = function getItemPosition(row)
        {
            var itemObj = row.find("[name=\"hidItemKey[]\"]").first();
            var itemkey = itemObj.val();
            var carkey = tabObj.find("[name=hidCarKey]").val();
            
            var selItemPositionObj = row.find("[name=\"selItemPosition[]\"]");

            if (!carkey || !itemkey) return;
        
            $.ajax({
                type: "GET",
                url: 'ajax-item.php',
                data: 'action=getItemPositionForMaintenance&pkey=' + itemkey+'&carkey=' + carkey, 
            }).done(function (data) { 
    
                var data = parseJSON(data); 
                data.unshift({ itempositionkey: 0, positioname: lang.partsPosition });
                
                var selectOpt = data;
                
                reInsertSelectBox(selItemPositionObj, selectOpt, { "key": "itempositionkey", "label": "positioname" });
                
                if (selectOpt.length > 0)  {    
                    selItemPositionObj.prop('selectedIndex', 0); 
                }

            });

        }

        this.getItemLastSN = function getItemLastSN(obj)
        {
            var detailRow = $(obj).closest(".transaction-detail-row");

            var carkey = tabObj.find("[name=hidCarKey]").val();
            var itemkey = detailRow.find("[name=\"hidItemKey[]\"]").val();
            var positionkey = detailRow.find("[name=\"selItemPosition[]\"]").val();

            if (!carkey || !itemkey || !positionkey) return;

            // itemkey dikirim utk cari model sparepartype yg sama
            $.ajax({
                type: "GET",
                url: 'ajax-car.php',
                data: 'action=getCarItemLastSNForMaintenance&itemkey=' + itemkey+'&carkey=' + carkey+'&positionkey='+positionkey, 
            }).done(function (data) { 
                
                var data = parseJSON(data);

                if (!data || data.length <= 0) {
                    detailRow.find("[name=\"hidLastItemKey[]\"]").val("").change();
                    detailRow.find("[name=\"lastItemName[]\"]").val("").change();
                    detailRow.find("[name=\"lastSN[]\"]").val("").change();
                    return;
                }


                detailRow.find("[name=\"hidLastItemKey[]\"]").val(data[0]['itemkey']);
                detailRow.find("[name=\"lastItemName[]\"]").val(data[0]['itemname']);
                detailRow.find("[name=\"lastSN[]\"]").val(data[0]['serialnumber']);

            });

        }
        
        
        this.calculateDetail = function calculateDetail(obj){     
            var detailRows = (obj) ? $(obj).closest('.transaction-detail-row') : tabObj.find('.item-row .transaction-detail-row');
                
            var isOutsource = tabObj.find("[name=chkIsOutsource]").val();
            
             detailRows.each(function() {   
                    
                    var detailRow = $(this);
                 
                    var unitkey =  detailRow.find("[name='selUnit[]']").val();
                    var itemkey =  detailRow.find("[name='hidItemKey[]']").val();
                    if (itemkey == undefined) return;

                    // update price in unit  
                    var conversion =  ( thisObj.conversion[itemkey] ) ?  thisObj.conversion[itemkey][unitkey] : 1;  
                    var cogs = unformatCurrency(detailRow.find("[name='hidCOGS[]']").val());
                    var qty =  unformatCurrency(detailRow.find("[name='qty[]']").val()); 
                    var discount =  unformatCurrency(detailRow.find("[name='discountValueInUnit[]']").val());
                    var discountType =  unformatCurrency(detailRow.find("[name='selDiscountType[]']").val());
                    var itemType = detailRow.find("[name='hidItemType[]']").val()
                    
                    // ini hanya boleh berlaku utk item inhouse 
                    var priceInUnit = 0;
                    if(isOutsource == 1 || itemType == 3){ 
                        priceInUnit =  unformatCurrency(detailRow.find("[name='priceInUnit[]']").val());
                        //qty *= conversion;
                    }else{ 
                        //hanya diupdate kalo jenis barangnya item 
                        priceInUnit =  cogs * conversion; 
                        detailRow.find("[name='priceInUnit[]']").val(priceInUnit).blur(); 
                    }

                    if (discount != 0){
                        if (discountType == 2)
                            discount = discount/100 * priceInUnit;
                    }

                    var subtotal = qty * (priceInUnit - discount);
                    detailRow.find("[name='subtotal[]']").val(subtotal).blur(); 
                 
             })
                     
            thisObj.calculateTotal();
        }
	
        this.calculateTotal = function calculateTotal(){  

                var subtotal = 0; 
                tabObj.find(" [name='subtotal[]']").each(function() {   
                        subtotal += parseInt(unformatCurrency($(this).val())) || 0;
                })

                tabObj.find(" [name='subtotal']").val(subtotal).blur();

                var finalDiscount = parseFloat(unformatCurrency(tabObj.find(" [name='finalDiscount']").val())) || 0 ;
                var finalDiscountType = parseInt(unformatCurrency(tabObj.find(" [name='selFinalDiscountType']").val())) || 0 ;
                var etcCost = parseInt(unformatCurrency(tabObj.find(" [name='etcCost']").val())) || 0 ; 

                var includeTax =   tabObj.find(" [name='chkIncludeTax']").val();
                var taxPercentage =  parseFloat(unformatCurrency(tabObj.find(" [name='taxPercentage']").val())) || 0 ;  

                if (finalDiscount != 0){
                    if (finalDiscountType == 2)
                        finalDiscount = finalDiscount/100 * subtotal;
                }

                subtotal -= finalDiscount;

                tabObj.find(" [name='beforeTaxTotal']").val(subtotal).blur();

                var taxValue = 0;
                if (includeTax == 0) {
                        taxValue = subtotal * taxPercentage / 100;
                        subtotal += taxValue;
                }else{
                        taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                        tabObj.find(" [name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
                }

                tabObj.find(" [name='taxValue']").val(taxValue).blur(); 

                var total = subtotal + etcCost;
                tabObj.find(" [name='total']").val(total).blur(); 
            
                totalPayment = parseInt(unformatCurrency(tabObj.find("[name='totalPayment']").val()));
            
                var balance = totalPayment - total; 
                tabObj.find(" [name='balance']").val(balance).blur();

       }
        
        this.showSupplier = function showSupplier(obj){   
             if ($(obj).val() == 1){  
                 tabObj.find(" [name=supplierName]").prop('readonly', false); 
                 tabObj.find(" .external-workshop").removeClass("display-none");
                 tabObj.find(" .inhouse-workshop").addClass("display-none");  
             }else{ 
                 tabObj.find(" [name=supplierName]").prop('readonly', true); 
                 tabObj.find(" .external-workshop").addClass("display-none");
                 tabObj.find(" .inhouse-workshop").removeClass("display-none"); 
             }

            tabObj.find(" [name='hidItemType[]']").each(function(){  
                 thisObj.updateReadonlyDetailRow ($(this).closest(".div-table-row"));
            })
        }
        
        this.updateDisplay = function updateDisplay(obj){   
            var selectedType = obj.val();
            tabObj.find(".vehicle-type").hide();
            tabObj.find(".type-"+selectedType).show();

        }

        this.rebindEl = function rebindEl(){
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete,'ajax-item.php?action=searchData&itemtype=1,3&limit=25','getTabObj().updateDetail'); 
            bindEl(tabObj.find("[name='qty[]'], [name='priceInUnit[]'], [name='discountValueInUnit[]'],  [name='selUnit[]']" ), 'change',  function(){ thisObj.calculateDetail(this) }); 
            bindEl(tabObj.find("[name='selDiscountType[]']"),'change',function(){ updateDecimal(this); thisObj.calculateDetail(this) });    
            bindEl(tabObj.find("[name='selItemPosition[]']"),'change',function(){ thisObj.getItemLastSN(this) });
        }
        
        this.onChangePaymentMethodHandler = function onChangePaymentMethodHandler(){
            thisObj.calculateTotal();
            //bindEl(tabObj.find("[name='paymentMethodValue[]']"),'change',function(){ thisObj.calculateTotal() });   
        }
         
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }
      this.saveExecuteDate = function saveExecuteDate(){
            var pkey = tabObj.find("[name=hidId]").val();
            var executeDate = tabObj.find("[name=executeDate]").val();
            
            $.ajax({
                        type: "POST",
                        url:  'ajax-car-service-maintenance.php', 
                        async : false,
                        data: 'action=updateExecuteDate&pkey=' + pkey +'&executeDate=' + executeDate, 
                        success: function(data){    
                            data = parseJSON(data);
                            if(data[0].valid == false){
								alert(data[0].message);
							}else{
								alert('Data tidak berhasil dirubah');
							}
                        }  
                    }); 
        }
    
        this.updateDate = function updateDate(obj){
            tabObj.find("[name=executeDate]").val(obj.val());
            tabObj.find("[name=estDate]").val(obj.val());
        }
    
        this.updateMileage = function updateMileage(obj){
            
                var executeDate = tabObj.find("[name=executeDate]").val();
                var carkey = tabObj.find("[name=hidCarKey]").val();
            
               $.ajax({
                        type: "POST",
                        url:  'ajax-car.php', 
                        async : false,
                        data: 'action=getMileage&trDate=' + executeDate +'&carkey=' + carkey, 
                        success: function(data){    
                            data = parseJSON(data);
                            var mileage = 0;
                            if(data.length != 0){ 
                                mileage = data;
                                //registrationNumber = standardizeRegistrationNumber(registrationNumber); 
                                //var mileage = (data[registrationNumber][0]['mileage']) ?? 0;
                            }
                        
                            // kalo milage kosogn (mungkin bl mpernah ad history atau dari gps yg tdk support) user boleh edit sendiri
                           
                            var readonlyMileage =  (mileage <= 0) ? false : true;
                            tabObj.find("[name=mileage]").attr("readonly",readonlyMileage);
                            
                            tabObj.find("[name=mileage]").val(mileage).blur();
                        }  
                    }); 
        }
        
        this.loadOnReady = function loadOnReady(){ 
        		
            if(thisObj.useStorage){ 
                
            }else{ 
                if(id){   
                    for($i=0;$i<rsFile.length;$i++) 
                        arrFile.push(rsFile[$i].file); 

                    createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,true); 
                }else{ 
                    createFileUploader(fileUploaderTarget,fileFolder, "" , "",true); 
                }
            }
            
            tabObj.find("[name=selTermOfPaymentKey]" ).change(function() {
                for(i=0;i<cashTOP.length;i++){ 
                    if ($(this).val() == cashTOP[i]){  
                        tabObj.find("[name='paymentMethodValue[]']").each(function() { $(this).closest('.transaction-detail-row').find('.remove-button').click();  }) 
                        tabObj.find(".cashTOP").hide();
                        return;
                    }
                } 	

               tabObj.find(".cashTOP").show();
            });   
            tabObj.find("[name=executeBtn]").on('click', function() { thisObj.saveExecuteDate(); }); 
            tabObj.find("[name=selTermOfPaymentKey]" ).change();  
            
            tabObj.find(".form-detail-field").toggle();  
            tabObj.find(".form-detail-button").click(function() {  

                tabObj.find(".form-detail-field").toggle( "highlight" );
                var temp = tabObj.find(".form-detail-button").attr("relalt");   
                tabObj.find(".form-detail-button").attr("relalt",tabObj.find(".form-detail-button").text());
                tabObj.find(".form-detail-button").text(temp);

            }); 
            
             
            tabObj.find("[name=chkIsOutsource]" ).change(function(){thisObj.showSupplier(this); thisObj.calculateDetail();}) 
            tabObj.find("[name=selFinalDiscountType], [name=finalDiscount], [name=beforeTaxTotal], [name=chkIncludeTax], [name=etcCost], [name=taxPercentage]" ).change(function(){thisObj.calculateTotal(this)}) 
            tabObj.find("[name=selFinalDiscountType]").change(function(){updateFinalDiscountDecimal(this)}) 
 		    tabObj.find("[name=selType]").change(function(){thisObj.updateDisplay($(this))}) 
            tabObj.find("[name=trDate]").change(function(){thisObj.updateDate($(this));}) 
            
            if(varConstant.USE_GPS_MILEAGE == 1)
                tabObj.find("[name=policeNumber], [name=executeDate]").change(function(){thisObj.updateMileage(); }) 
                    
            thisObj.rebindEl(); 
             
        }
        
}
