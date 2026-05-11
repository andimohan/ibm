function CashBankTransfer(tabID,varConstant,opt){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	this.tablekey = varConstant.TABLEKEY;  
        this.useStorage = varConstant.USE_STORAGE;  
     
		var fileFolder = varConstant.uploadFileFolder;
		var fileUploaderTarget = "item-file-uploader";
		var rsFile = varConstant.rsFile; 
		var arrFile = Array(); 

        var objAndValueFromForDetailAutoComplete = new Array; 
        var objAndValueToForDetailAutoComplete = new Array; 

        objAndValueFromForDetailAutoComplete.push({object:'hidCOAFromKey[]', value :'pkey'}); 
        objAndValueToForDetailAutoComplete.push({object:'hidCOAToKey[]', value :'pkey'}); 
 
  //      <?php if (empty($_GET['id'])){ ?> 
  //       	addNewTemplateRow("detail-row-template");
  //      <?php } ?>
  //      bindAutoCompleteForTransactionDetail('COAFromName[]',objAndValueFromForDetailAutoComplete[selectedTab.newPanel[0].id],'ajax-coa.php?action=searchData&iscashbank=1'); 
  //      bindAutoCompleteForTransactionDetail('COAToName[]',objAndValueToForDetailAutoComplete[selectedTab.newPanel[0].id],'ajax-coa.php?action=searchData&iscashbank=1');
  
 
    	var id = tabObj.find("[name=hidId]").val();
	       
        this.tabID = tabID;     
      
    
        this.calculateTotal = function calculateTotal(){
            
            var amount = 0;   
             
            tabObj.find("[name='amount[]']").each(function(){   
                    amount += parseInt(unformatCurrency($(this).val())) || 0; 
            });     

            tabObj.find("[name='total']").val(amount).blur();  
 
        };
    
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
        };
                
        this.rebindEl = function rebindEl(){  
            bindEl(tabObj.find("[name='amount[]'] "),'change', function() { thisObj.calculateTotal(); });       
            bindAutoCompleteForTransactionDetail('COAFromName[]',objAndValueFromForDetailAutoComplete ,'ajax-coa.php?action=searchData&iscashbank=1'); 
            bindAutoCompleteForTransactionDetail('COAToName[]',objAndValueToForDetailAutoComplete,'ajax-coa.php?action=searchData&iscashbank=1');

        };
         
        this.loadOnReady = function loadOnReady(){  
			
            var hidCurrencyObj = tabObj.find("[name=hidCurrencyKey]");
  
             
            if(thisObj.useStorage){

            }else{ 
                if(id){    
                    for($i=0;$i<rsFile.length;$i++) 
                        arrFile.push(rsFile[$i].file); 

                    createFileUploader(fileUploaderTarget,fileFolder, id ,arrFile,true);  

                }else{  
                     createFileUploader(fileUploaderTarget, fileFolder, "", "", true);
                }
            }
                  
            tabObj.find('[name=btnSubmitFileAjax]').on('click', function () { onSubmitFileAjax(tabObj,
                                                                                            {ajaxFile: 'ajax-cash-bank-transfer.php'}
                                                                                           );});
            
            thisObj.rebindEl();
        }
}
