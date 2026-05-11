function TruckingCostCashOut(tabID, varConstant, uploadFolder, rsFile, autoDeductAR){    
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	var objAndValue = new Array; 
		objAndValue.push({object:'hidCOAKey[]', value :'pkey'}); 
        objAndValueForDetailAutoComplete = objAndValue;
         
        this.tabID = tabID;    
        this.useStorage = varConstant.USE_STORAGE;  
    
        var fileFolder = uploadFolder;
        var fileUploaderTarget = "item-file-uploader"; 
        var arrFile = Array();  
    
        var id = tabObj.find("[name=hidId]").val();
    
        this.calculateTotal = function calculateTotal(){ 
            var amount = 0;   

            tabObj.find("[name='amount[]']").each(function(){  amount += parseInt(unformatCurrency($(this).val())) || 0;  })      
            tabObj.find("[name='subtotal']").val(amount).blur();   
            
            var aremployee = parseInt(unformatCurrency(tabObj.find("[name='arEmployee']").val())) || 0;
            var total = amount - aremployee;
            tabObj.find("[name='total']").val(total).blur();   
         
        }
        
        this.updateEmployeeInformation = function updateEmployeeInformation(){
            var employeeKey = tabObj.find("[name=hidEmployeeKey]").val(); 
            
             $.ajax({
                type: "GET",
                url:  'ajax-employee.php', 
                data: "action=getDataRowById&pkey=" + employeeKey,  
                beforeSend:function (xhr){ 
                     tabObj.find("[name=recipientMobile]").val("");
                     tabObj.find("[name=recipientBankName]").val("");
                     tabObj.find("[name=recipientBankAccountName]").val("");
                     tabObj.find("[name=recipientBankAccountNumber]").val("");
                     tabObj.find(".ar-employee").html("0");
                },
                success: function(data){  
                        if(data){
                            var data = JSON.parse(data);    
                            data = data[0];
                            
                            if (!data) return; 
                            
                            if (data.mobile)  tabObj.find("[name=recipientMobile]").val(data.mobile); 
                            if (data.bankname) tabObj.find("[name=recipientBankName]").val(data.bankname);
                            if (data.bankaccountname) tabObj.find("[name=recipientBankAccountName]").val(data.bankaccountname);
                            if (data.bankaccountnumber) tabObj.find("[name=recipientBankAccountNumber]").val(data.bankaccountnumber);
                            
                            //tabObj.find(".ar-employee").html(data.aroutstanding).formatCurrency({roundToDecimalPlace: 0 });
                            
                            var arOutstanding = (autoDeductAR ==1) ? data.aroutstanding : 0; 
                            tabObj.find("[name=arEmployee]").val(arOutstanding).blur();
                            
                        }
                }  
            }); 

        }
        
        this.updateEmployeeAROutstanding = function updateEmployeeAROutstanding(){ 
              var employeeKey = tabObj.find("[name=hidEmployeeKey]").val(); 
            
             $.ajax({
                type: "GET",
                url:  'ajax-employee.php', 
                data: "action=getDataRowById&pkey=" + employeeKey,  
                beforeSend:function (xhr){  
                     tabObj.find(".ar-employee").html("0");
                },
                success: function(data){   
                        if(data){ 
                            var data = JSON.parse(data);   
                            data = data[0]; 
                            //tabObj.find(".ar-employee").html(data.aroutstanding).formatCurrency({roundToDecimalPlace: 0 }); 
                        }
                }  
            }); 
        }
        

        this.updateReference = function updateReference(){
            
             $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-cost-cash-out.php', 
                    data: "action=searchAvailableReference&code=" +  tabObj.find("[name=refCode]").val(),  
                    beforeSend:function (xhr){ 
                         tabObj.find("[name=refCode2]").val("");
                         tabObj.find("[name=hidRefKey2]").val(0);
                    },
                    success: function(data){  
                            if(data){
                                 var data = JSON.parse(data);   
                                 tabObj.find("[name=hidRefTable]").val(data[0].reftabletype);
                                 tabObj.find("[name=refCode2]").val(data[0].refcode2);
                                 tabObj.find("[name=hidRefKey2]").val(data[0].refkey2);
                                 tabObj.find("[name=selWarehouse]").val(data[0].warehousekey);
                                
                                 updateComboboxReadonly(tabObj.find("[name=selWarehouse]"));
                                 updateComboboxReadonly(tabObj.find("[name=hidRefTable]"));
                                
                                // kalo JO, otomatis munculin nama penerima 
                                 tabObj.find("[name=employeeName]").val(data[0].employeename);
                                 tabObj.find("[name=hidEmployeeKey]").val(data[0].employeekey);
                                
                                 tabObj.find("[name=customerName]").val(data[0].customername);
                                 tabObj.find("[name=consigneeName]").val(data[0].consigneename);
                                
                                 thisObj.updateEmployeeInformation();
                            }
                    }  
                }); 
        }

        this.importData = function importData(){  
           
                var importButton =  tabObj.find("[name=btnImport]"); 
            
                var refCodeObj =  tabObj.find("[name=refCode]");
                var employeeObj = tabObj.find("[name=employeeName]");
                    
                var refkey = tabObj.find("[name=hidRefKey]").val();
                var reftablekey = tabObj.find("[name=hidRefTable]").val();
                var employeekey =  tabObj.find("[name=hidEmployeeKey]").val();
                var warehousekey =  tabObj.find("[name=selWarehouse]").val();
                var urlAjax = (reftablekey == 261) ? 'ajax-trucking-service-work-order.php' : 'ajax-trucking-service-order.php';
                    
                if(!refkey || !reftablekey || !warehousekey || (reftablekey == 261 && !employeekey)){ 
                    $("#defaultForm-"+tabID).bootstrapValidator('revalidateField', refCodeObj.attr("name"));   
                    $("#defaultForm-"+tabID).bootstrapValidator('revalidateField', employeeObj.attr("name"));   
                    return;
                }
                   
                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;
 
                $.ajax({
                    type: "GET",
                    url:  urlAjax,
                    beforeSend:function (xhr){
                        importButton.prop('disabled', true) ;   
                        clearAllRows($("#defaultForm-"+tabID));
                        thisObj.activeAjaxConnections++; 
                    },
                    data: "action=getUnCashedCostDetail&pkey="+refkey+"&warehousekey="+warehousekey+"&reftablekey="+reftablekey+"&employeekey="+employeekey,  
                    success: function(data){ 
                            if(!data) return;
                                
                            var data = JSON.parse(data);  
                            var i;
 
                            for(i=0;i<data.length;i++){   
                                    var arrPostValue = []; 
                                    arrPostValue.push({"selector":"refheadercostkey", "value":data[i].pkey});
                                    arrPostValue.push({"selector":"hidCostKey", "value":data[i].costkey}); 
                                    arrPostValue.push({"selector":"costName", "value":data[i].name}); 
                                    arrPostValue.push({"selector":"hidCOAKey", "value":data[i].coakey}); 
                                    arrPostValue.push({"selector":"COAName", "value":data[i].coacodename}); 
                                    arrPostValue.push({"selector":"costValue", "value":data[i].costvalue}); 
                                    arrPostValue.push({"selector":"amount", "value":data[i].requestamount}); 
                                    arrPostValue.push({"selector":"detailDesc", "value":data[i].description}); 
                                    arrPostValue.push({"selector":"qty", "value":data[i].qty}); 
                                    $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));    
                           } 
                        
                            thisObj.rebindEl(); 
                            tabObj.find(".inputnumber").change().blur(); 
  
                    } , 
                    complete:function() { 
                        importButton.prop('disabled', false);   
                        decreaseActiveAjaxConnections(thisObj);  
                    }
                     
                }); 
        }  
        
        this.sendMessage = function sendMessage(){
             
            var waLink = 'https://api.whatsapp.com/send?';
         
            var mobile = tabObj.find("[name=recipientMobile]").val();
            if (!mobile){
                alert(phpErrorMsg.phone[1]);
                return;    
            }
            
            var cost = "";
            tabObj.find("[name='costName[]']:not(:disabled)").each(function(){ 
                if(cost != "")
                    cost += ', ';
                
                cost += '*'+$(this).val()+'*';
            })
           
            
            var amount = tabObj.find("[name=total]").val() || 0 ;
            var message = 'Biaya operasional sebesar *Rp. '+amount+'* untuk '+cost+' telah ditransfer. Terima Kasih.';
            
            waLink += 'phone='+ waFormat(mobile);
            waLink += '&text='+encodeURIComponent(message);
            window.open(waLink,'_blank');
        }
        
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        }

        this.rebindEl = function rebindEl(){    
            bindEl(tabObj.find("[name='amount[]'] "),'change', function() { thisObj.calculateTotal(); });  
            bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailAutoComplete,'ajax-coa.php?action=searchData&iscashbank=1');
        }
        
		this.updateDocumentFiles = function updateDocumentFiles(objButton){
			 
			  var pkey = tabObj.find('[name=hidId]').val();
			  var token = tabObj.find('[name=token-item-file-uploader]').val();
			  var fileName = tabObj.find('[name=item-file-uploader]').val();
			
			  if (parseInt(pkey) == 0 || pkey == '' || parseInt(token) == 0|| token == '') 
					return;
			 
			  $.ajax({
                        type: "POST",
                        url:  'ajax-trucking-cost-cash-out.php', 
                        async : false,
                        data: 'action=updateDocumentFiles&pkey=' + pkey +'&token-item-file-uploader=' + token + '&item-file-uploader=' + fileName, 
                        success: function(data){   
                           
                            data = parseJSON(data);
							
							if(data.length == 0) return; 
							
							if(data[0].valid == false){
								alert(data[0].message)
							}else{
								objButton.hide();
								objButton.closest("div").find(".file-uploader").hide();
							}
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
             
            //tabObj.find(".file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(fileUploaderTarget); }});
            tabObj.find(".file-list" ).disableSelection();

            tabObj.find("[name=btnImport]" ).on('click', function() { thisObj.importData(); }); 
            tabObj.find(".wa-button" ).on('click', function() { thisObj.sendMessage(); });  
            tabObj.find("[name=arEmployee]").change(function(){ thisObj.calculateTotal(); })   
            tabObj.find("[name=btnUpdateFile]").click(function() { thisObj.updateDocumentFiles($(this)); });
			
            
            tabObj.find('[name=btnSubmitFileAjax]').on('click', function () { onSubmitFileAjax(tabObj,
                                                                                            {ajaxFile: 'ajax-trucking-cost-cash-out.php'}
                                                                                           ) });
            
            thisObj.updateEmployeeAROutstanding();
            thisObj.rebindEl();
        }
}

