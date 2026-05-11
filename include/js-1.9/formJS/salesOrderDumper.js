function SalesOrderDumper(tabID){   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
  
        this.tabID = tabID;     
        var id = tabObj.find("[name=hidId]").val(); 
     
        this.recalculateTotal = function recalculateTotal(){ 
            
            var weight = parseFloat(unformatCurrency(tabObj.find("[name='weight']").val() )) || 0;
            var distance = parseFloat(unformatCurrency(tabObj.find("[name='distance']").val() )) || 0;
            var pricePerDistance = parseInt(unformatCurrency(tabObj.find("[name='pricePerDistance']").val() )) || 0;
            
            var amount = weight * pricePerDistance;
            tabObj.find("[name='totalPrice']").val(amount).blur();  
              
        }
     
        this.importDataDetail =  function importDataDetail(){ 
            $.ajax({
                    type: "GET",
                    url:  'ajax-project-dumper.php', 
                    async: false,
                    data: "action=getLocationInformation&pkey=" + tabObj.find("[name=hidProjectKey]").val()+ "&locationkey=" + tabObj.find("[name=selDestination]").val() ,  
                    success: function(data){ 
                         
                        if (!data) return;
                        data = JSON.parse(data) ; 
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
  
                        data = data[0]; 
                        
                        tabObj.find("[name=distance]" ).val(data.qty).blur(); 
                        tabObj.find("[name=pricePerDistance]" ).val(data.priceperdistance).blur(); 
                        thisObj.recalculateTotal();
 
                    } ,
                    complete:function() {   
                       
                    }
                });
        }
        
        this.updateLocation = function updateLocation(){
            $.ajax({
                    type: "GET",
                    url:  'ajax-project-dumper.php', 
                    async: false,
                    data: "action=getDetailById&pkey=" + tabObj.find("[name=hidProjectKey]").val() ,  
                }).done(function( data ) {

                    if (!data) return;
                    data = JSON.parse(data) ;

                    // update combobox services
                    var newOptions = {};
                    for(i=0;i<data.length;i++)  
                        newOptions[data[i].locationkey] =  data[i].locationname;       

                    var select =  tabObj.find("[name=selDestination]"); 
                    var options = (select.prop) ? select.prop('options') : select.attr('options');  

                    $('option', select).remove(); 

                    $.each(newOptions, function(val, text) {
                        options[options.length] = new Option(text, val);
                    });

                    select.find('option:eq(0)').prop('selected', true).change();

                });
        }
         
        this.updateProjectInformation =  function updateProjectInformation(){
            $.ajax({
                    type: "GET",
                    url:  'ajax-project-dumper.php',
                    async: false, 
                    data: "action=getDataRowById&pkey=" +  tabObj.find("[name=hidProjectKey]" ).val() ,  
                    success: function(data){ 
                        if (!data) return;
                        
                        data = JSON.parse(data); 
                        
                        if(data.length == 0){ 
                            alert(phpErrorMsg[213])
                            return;
                        }
  
                        data = data[0]; 
                        
                        tabObj.find("[name=location]" ).val(data.locationname); 
  
                        // udpate detail container 
                        thisObj.updateLocation();

                    } ,
                    complete:function() {  
                           
                    }
                });
        }
        
        
        this.rebindEl = function rebindEl(){  
        }
          
        this.loadOnReady = function loadOnReady(){ 
            tabObj.find("[name=selDestination]").change(function() { thisObj.importDataDetail(); });
            tabObj.find("[name=selDestination]").change();
            tabObj.find("[name=weight], [name=pricePerDistance],[name=distance]" ).change(function(){thisObj.recalculateTotal()}); 
            thisObj.rebindEl();
        }
}