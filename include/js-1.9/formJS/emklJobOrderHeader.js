function EmklJobOrderHeader(tabID,data,varConstant) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
     
    	var objAndValue = new Array; 
        objAndValue.push({object:'hidContainerDetailKey[]', value :'pkey'}); 
        var objAndValueForDetailAutoComplete = objAndValue;
        
        this.tabID = tabID;    
     
        this.afterRemoveRowHandler = function afterRemoveRowHandler(){
         
        }
        
        this.updateSalesman=function updateSalesman(){
             // update salesman 
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php', 
                    async : false,
                    data: "action=getSalesman&pkey=" + tabObj.find("[name=hidCustomerKey]").val(),  
                    beforeSend:function (xhr){ 
                        tabObj.find("[name=hidSalesKey]").val(0);
                        tabObj.find("[name=salesName]").val("");
                    },
                success: function (data) {  
                        data = parseJSON(data); 
                        if(data.length == 0)return;  
                        
                       tabObj.find("[name=hidSalesKey]").val(data.pkey);
                       tabObj.find("[name=salesName]").val(data.name);
                         
                    }  
                });
            
        }
          
        this.updateJob = function updateJob(){  
            var selTypeObj = tabObj.find("[name=selContainerType]"); 
            
            if (selTypeObj.val() == varConstant.EMKL.emklType.fcl ||
				selTypeObj.val() == varConstant.EMKL.emklType.trucking){
                //laut
                tabObj.find(".lcl").hide();
                tabObj.find(".truckingfcl").show();
            }else if(selTypeObj.val() == varConstant.EMKL.emklType.lcl || 
					 selTypeObj.val() == varConstant.EMKL.emklType.lclnc ){ 
                tabObj.find(".lcl").show();
                tabObj.find(".truckingfcl").hide();
            }else{
                tabObj.find(".lcl").hide();
                tabObj.find(".truckingfcl").hide(); 
            }
        }
                 
        this.rebindEl = function rebindEl(){  
              
        } 
            
        this.loadOnReady = function loadOnReady(){ 
            tabObj.find("[name=selContainerType]").change(function() { thisObj.updateJob(); });
            tabObj.find("[name=selContainerType]").change();
             
            // TEL gk pake data
            if(data && data['containerNumberDetail']){
                if(data['containerNumberDetail'].length == 0)
                    addNewTemplateRow("container-row-template",null,null,thisObj.rebindEl);
            }
             
            // sudah terbentuk
//            if(!data['detail'] || data['detail'].length == 0)
//                addNewTemplateRow("detail-row-template",null,null,thisObj.rebindEl);
             
            thisObj.rebindEl();
        }  
}  
