function TruckingCashOutRequest(tabID){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;    

        var objAndValue = new Array; 
        // objAndValue.push({object:'hidEmployeeKey[]', value :'pkey'}); 
        
        objAndValue.push({ object: 'hidCostCashOutKey[]', value: 'pkey' });
        objAndValue.push({ object: 'refDate[]', value: 'refdate', type : 'date' });   
        objAndValue.push({ object: 'recipientNameDetail[]', value: 'employeename' });
        objAndValue.push({ object: 'amount[]', value: 'subtotal' });
        
    var objAndValueForDetailAutoComplete = objAndValue;
    
        this.tabID = tabID;    
       
    this.calculateTotal = function calculateTotal(obj){
               
            var amount = 0;   
             
            tabObj.find("[name='amount[]']").each(function(){   
                    amount += parseInt(unformatCurrency($(this).val())) || 0; 
            })     

           tabObj.find("[name='total']").val(amount).blur();  
              
    }

    this.resetDetails = function resetDetails() {  
        clearAllRows(tabObj.find(".mnv-transaction"));
        thisObj.rebindEl();
    }
    

     this.afterRemoveRowHandler = function afterRemoveRowHandler(){
            thisObj.calculateTotal(); 
    }


    this.importData = function importData() {
        loadOverlayScreen({ content: _LOADING_TEMPLATE_ });
        thisObj.activeAjaxConnections = 0;

        //get value startdate & enddate
        var startdate = tabObj.find("[name=trStartDatePeriod]").val();
        var enddate = tabObj.find("[name=trEndDatePeriod]").val();

        var dateParam = "";

        if (startdate && enddate) 
            dateParam = "&startdate=" + startdate + "&enddate=" + enddate;
        
        var ajaxData = "action=searchDataForRequest&statuskey=1&recipientkey=" + tabObj.find("[name=hidRecipientKey]").val() + dateParam;
       
        $.ajax({
            type: "GET",
            url: "ajax-trucking-cost-cash-out.php",
            beforeSend: function (xhr) {

            clearAllRows(tabObj.find(".mnv-transaction"));
                thisObj.activeAjaxConnections++; 
            },
            data: ajaxData,
            success: function (data) {
 
                var data = JSON.parse(data);

                var i;
                for(i=0;i<data.length;i++){  
                    var arrPostValue = []; 
                        arrPostValue.push({ "selector": "hidCostCashOutKey", "value": data[i].pkey }); 
                        arrPostValue.push({"selector":"cashOutCode", "value":data[i].code});
                        arrPostValue.push({ "selector": "refDate", "value": moment(data[i].trdate).format("DD / MM / YYYY HH:mm") });
                        arrPostValue.push({"selector":"recipientNameDetail", "value":data[i].employeename});
                        arrPostValue.push({"selector":"amount", "value":data[i].subtotal});

                        addNewTemplateRow("detail-row-template", JSON.stringify(arrPostValue));  
                    }
                    
                tabObj.find(".inputnumber").change().blur();
                tabObj.find(".inputdecimal").change().blur();

                decreaseActiveAjaxConnections(thisObj); 
                
            },
                error: function (xhr, errDesc, exception) {
                 decreaseActiveAjaxConnections(thisObj); 
            }
        })

    }

    this.updateEmployeeInformation = function updateEmployeeInformation(event, ui) {
        var obj = this; 
        thisObj.rebindEl(); 
    }

    this.rebindEl = function rebindEl() {  

        var startdate = tabObj.find("[name=trStartDatePeriod]").val();
        var enddate = tabObj.find("[name=trEndDatePeriod]").val();
        var dateParam = "&startdate=" + startdate + "&enddate=" + enddate;

        bindAutoCompleteForTransactionDetail('cashOutCode[]', objAndValueForDetailAutoComplete,'ajax-trucking-cost-cash-out.php?action=searchDataForRequest&recipientkey=' + tabObj.find("[name=hidRecipientKey]").val() + dateParam, thisObj.updateDetail);
        
        bindEl(tabObj.find("[name='amount[]'] "), 'change', function () { thisObj.calculateTotal(); });      
    } 

    this.updateDetail = function updateDetail(target,objAndValue,ui){   
            var detailRow = $(target).closest(".transaction-detail-row");
            for(i=0;i<objAndValue.length;i++){   
                if (objAndValue[i].type == "date")
                   ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format("DD / MM / YYYY HH:mm");

                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(decodeHTMLEntities(ui.item[objAndValue[i].value])).change().blur();  
            }

            detailRow.find("[name=\"cashOutCode[]\"]").first().val(ui.item['code']);
            //ARPayment.updateDefaultDownpayment();
    }
     
    this.loadOnReady = function loadOnReady() { 
        thisObj.rebindEl(); 
        thisObj.calculateTotal();
        tabObj.find("[name=btnImport]").on('click', function() { thisObj.importData(); });
    }
    
}
