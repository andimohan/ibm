function MedicalRecord(tabID,rs){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID;    
        
        var  objAndValue = new Array;
		objAndValue.push({object:'hidEmployeeKey[]', value :'pkey'});
//		objAndValue.push({object:'employeeName[]', value :'name'});
        var objAndValueForDetailAutoComplete  = objAndValue; 
        
    
        this.rs = (rs.length > 0) ? rs[0] : null;

    
        var id = tabObj.find("[name=hidId]").val();  
        
    
        this.updateDetail = function updateDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row");
            var quizKeyObj = detailRow.find("[name=\"hidEmployeeKey[]\"]").first();

            for(i=0;i<objAndValue.length;i++){   
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
            } 

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"employeeName[]\"]").first().val(ui.item['value']);  
 
         }
        
        this.updateCustomerInformation = function updateCustomerInformation(){
            var customerkey = tabObj.find("[name=hidCustomerKey]").val();
              
             $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + customerkey ,  
                }).done(function( data ) { 
 
                        if(!data) return;
                 
                        data = JSON.parse(data) ; 
                        data = data[0];

                        tabObj.find("[name=customerCode]").val(data.code);
                        tabObj.find("[name=address]").val(data.address);
                        tabObj.find("[name=medicineAllergy]").val(data.description);
                });
 
        }  
           
        this.updateSalesMan = function updateSalesMan(){
            var customerkey = tabObj.find("[name=hidCustomerKey]").val();
              
             $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    async: false,
                    data: "action=getSalesman&pkey=" + customerkey ,  
                }).done(function( data ) { 
 
                        if(!data) return;
                 
                        data = JSON.parse(data) ; 
                        if ( data.length  == 0  ) return;

                 
                        tabObj.find("[name=hidEmployeeKey]").val(data.pkey);
                        tabObj.find("[name=employeeName]").val(data.name);
         
                });
 
        }  
        
        
        this.updateAgeCustomer = function updateAgeCustomer(){
            var customerkey = tabObj.find("[name=hidCustomerKey]").val();
             $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    async: false,
                    data: "action=getCustomersAge&pkey=" + customerkey ,  
                }).done(function( data ) { 

                        if(!data) return;
                 
                        data = JSON.parse(data) ; 
                        if ( data.length  == 0  ) return;

                        tabObj.find("[name=age]").val(data);
         
                });
 
        }  
      
      
        this.rebindEl = function rebindEl(){ 
            
            bindAutoCompleteForTransactionDetail('employeeName[]',objAndValueForDetailAutoComplete,'ajax-employee.php?action=searchData',thisObj.updateDetail); 

        
        } 
        
        this.loadOnReady = function loadOnReady(){  
     
            
            if (!thisObj.rs){
                var newRow = addNewTemplateRow("medical-row-template",null,null,thisObj.rebindEl);
                newRow.find(".input-datetime").removeClass("hasDatepicker");
                newRow.find(".input-datetime").removeAttr("id"); 
                newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
            }
                        
            tabObj.find("[name=btnAddMedical]").on('click', function() {
                var newRow = addNewTemplateRow("medical-row-template",null,null,thisObj.rebindEl);
                newRow.find(".input-datetime").removeClass("hasDatepicker");
                newRow.find(".input-datetime").removeAttr("id"); 
                newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
                    

            });
            
         thisObj.rebindEl();
        
        }
        
}
