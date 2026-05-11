function Item(tabID, opt, cons) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID;   
        this.useStorage = cons.USE_STORAGE;  
            
        var folder = opt.uploadImageFolder;
        var imageUploaderTarget = "item-image-uploader"; 
        var arrImage =  (opt.arrImage) ? opt.arrImage : Array();  
        var arrPHPThumbHash =  (opt.arrPHPThumbHash) ? opt.arrPHPThumbHash : Array();  
    
        var fileFolder = opt.uploadFileFolder;
        var fileUploaderTarget = "item-file-uploader"; 
        var arrFile =  (opt.arrFile) ? opt.arrFile : Array();  
    
        var id = tabObj.find("[name=hidId]").val();
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidItemPackageKey[]', value :'pkey'});    
        var objAndValueForDetailAutoComplete = objAndValue;
    
		var objAndValue = new Array;
		objAndValue.push({object:'hidItemSpecificationKey[]', value :'pkey'});    
        var objAndValueForSpecificationDetailAutoComplete = objAndValue;
        var mpVariant = {};
         

		var objAndValue = new Array;
		objAndValue.push({object:'hidModelKey[]', value :'pkey'});    
        var objAndValueForDetailModelAutoComplete = objAndValue;

		var objAndValue = new Array;
		objAndValue.push({object:'hidTextureKey[]', value :'pkey'});    
        var objAndValueForDetailTextureAutoComplete = objAndValue;

		var objAndValue = new Array;
		objAndValue.push({object:'hidColorKey[]', value :'pkey'});    
        var objAndValueForDetailColorAutoComplete = objAndValue;

		var objAndValue = new Array;
		objAndValue.push({object:'hidCharacterKey[]', value :'pkey'});    
        var objAndValueForDetailCharacterAutoComplete = objAndValue;

		var objAndValue = new Array;
		objAndValue.push({object:'hidVariationKey[]', value :'pkey'});    
        var objAndValueForDetailVariationAutoComplete = objAndValue;
    
        this.updateMarketplaceAttributes = function updateMarketplaceAttributes(){ 
              if(!opt.hasActiveMarketplace) return; 
           
              // update attribute
              $.ajax({
                    type: "GET",
                    url:  'ajax-marketplace.php',
                    async : false,
                    beforeSend:function (xhr){ 
                        tabObj.find(".attributes-row").not(".row-template").remove(); 
                    },
                    data: "action=getMarketplaceCategoryAttributes&categorykey=" +  tabObj.find("[name=hidCategoryKey]").val(),  
                    success: function(data){  
                            if(!data) return;
                        
                            var data = JSON.parse(data);  
                            var totalData =  data.length;
                            for(i=0;i<totalData;i++){   
                                
                               var marketplacekey = data[i]['marketplacekey'];
                               var attributes = data[i]['attributes'];
                               var classPanel = "#" + thisObj.tabID +' .attributes-'+marketplacekey;    
                                  
                               var totalAttributes = attributes.length -1;
                               for(var j=totalAttributes;j>=0;j--){   
                                    var arrPostValue = [];   
                                   
                                    arrPostValue.push({"selector":"hidAttributeDetailKey", "value":0}); 
                                    arrPostValue.push({"selector":"hidMarketplaceKey", "value":data[i].marketplacekey}); 
                                    arrPostValue.push({"selector":"hidCategoryAttributeName", "value":attributes[j].name});  
                                    arrPostValue.push({"selector":"hidCategoryAttributeKey", "value":attributes[j].attributekey});   
                                    arrPostValue.push({"selector":"hidAttributeLabel", "value":attributes[j].label});
                                   
                                    var desc = (attributes[j].description) ? '<div class="tag">'+attributes[j].description+'</div>' : '';
                                    
                                    $newRow = addNewTemplateRow("category-attribute-row-template",JSON.stringify(arrPostValue),null,null, classPanel); 
                                    if(attributes[j].class) $newRow.addClass(attributes[j].class);
                                        
                                        
                                   $newRow.find(".attribute-label").html(attributes[j]['label']);
                                   $newRow.find(".attribute-value").html(attributes[j]['input']+desc); 
                               }
                             }  

                    } ,
                    error: function(xhr, errDesc, exception) {
                      

                    }
                });
           
                thisObj.updateVariant();
            
        }
        
   this.updateItemParent = function updateItemParent(){ 
			if(!opt.usevariant) return; 
			var parentkey = tabObj.find("[name=hidParentItemKey]").val();

			$.ajax({
				type: "GET",
				url:  'ajax-item.php',
				async: false,
				data: "action=getDataRowById&pkey=" + parentkey ,  
			}).done(function( data ) { 

				if(!data) return;

				data = JSON.parse(data) ; 

				if(data.length == 0){ 
					alert(phpErrorMsg[213])
					return;
				}

				data = data[0];

				tabObj.find("[name=hidCategoryKey]").val(data.categorykey); 
				tabObj.find("[name=categoryName]").val( decodeHTMLEntities(data.categoryname)); 
				tabObj.find("[name=hidBrandKey]").val(data.brandkey); 
				tabObj.find("[name=brandName]").val(decodeHTMLEntities(data.brandname)); 
				tabObj.find("[name=gramasi]").val(data.gramasi).blur(); 

				thisObj.updateVariant();
			});  
        }

       this.updateVariant =  function updateVariant(onLoad){  
           if(!opt.usevariant) return; 
            
           var parentkey = tabObj.find("[name=hidParentItemKey]" ).val();
           
           // update variant
              $.ajax({
                    type: "GET",
                    url:  'ajax-marketplace.php',
                    async : false,
                    beforeSend:function (xhr){ 
                        // hapus dulu semua
                        // nanti saja dulu, harusnya setiap rs kalo empty jg dikirim kosong
                        /*var select = tabObj.find("['name=selVariant"+marketplaceKey+"[]']");
                        var options = (select.prop) ? select.prop('options') : select.attr('options'); 
                        $('option', select).remove(); */
                    },
                    data: "action=getMarketplaceCategoryVariant&parentkey="+parentkey+"&categorykey=" +  tabObj.find("[name=hidCategoryKey]").val(),  
                    success: function(data){  

                            data = parseJSON(data); 
                            if(data.length == 0)return;
                        
                            
                            //mpVariant = {};
                        
                            $.each(data, function(key, item) {
                                
                                var marketplaceKey = key; 
                                if(!data[marketplaceKey]) return; // kalo gk ad, ajaxnya balikin null soalnya
								
                                var selectOpt = data[marketplaceKey]; 
                                
								var selectVariantBox = tabObj.find("[name=\"selVariant"+marketplaceKey+"[]\"]");
								
                                // setiap marketplace bisa berbeda,
								// tokopedia didefine variasi apa saja yg memungkinkan
								// shopee bebas
                                if(marketplaceKey == cons.MARKETPLACE['tokopedia']){  
                                    // update variant yg bisa dipilih
									/*  mpVariant[marketplaceKey] = Array();
                                    for(var i=0;i<item.length;i++)
                                        mpVariant[marketplaceKey][item[i]['key']] = item[i]['arrVariant'];*/

									// utk tokopeoda, ini masih ngebug ketika ad pertama kali, gk keluar
                                    if(!onLoad){ 
                                        reInsertSelectBox(selectVariantBox,selectOpt, {"key" : "key", "label" : "label"} ); 

                                        // kalo jenisnya variant, buat readonly variantny, dan harus ikutin parent
                                        thisObj.updateVariantReadonly();
                                    }
                                } else if(marketplaceKey == cons.MARKETPLACE['shopee']){   
									selectVariantBox.val(selectOpt[0]['variantkey']);
								}
									
             
                            }); 
                    } ,
                    error: function(xhr, errDesc, exception) {
                      

                    }
                });
       }
       
       this.updateVariantReadonly = function updateVariantReadonly(){ 
		    var checkedVvalue = tabObj.find("[name=chkIsVariant]" ).val();
            var readonly = (checkedVvalue == 1) ? true : false;
                                        
            var selectVariantBox = tabObj.find(".select-variant"); // pake class aj biar lebih mudah
            updateComboboxReadonly(selectVariantBox,readonly);  
		   
		   	if(checkedVvalue == 0){
				tabObj.find("[name=parentItemName]" ).val("")
				tabObj.find("[name=hidParentItemKey]" ).val("");
				
			}
		    
		   tabObj.find(".txt-variant" ).attr("readonly",readonly);
		   tabObj.find("[name=parentItemName]" ).attr("readonly",!readonly);
		   
       }

       
       this.importContentOfPackage =  function importContentOfPackage(){  
                
            if (!tabObj.find("[name=hidContentOfPackageKey]").val()) return;
             
            loadOverlayScreen({content: _LOADING_TEMPLATE_});
            thisObj.activeAjaxConnections = 0;
             
            $.ajax({
                type: "GET",
                url:  'ajax-item-checklist-group.php',
                async : false,
                beforeSend:function (xhr){
                    clearAllRows(tabObj.find(".package-list"));
                    thisObj.activeAjaxConnections++; 
                },
                data: "action=getDetailById&pkey=" +  tabObj.find("[name=hidContentOfPackageKey]" ).val(),  
                success: function(data){ 
                        var data = JSON.parse(data);  
                        var i; 

                        for(i=0;i<data.length;i++){   

                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidItemPackageKey", "value":data[i].itemkey});
                            arrPostValue.push({"selector":"itemPackageName", "value":data[i].itemname}); 
                            arrPostValue.push({"selector":"qty", "value":data[i].qty});  

                            $newRow = addNewTemplateRow("detail-content-of-package-row-template",JSON.stringify(arrPostValue)); 
                            $newRow.find(".baseitemunit").html(data[i].baseunitname);  

                        } 

                        tabObj.find(".inputnumber").change().blur();
                         
                        thisObj.rebindEl();
                        decreaseActiveAjaxConnections(thisObj); 
                    
                } ,
                error: function(xhr, errDesc, exception) {
                    decreaseActiveAjaxConnections(thisObj); 
                }
            }); 
        
       }
       
       this.importMarketplaceAttributes = function importMarketplaceAttributes(){
            $.ajax({
                type: "GET",
                url:  'ajax-item.php',
                async : false,
                beforeSend:function (xhr){
                    clearAllRows(tabObj.find(".package-list"));
                    thisObj.activeAjaxConnections++; 
                },
                data: "action=getMarketplaceCategoryAttributes&pkey=" +  tabObj.find("[name=hidItemImportKey]" ).val(),  
                success: function(data){ 
                     
                        if(!data) return;
                    
                        var data = JSON.parse(data);   
                        var marketplacekey, attributekey,attributeValue;
                     
                        tabObj.find("[name=\"attributeValue[]\"]").each(function(){  
                            marketplacekey = $(this).closest(".attributes-row").find("[name=\"hidMarketplaceKey[]\"]").val();
                            attributekey = $(this).closest(".attributes-row").find("[name=\"hidCategoryAttributeKey[]\"]").val(); 
                             
                            if(data[marketplacekey] && data[marketplacekey][attributekey])
                                $(this).val(data[marketplacekey][attributekey]);
                            
                        });


                        tabObj.find(".inputnumber").change().blur();
                         
                        thisObj.rebindEl();
                        decreaseActiveAjaxConnections(thisObj); 
                    
                } ,
                error: function(xhr, errDesc, exception) {
                    decreaseActiveAjaxConnections(thisObj); 
                }
            }); 
       }
 
       this.updateCommissionDecimal = function updateCommissionDecimal(obj){  
            var parentObj =  $(obj).parent().parent();

            var objDiscVal = parentObj.find("[name='commissionValue']");
            var discType = $(obj).val();

            objDiscVal.removeClass("inputnumber").addClass("inputdecimal");

            if (discType == 1){
                objDiscVal.unbind("blur").bind( "blur", function(event) {  inputNumberOnBlur($(this)) });
            }else{
                objDiscVal.unbind("blur").bind( "blur", function(event) { inputNumberOnBlur($(this),2)}); 
            } 

           objDiscVal.blur(); 
 
        }
 
        this.useVariant = function useVariant(obj){ 
			var isShowVariant = $(obj).val();
            var categoryNameObj = tabObj.find("[name=categoryName]");
            var brandNameObj = tabObj.find("[name=brandName]");
            
			if(isShowVariant==1){
				tabObj.find(".isvariant, .variant-option-col").show(); 
				categoryNameObj.prop("readonly",true);  
                categoryNameObj.closest("div").find(".add-button").hide();
				brandNameObj.prop("readonly",true);  
                brandNameObj.closest("div").find(".add-button").hide();
			}else{
				tabObj.find(".isvariant, .variant-option-col").hide();
				categoryNameObj.prop("readonly",false);  
                categoryNameObj.closest("div").find(".add-button").show();
				brandNameObj.prop("readonly",false);  
                brandNameObj.closest("div").find(".add-button").show();
			}
            
            thisObj.updateVariantReadonly();
			
        }
		
		this.updateChkSync = function updateChkSync(){  
            // test semua pake satu relignore saja  
            var chkAllObj = tabObj.find("[name=chkSyncAllMarketplace]");
            if(chkAllObj.attr("relignore")) return; 
               
            chkAllObj.attr("relignore",true); 
            tabObj.find("[name=\"chkSyncToMarketplace[]\"]").val(chkAllObj.val()).change();
            chkAllObj.removeAttr("relignore");
        }
        
        this.updateChkSyncToAll = function updateChkSyncToAll(){ 
            // test semua pake satu relignore saja  
            var chkAllObj = tabObj.find("[name=chkSyncAllMarketplace]");
            if(chkAllObj.attr("relignore")) return; 
            
            
            chkAllObj.attr("relignore",true);
            var notChecked = tabObj.find("[name=\"chkSyncToMarketplace[]\"][value=0]"); 
            tabObj.find("[name=chkSyncAllMarketplace]").val( (notChecked.length > 0) ? 0 : 1 ).change();
            chkAllObj.removeAttr("relignore");
        }
        
        this.rebindEl = function rebindEl(){ 
            bindAutoCompleteForTransactionDetail('itemPackageName[]',objAndValueForDetailAutoComplete,'ajax-item-checklist.php?action=searchData'); 
            bindAutoCompleteForTransactionDetail('itemSpecificationName[]',objAndValueForSpecificationDetailAutoComplete,'ajax-item-specification.php?action=searchData'); 
            bindAutoCompleteForTransactionDetail('modelName[]', objAndValueForDetailModelAutoComplete, 'ajax-model.php?action=searchData&searchField=code,name');  
            bindAutoCompleteForTransactionDetail('textureName[]', objAndValueForDetailTextureAutoComplete, 'ajax-texture.php?action=searchData&searchField=code,name');  
            bindAutoCompleteForTransactionDetail('colorName[]', objAndValueForDetailColorAutoComplete, 'ajax-color.php?action=searchData&searchField=code,name');  
            bindAutoCompleteForTransactionDetail('characterName[]', objAndValueForDetailCharacterAutoComplete, 'ajax-character.php?action=searchData&searchField=code,name');  
            bindAutoCompleteForTransactionDetail('variationName[]', objAndValueForDetailVariationAutoComplete, 'ajax-item-variation.php?action=searchData&searchField=code,name');  

            bindEl(tabObj.find("[name=chkSyncAllMarketplace]"),'change',function(){thisObj.updateChkSync()}); 
            bindEl(tabObj.find("[name=\"chkSyncToMarketplace[]\"]"),'change',function(){thisObj.updateChkSyncToAll()}); 
        }
        
        this.updateMPBrandAttribute = function updateMPBrandAttribute(obj){ 
           // v2 sudah gk kepake
//             if(!opt.hasActiveMarketplace) return; 
//            
//              $.ajax({
//                    type: "GET",
//                    url:  'ajax-brand.php',
//                    async : false,
//                    beforeSend:function (xhr){ },
//                    data: "action=getBrandUsedForShopee&categorykey=" +  tabObj.find("[name=hidCategoryKey]").val() + "&brandkey=" + tabObj.find("[name=hidBrandKey]").val(),  
//                    success: function(data){    
//                            if(!data) return; 
//                            var data = JSON.parse(data);    
//                            if(data.length == 0) return;
//                        
//                            var brandname = (data[0]['marketplacebrandname'] != undefined) ? data[0]['marketplacebrandname'] : '';
//                            tabObj.find("[attr-label=brand]").val(brandname);
//                    } ,
//                    error: function(xhr, errDesc, exception) {
//                       
//                    }
//                });
             
        }
        
        this.updateJewelryAutoPrice = function updateJewelryAutoPrice(){
            var schemekey = tabObj.find("[name=selCaratPricing]").val(); 
            if (schemekey == 0){
                tabObj.find(".additional-price").hide();
                tabObj.find("[name=sellingPrice]").prop('readonly', false);
            }else{ 
                tabObj.find(".additional-price").show();
                tabObj.find("[name=sellingPrice]").prop('readonly', true);
            }
        }
        
        this.updateVariantOpt = function updateVariantOpt(obj){
            if(!opt.usevariant) return;
            
            var marketplaceKey = obj.attr("attr-marketplacekey");
            var variantUnitId = obj.val();
             
            // khusus tokopedia
            if(marketplaceKey == cons.MARKETPLACE['tokopedia']){  
                var variantObj = obj.closest(".variant-row").find("[name=\"selOption"+marketplaceKey+"[]\"]"); 
                if(variantUnitId == null) return; 
                var selectOpt = mpVariant[marketplaceKey][variantUnitId]; 
                reInsertSelectBox(variantObj,selectOpt);   
            }
        }

