function Course(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID;    
        
        var  objAndValue = new Array;
		objAndValue.push({object:'hidQuizKey[]', value :'pkey'});
//		objAndValue.push({object:'quizName[]', value :'name'});
        var objAndValueForDetailAutoComplete  = objAndValue; 
    
    
        var id = tabObj.find("[name=hidId]").val();  
        
    
        this.updateDetail = function updateDetail(target,objAndValue,ui){
             
            var detailRow = $(target).closest(".transaction-detail-row");
            var quizKeyObj = detailRow.find("[name=\"hidQuizKey[]\"]").first();

            for(i=0;i<objAndValue.length;i++){   
                detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
            } 

            // harus handle manual utk obj autosearch
            detailRow.find("[name=\"quizName[]\"]").first().val(ui.item['value']);  
 
         }
      
      
        this.rebindEl = function rebindEl(){ 
            bindAutoCompleteForTransactionDetail('quizName[]',objAndValueForDetailAutoComplete,'ajax-quiz.php?action=searchData','getTabObj().updateDetail'); 

        
        } 
        
        this.loadOnReady = function loadOnReady(){  

            
         thisObj.rebindEl();
        
        }
        
}
