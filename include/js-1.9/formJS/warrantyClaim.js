function WarrantyClaim(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     

        var  objAndValue = new Array;
        objAndValue = new Array;
        objAndValue.push({object:'hidItemkey[]', value :'pkey'}); 
        var objAndValueForItemAutoComplete = objAndValue;

        var  objAndValue = new Array;
        objAndValue = new Array;
        objAndValue.push({object:'hidVendorPartNumberKey[]', value :'pkey'});
        objAndValue.push({object:'itemName[]', value :'itemname'});    
        objAndValue.push({object:'hidItemKey[]', value :'itemkey'});
        objAndValue.push({object:'hidTempItemKey[]', value :'itemkey'});
        var objAndValueForVendorAutoComplete = objAndValue; 
    
        var objAndValue = new Array;
		objAndValue.push({object:'hidIssueKey[]', value :'pkey'});  
        var objAndValueIssueDetailAutoComplete = objAndValue;
    
        this.tabID = tabID;    

        this.updateCustomerInformation =  function updateCustomerInformation(){ 
              var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  

                if(!customerkey)  return;

               $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php', 
                    data: "action=getDataRowById&pkey=" + customerkey ,  
                }).done(function( data ) { 

                        data = JSON.parse(data) ; 
                        data = data[0];

                        var address = data.address ;  
                        tabObj.find("[name=customerName]").val(data.name);
                        tabObj.find("[name=customerPhone]").val(data.phone);
                        tabObj.find("[name=customerEmail]").val(data.email);
                        tabObj.find("[name=customerAddress]").val(address);   
                   
                        tabObj.find('form').bootstrapValidator('revalidateField', 'customerPhone'); 
                        tabObj.find('form').bootstrapValidator('revalidateField', 'customerEmail'); 
                });
        } 

        this.updateItem =  function updateItem(obj){
            var detailRow = $(obj).closest(".transaction-detail-row");
            var sn = detailRow.find("[name=\"serialNumber[]\"]").val();
 
            detailRow.find(".reset-on-data-not-found input").val("");
            detailRow.find("[name='hidSNKey[]']").val("");
            detailRow.find(".date-diff").html("0");
                       
            clearAllRows(detailRow.find(".content-of-package"));
            if (!sn) return;
                
            $.ajax({
                type: "GET",
                url:  'ajax-item.php', 
                data: "action=searchSerialNumberInMarket&sn=" + sn
            }).done(function( data ) { 
                data = JSON.parse(data) ; 

                if (data.length > 0 ){
                    data = data[0];
                    detailRow.find("[name=\"itemName[]\"]").val(decodeHTMLEntities(data.itemname));
                    detailRow.find("[name=\"hidItemKey[]\"]").val(data.itemkey);
                    detailRow.find("[name=\"hidTempItemKey[]\"]").val(data.itemkey); 
                    detailRow.find("[name=\"vendorPartNumber[]\"]").val(data.partnumber); 
                    detailRow.find("[name=\"hidVendorPartNumberKey[]\"]").val(data.vendorpartnumberkey); 

                    warrantyClaim.updateSNInformation(detailRow); 
                    warrantyClaim.updateItemContent(detailRow); 
                }else {
                    alert(phpErrorMsg[213]);
                    //detailRow.find("[name=\"serialNumber[]\"]").val("");
                }
            }); 
        }

        this.updateSNInformation = function updateSNInformation(row){
             
              var expDateCol = row.find(".expired-date");
              expDateCol.removeClass("col-red-cardinal");
            
              $.ajax({  
                        type: "GET", 
                        url:  'ajax-sn.php', 
                        data: "action=getSNInformation&sn="+ row.find("[name=\"serialNumber[]\"]").val() ,  
                        success: function(data){   
                            
                            if (!data) return;
                            var data = JSON.parse(data);  

                            data = data[0]; 
                            var itemoutdate =  (data.itemoutdate != '') ? moment(data.itemoutdate).format("DD / MM / YYYY") : '';
                            row.find("[name=\"itemOutDate[]\"]").val(itemoutdate);

                            var warrantyperiodexpireddate =  (data.warrantyperiodexpireddate != '') ? moment(data.warrantyperiodexpireddate).format("DD / MM / YYYY") : '';
                            row.find("[name=\"warrantyPeriodExpiredDate[]\"]").val(warrantyperiodexpireddate); 
                            row.find("[name=\"hidSellerKey[]\"]").val(data.recipientkey); 
                            row.find("[name=\"sellerName[]\"]").val(data.recipientname);
                              
                            row.find(".date-diff").html(data.warrantyperiodexpireddatediff).blur();
                              
                            if(data.warrantyperiodexpireddatediff < 0)
                                expDateCol.addClass("col-red-cardinal"); 
                        }  
                    }) ; 
        }

        this.updateItemContent = function updateItemContent(row){
            
                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                thisObj.activeAjaxConnections = 0;
    
                var newContentSelector = 'content-of-package-row-template';

                 $.ajax({
                    type: "GET",
                    url:  'ajax-item.php',
                    beforeSend:function (xhr){   
                        clearAllRows(row.find(".content-of-package"));
                        thisObj.activeAjaxConnections++; 
                    },
                    async:false,
                    data: "action=getItemPackageOfContent&pkey=" + row.find('[name="hidItemKey[]"]').val(), 
                    success: function(data){ 
                            
                            if (!data) return;
                            var data = JSON.parse(data);

                            for(i=0;i<data.length;i++){   
                                    var arrPostValue = [];  
                                    arrPostValue.push({"selector":"qtyDetail" , "value":0});
                                    arrPostValue.push({"selector":"itemNameDetail", "value":data[i].itemchecklistname});
                                    arrPostValue.push({"selector":"hidItemDetailKey" , "value":data[i].itemkey});  

                                    newrow = addNewTemplateRow(newContentSelector,JSON.stringify(arrPostValue),row);  
                                    newrow.find(".inputnumber").blur(); 

                                   /* newrow.find("input").each(function() {  
                                        str = $(this).attr("name");
                                        str = str.replace(/[\[](\d+)?[\]][\[][\]]$/,'['+detailRowsToken +'][]');  
                                        $(this).attr("name",str);
                                    })*/
                            }
                                
                        decreaseActiveAjaxConnections(thisObj); 

                    } ,
                     error: function(xhr, errDesc, exception) {
                         decreaseActiveAjaxConnections(thisObj);  
                   }
                });


            } 
        
        this.updateIssueKey = function updateIssueKey(obj){ 
            var hidKeyObj  = obj.closest(".transaction-detail-row").find("[name='hidIssueKey[]']"); 
            hidKeyObj.val(obj.val());
        }
            
        this.rebindEl = function rebindEl(){    
            bindEl(tabObj.find("[name='serialNumber[]']"),'change',function(){ thisObj.updateItem($(this)) });  
            bindAutoCompleteForTransactionDetail('issueName[]',objAndValueIssueDetailAutoComplete,'ajax-issue-category.php?action=searchData');  
         } 
        
        this.loadOnReady = function loadOnReady(){
             
            tabObj.find(".section-panel .title" ).click(function() { $(this).closest(".section-panel").find(".section-panel-content").first().toggle(); });
             
            var itemRows = tabObj.find(".item-row"); 
            itemRows.each(function() {    
                  var row = $(this).find(".issue-list .transaction-detail-row");  
                  if(row.length == 0)  addNewTemplateRow('issue-row-template',null,$(this),thisObj.rebindEl);  
            }) 
            
            thisObj.rebindEl();
        }
}