// buat jewelry
   this.getDataForDuplicated = function getDataForDuplicated(pkey)
        {
            
            $.ajax({
                type: "GET",
                async: false,
                url:  'ajax-item.php', 
                data: "action=getItemDataForDuplicate&pkey=" + pkey  
            }).done(function( data ) {
                
                data = parseJSON(data); 
                if(data.length == 0)return; 
                data = data[0];
				

                tabObj.find("[name=code]").val(data.code);
                tabObj.find("[name=barcode]").val(data.barcode);
                tabObj.find("[name=name]").val(data.name);
                tabObj.find("[name=aliasName]").val(data.aliasname);
                tabObj.find("[name=metaTitle]").val(data.metatitle);

                tabObj.find("[name=selPricingCategory]").val(data.pricingcategorykey);
                tabObj.find("[name=selPricingCategory]").change();
                
                tabObj.find("[name=additionalPrice]").val(data.additionalprice).blur();
                tabObj.find("[name=sellingPrice]").val(data.sellingprice).blur();
                tabObj.find("[name=cogs]").val(data.cogs).blur();
                
                tabObj.find("[name=shortdescription]").val(data.shortdescription);
                tabObj.find("[name=metaDescription]").val(data.metadescription);
              
                tabObj.find("[name=tag]").val(data.tag);
                tabObj.find("[name=orderList]").val(data.oderlist);
                
                tabObj.find("[name=chkIsPublish]").val(data.publish).change();
                tabObj.find("[name=chkNeedSN]").val(data.needsn).change();

                tabObj.find("[name=marginPercentage]").val(data.marginpercentage).blur();

                tabObj.find("[name=selBaseUnitKey]").val(data.baseunitkey);
                tabObj.find("[name=selBaseUnitKey]").change();

                tabObj.find("[name=selDefaultTransUnitKey]").val(data.deftransunitkey);
                tabObj.find("[name=selDefaultTransUnitKey]").change();

                tabObj.find("[name=chkIsVariant]").val(data.isvariant).change();

                if (data.isvarian == 1) {
                    tabObj.find("name=hidParentItemKey").val(data.parentkey);
                    tabObj.find("[name=chkIsPrimary").val(data.isprimary).change();
                }

                tabObj.find("[name=hidBrandKey]").val(data.brandkey);
                tabObj.find("[name=brandName]").val(data.brandname);

                tabObj.find("[name=hidCategoryKey]").val(data.categorykey);
                tabObj.find("[name=categoryName]").val(data.categoryname);

                tabObj.find("[name=hidMaterialKey]").val(data.materialkey);
                tabObj.find("[name=materialName]").val(data.materialname);

                tabObj.find("[name=hidPlatingKey]").val(data.platingkey);
                tabObj.find("[name=platingName]").val(data.platingname);

                var sku_alias = data.sku_alias_detail;
                var video = data.video_detail;
                var model = data.model_detail;
                var texture = data.texture_detail;
                var color = data.color_detail;
                var character = data.character_detail;
                var variation = data.item_variation_detail;

                if(sku_alias.length > 0) {
                    clearAllRows(tabObj.find(".mnv-sku-alias"));
                    var i;
                    for(i=0;i<sku_alias.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"skuAlias", "value":sku_alias[i].skualias}); 
                        addNewTemplateRow("sku-alias-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                    }
                }
                

                if(video.length > 0) {
                    clearAllRows(tabObj.find(".mnv-transaction"));
                    var i;
                    for(i=0;i<video.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"url", "value":video[i].url}); 
                        addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);   
                    }
                }

                if(model.length > 0) {
                    clearAllRows(tabObj.find(".mnv-item-model"));
                    var i;
                    for(i=0;i<model.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"hidModelKey", "value":model[i].modelkey}); 
                        arrPostValue.push({"selector":"modelName", "value":model[i].modelname}); 
                        addNewTemplateRow("item-model-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                    }
                } 

                if (texture.length > 0) {
                    clearAllRows(tabObj.find(".mnv-item-texture"));
                    var i;
                    for(i=0;i<texture.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"hidTextureKey", "value":texture[i].texturekey}); 
                        arrPostValue.push({"selector":"textureName", "value":texture[i].texturename}); 
                        addNewTemplateRow("item-texture-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                    }
                }

                if(color.length > 0) {
                    clearAllRows(tabObj.find(".mnv-item-color"));
                    var i;
                    for(i=0;i<color.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"hidColorKey", "value":color[i].colorkey}); 
                        arrPostValue.push({"selector":"colorName", "value":color[i].colorname}); 
                        addNewTemplateRow("item-color-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                    }
                } 

                if(character.length > 0) {
                    clearAllRows(tabObj.find(".mnv-item-character"));
                    var i;
                    for(i=0;i<character.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"hidCharacterKey", "value":character[i].characterkey}); 
                        arrPostValue.push({"selector":"characterName", "value":character[i].charactername}); 
                        addNewTemplateRow("item-character-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                    }
                }


                if(variation.length > 0) {
                    clearAllRows(tabObj.find(".mnv-item-variation"));
                    var i;
                    for(i=0;i<variation.length;i++){  
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"hidVariationKey", "value":variation[i].variationkey}); 
                        arrPostValue.push({"selector":"variationName", "value":variation[i].variationname}); 
                        addNewTemplateRow("item-variation-row-template",JSON.stringify(arrPostValue),'',thisObj.rebindEl);  
                    }
                }
                tabObj.find("[name=hidRingSizeKey]").val(data.ringsizekey);
                tabObj.find("[name=ringSizeName]").val(data.ringsizename);

                tabObj.find("[name=size]").val(data.size).blur();
                tabObj.find("[name=selSizeUnitKey]").val(data.sizeunitkey);
                tabObj.find("[name=selSizeUnitKey]").change();

                tabObj.find("[name=length]").val(data.length).blur();
                tabObj.find("[name=width]").val(data.width).blur();
                tabObj.find("[name=height]").val(data.height).blur();

                tabObj.find("[name=carat]").val(data.carat).blur();
                tabObj.find("[name=gramasi]").val(data.gramasi).blur();

                tabObj.find("[name=selWeightUnit]").val(data.weightunitkey);
                tabObj.find("[name=selWeightUnit]").change();

            });
        }
            
        this.loadOnReady = function loadOnReady(){ 
            
            if(opt.showItemImage){
                 
                if(thisObj.useStorage){ 

                }else{ 

                    if(id){     
                        createImageUploader({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget},{"folder":opt.uploadImageFolder, "token": id, "arrImage":arrImage,"phpThumbHash":opt.arrPHPThumbHash},true,true); 
                        createFileUploader(opt.fileUploaderTarget,opt.uploadFileFolder, id , arrFile,true);  
                    }else{ 
                        createImageUploader({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget},{"folder":opt.uploadImageFolder},true,true);
                        createFileUploader(opt.fileUploaderTarget,opt.uploadFileFolder, "" , "",true); 
                    }

                    tabObj.find(".image-list ").sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemImageArray({"tabID":thisObj.tabID, "name":opt.imageUploaderTarget}); }});
                    tabObj.find(".image-list"  ).disableSelection();

                    tabObj.find(".file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(opt.fileUploaderTarget); }});
                    tabObj.find(".file-list"  ).disableSelection(); 
                }
            }    

			if(opt.usevariant){
 				tabObj.find("[name=chkIsVariant]" ).change(function(){thisObj.useVariant(this)}) ;
				tabObj.find(" [name=chkIsVariant]" ).change();  
			} 

             if(opt.showItemDescription) tabObj.find("[name=btnAddDescription]").on('click', function() { addNewTemplateRow("item-description-row-template"); });  

             tabObj.find("[name=selCommissionType]").on('click', function() { thisObj.updateCommissionDecimal(this); });
             tabObj.find("[name=btnImportMarketplaceAttribute]").on('click', function() { thisObj.importMarketplaceAttributes(); });
 
            
             tabObj.find("[name=btnAddUnitConversion]").on('click', function() { addNewTemplateRow("unit-conversion-row-template"); });
             tabObj.find("[name=btnAddPartNumberRows]").on('click', function() { addNewTemplateRow("vendor-part-number-row-template"); });
             tabObj.find("[name=btnAddPackageRows]").on('click', function() { 
                    addNewTemplateRow("detail-content-of-package-row-template");    
                    thisObj.rebindEl(); 
             });

			tabObj.find("[name=btnAddSpecificationRows]").on('click', function() { 
                    addNewTemplateRow("detail-item-specification-row-template");    
                    thisObj.rebindEl(); 
             }); 
            //tabObj.find(".multi-selectbox").searchableOptionList({  maxHeight: '250px',  showSelectAll: true, showSelectionBelowList: true  }); 

             tabObj.find("[name=selBaseUnitKey]").change(function() {    
                tabObj.find(".baseitemunit").html($(this).find('option:selected').text());
             });
             tabObj.find("[name=selBaseUnitKey]").change();

            tabObj.find(".section-panel .title" ).click(function() {   
                   $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
            });


            if(opt.showItemDescription && opt.rsItemDescription.length == 0) 
                 addNewTemplateRow("item-description-row-template");

            if(opt.showMultiUnit && opt.rsItemUnitConversion.length == 0) 
                 addNewTemplateRow("unit-conversion-row-template"); 
            
			if(opt.showContentOfPackage && opt.rsItemPackageOfContentDetail.length == 0)  
                 addNewTemplateRow("detail-content-of-package-row-template");	 		
			
			if(opt.showItemSpecification && opt.rsItemSpecification.length == 0) 
                 addNewTemplateRow("detail-item-specification-row-template");			
			
			if(opt.showTimeUnit && opt.rsTimeConversion.length == 0) 
                 addNewTemplateRow("timeconversion-row-template"); 

            
            if (opt.rsItemModelDetail && opt.rsItemModelDetail.length == 0)
                addNewTemplateRow("item-model-row-template",null,null,thisObj.rebindEl);

            if (opt.rsItemTextureDetail && opt.rsItemTextureDetail.length == 0)
                addNewTemplateRow("item-texture-row-template",null,null,thisObj.rebindEl);

            if (opt.rsItemSKUAliasDetail && opt.rsItemSKUAliasDetail.length == 0)
                addNewTemplateRow("sku-alias-row-template",null,null,thisObj.rebindEl);

            if (opt.rsItemColorDetail && opt.rsItemColorDetail.length == 0)
                addNewTemplateRow("item-color-row-template",null,null,thisObj.rebindEl);
           
            if (opt.rsItemCharacterDetail && opt.rsItemCharacterDetail.length == 0)
                addNewTemplateRow("item-character-row-template",null,null,thisObj.rebindEl);
            
            if (opt.rsItemVariationDetail && opt.rsItemVariationDetail.length == 0)
                addNewTemplateRow("item-variation-row-template",null,null,thisObj.rebindEl);
            
            thisObj.updateVariant(true);
            
            tabObj.find("[name=\"chkSyncToMarketplace[]\"]").each(function(){   
                var marketplacekey = $(this).closest("div").find("[name=\"hidSyncMarketplaceKey[]\"]").val();
                tabObj.find("[name=\"selVariant"+marketplacekey+"[]\"]").on('change', function() { thisObj.updateVariantOpt($(this)); }); 
                
                // gk boleh kaynya, kalo direset opt nya, kal oload form jadinya gk kepilih kayanya
                //tabObj.find("[name=\"selVariant"+marketplacekey+"[]\"]").change();
            });

            
            tabObj.find("[name=selCaratPricing]").change(function() {   
                thisObj.updateJewelryAutoPrice();
            });
            tabObj.find("[name=selCaratPricing]").change();
            
/*            if(opt.showVendorPartNumber && opt.rsVendorPartNumber.length == 0) 
                 addNewTemplateRow("vendor-part-number-row-template");*/
            
            
    /*            
            <?php if ($showContentOfPackage) {  ?>
                <?php if (!isset($rsItemPackageOfContentDetail) || empty($rsItemPackageOfContentDetail)) { ?> 
                    addNewTemplateRow("detail-content-of-package-row-template");
                <?php } ?> 
 

            <?php if ($showItemFilter) { ?> 
             $("#" + tabID + " .item-filter li input").click(function() {  
                  if ($(this).prop("checked")){  
                      $(this).closest("label").addClass("bg-green-avocado text-white");
                  }
                  else{ 
                     $(this).closest("label").removeClass("bg-green-avocado text-white");
                  } 
             });
            <?php } ?>*/
              
            multiLang(tabObj); 
            thisObj.rebindEl(); 
        }
}
