function JobProgress(tabID,data){   
    var thisObj = this;
    var tabObj = $("#" + tabID);    

    this.tabID = tabID;  
    
    this.updateAutoNumberRow = function updateAutoNumberRow() {
        var number = 1;

        $(".transaction-detail-row").each(function() {
            var input = $(this).find("input[name='numberDetail[]']");
            if (!input.length) return;

            input.each(function() {
                $(this).val(number).trigger("change");
            });

            number++;
        });
    }

     this.afterRemoveRowHandler = function afterRemoveRowHandler(){
        thisObj.updateAutoNumberRow();
    }
      
    this.rebindEl = function rebindEl(){   
        thisObj.updateAutoNumberRow(); 
    } 
     
    this.loadOnReady = function loadOnReady() { 
        
        if (!data['detail'] || data['detail'].length == 0)
                addNewTemplateRow("detail-row-template",null,null,thisObj.rebindEl);

        thisObj.rebindEl(); 
    }
    
}