function Leasing(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);    
    
        this.tabID = tabID;    
        
        this.calculateInstallment = function calculateInstallment(){ 
            var periode = parseInt(unformatCurrency(tabObj.find("[name='period']").val()));
            var loan =  parseInt(unformatCurrency(tabObj.find("[name='loanAmount']").val()));
             
            loan = (periode == 0) ? 0 : loan / periode;
            tabObj.find("[name='installment']").val(loan).blur();  
        }
//           
 
        this.rebindEl = function rebindEl(){    
        } 
        
        this.loadOnReady = function loadOnReady(){ 
            
            tabObj.find("[name='period'], [name='loanAmount']").change(function(){thisObj.calculateInstallment()}); 
             
            thisObj.rebindEl();  
        }
        
}
