function MembershipAttendance(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID; 
	
		this.updateMembership = function updateMembership(){
            var membershipkey = tabObj.find("[name=selCustomerMembership]").val(); 
           // var membershipkey = tabObj.find("[name=hidCustomerMembershipKey]").val(); 
            
				 $.ajax({
                            type: "GET",
                            url:  'ajax-customer-membership.php',
                            async: false,
                            data: "action=getDataRowById&pkey=" + membershipkey,    
                        }).done(function( data ) { 
                        if(data){
                            var data = JSON.parse(data);    
                            data = data[0];
                             
                             if (!data) return;
                                
                            tabObj.find("[name=maxAttendance]").val(data.maxattendance); 
                            tabObj.find("[name=timeLimit]").val(data.timelimit);
                            tabObj.find("[name=selMembership]").val(data.membershipkey);
                            tabObj.find("[name=attendance]").val(data.attendance);
                           // tabObj.find("[name=activationDate]").val(moment(data.activationdate).format(_DATE_FORMAT_));
                        	tabObj.find(".inputnumber").change().blur();  
						}   
            });  
   
        }
		
		this.updateCustomer =  function updateCustomer(){ 
                      var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  
                       
                        if(!customerkey)
                            return;
 
                       $.ajax({
                            type: "GET",
                            url:  'ajax-customer-membership.php',
                            async: false,
                            data: "action=getCustomer&customerkey=" + customerkey ,  
                        }).done(function( data ) { 
                                var select = tabObj.find(" [name=selCustomerMembership]");
                                $('option', select).remove();
                                
						   		if (!data) return;
		 				   
						   		data = JSON.parse(data) ;
						   
						   		if (!data[0]) return;
                                var newOptions = {};
								for(i=0;i<data.length;i++)  
									newOptions[data[i].pkey] =  data[i].code;       

								
								if(select.prop)  
								  var options = select.prop('options'); 
								else  
								  var options = select.attr('options');
 
								$('option', select).remove();

								$.each(newOptions, function(val, text) {
									options[options.length] = new Option(text, val);
				 				});
 
								select.find('option:eq(0)').prop('selected', true).change();
                        }); 
        }
		
         
        this.rebindEl = function rebindEl(){}
         
        this.loadOnReady = function loadOnReady(){
			 
			tabObj.find("[name=selCustomerMembership]").change(function() { thisObj.updateMembership(); });
			tabObj.find("[name=selCustomerMembership]").val();
			thisObj.rebindEl(); 
		}
}
