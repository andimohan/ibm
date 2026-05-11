function GeneralJournal(tabID,varConstant, uploadFolder, rsFile){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID; 
    this.tablekey = varConstant.tablekey;  
    this.useStorage = varConstant.useStorage;  
     
    var fileFolder = uploadFolder;
    var fileUploaderTarget = "item-file-uploader"; 
    var arrFile = Array();  

    var objAndValue = new Array;  
    objAndValue.push({object:'hidCOAKey[]', value :'pkey'}); 
    objAndValue.push({object:'COAName[]', value :'value'}); 
    var objAndValueForDetailAutoComplete = objAndValue;

    var id = tabObj.find("[name=hidId]").val();  
    
    
     this.calculateDetail = function calculateDetail(obj){   
          
            var rowObj = $(obj).closest(".transaction-detail-row");  
            var rate = parseFloat(unformatCurrency(rowObj.find("[name='rate[]']").val())) || 0;   


            var debitSource = parseFloat(unformatCurrency(rowObj.find("[name='debitSource[]']").val())) || 0;
            var creditSource = parseFloat(unformatCurrency(rowObj.find("[name='creditSource[]']").val())) || 0;   
 
            var selCurrencyDetailObj = rowObj.find("[name='selCurrencyKey[]']");  
         
            // kalo currency IDR, overwrite rate = 1 
            if (parseInt(selCurrencyDetailObj.val()) ==  varConstant.currency.idr){
                rate = 1;
                rowObj.find("[name='rate[]']").val(rate);
            }
         
         
            var subtotalDebit = debitSource * rate; 
            var subtotalCredit = creditSource * rate; 
 

            rowObj.find("[name='debit[]']").val(subtotalDebit).blur();   
            rowObj.find("[name='credit[]']").val(subtotalCredit).blur();   
             

            thisObj.calculateTotal();
        }
     
     
         this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row"); 

            var i;
           for(i=0;i<objAndValue.length;i++){      
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();    
            }

            thisObj.calculateTotal(); 

            detailRow.find("[name='COAName[]']").first().val(ui.item['value']); 
            detailRow.find("[name='hidCOAKey[]']").first().val(ui.item['pkey']); 
             
        } 
         
        
	    
        this.calculateTotal = function calculateTotal(){   
                var debit = 0;
                tabObj.find( " [name='debit[]']").each(function() {   
                  debit += parseFloat(unformatCurrency($(this).val())) || 0;
               })

               tabObj.find( " [name='totalDebit']").val(debit).blur();

               var credit = 0;
               tabObj.find(" [name='credit[]']").each(function() {   
                     credit += parseFloat(unformatCurrency($(this).val())) || 0;
               })

              tabObj.find( " [name='totalCredit']").val(credit).blur();       
                                              
     } 
        
    this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
         }
    
    this.updateDocumentFiles = function updateDocumentFiles(objButton){
			 
			  var pkey = tabObj.find('[name=hidId]').val();
			  var token = tabObj.find('[name=token-item-file-uploader]').val();
			  var fileName = tabObj.find('[name=item-file-uploader]').val();
			
			  if (parseInt(pkey) == 0 || pkey == '' || parseInt(token) == 0|| token == '') 
					return;
			 
			  $.ajax({
                        type: "POST",
                        url:  'ajax-general-journal.php', 
                        async : false,
                        data: 'action=updateDocumentFiles&pkey=' + pkey +'&token-item-file-uploader=' + token + '&item-file-uploader=' + fileName, 
                        success: function(data){    
                            data = parseJSON(data);
							
							if(data.length == 0) return; 
							
							alert(data[0].message);
							
							if(data[0].valid == false){
//								alert(data[0].message)
							}else{
								objButton.hide();
								objButton.closest("div").find(".file-uploader").hide();
							}
                        }  
                    }); 
		}
		
    
    
    this.rebindEl = function rebindEl(){ 
        bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailAutoComplete ,'ajax-coa.php?action=searchData',thisObj.updateDetail); 
        bindEl(tabObj.find("[name='debitSource[]'],[name='creditSource[]'], [name='rate[]'], [name='selCurrencyKey[]']"),'change', function() { thisObj.calculateDetail(this); });  
        bindEl(tabObj.find("[name='debit[]'],[name='credit[]']"),'change', function() { thisObj.calculateTotal(); });   
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
          
        
        tabObj.find(" .inputnumber")
			 .each(function() {  
				if($(this).val() == "") $(this).val(0); 
			 })
			 .bind( "blur", function(event) { 
			   inputNumberOnBlur($(this));
	 	});

        
        if(!id)
           addNewTemplateRow("detail-row-template",null,null,thisObj.rebindEl);

        tabObj.find("[attr-header=\"true\"]").attr('readonly',true).addClass("force-readonly"); // utk autocomplete, harus pake attr readonly 
        tabObj.find("select[readonly]").find("option:not(:selected)").attr('disabled', true);
        tabObj.find("[name=btnUpdateFile]").click(function() { thisObj.updateDocumentFiles($(this)); });
			 
         tabObj.find('[name=btnSubmitFileAjax]').on('click', function () { onSubmitFileAjax(tabObj,
                                                                                            {ajaxFile: 'ajax-general-journal.php'}
                                                                                           )
                                                                  });
        
        thisObj.rebindEl(); 
     
    }
}