function CustomerMembership(tabID) {   
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID; 
 
	   
		this.updateMembership = function updateMembership(){
            var membershipkey = tabObj.find("[name=selMembership]").val(); 
             
             $.ajax({
                type: "GET",
                url:  'ajax-membership.php', 
                data: "action=getDataRowById&pkey=" + membershipkey,  
                beforeSend:function (xhr){ 
                     tabObj.find("[name=maxAttendance]").val(0);
                     tabObj.find("[name=timeLimit]").val(0);
                },     
                success: function(data){   
                        if(data){
                            var data = JSON.parse(data);    
                            data = data[0];
                            
                            if (!data) return;
                            
                            if (data.maxattendance)  tabObj.find("[name=maxAttendance]").val(data.maxattendance); 
                            if (data.timelimit) tabObj.find("[name=timeLimit]").val(data.timelimit);
                            if (data.price) tabObj.find("[name=registrationCost]").val(data.price);
                        	tabObj.find(".inputnumber").change().blur();  
						}
                }  
            }); 
  
        } 
        
        this.updateVoucher = function updateVoucher(){
            var voucherkey = tabObj.find("[name=selVoucher]").val(); 
             
             $.ajax({
                type: "GET",
                url:  'ajax-voucher-transaction.php', 
                data: "action=getDataRowById&pkey=" + voucherkey,  
                beforeSend:function (xhr){ 

                },     
                success: function(data){ 
                        if(data){
                            if (!data) return;
                            var data = JSON.parse(data);    
                            data = data[0];
                             
                            if(data.value) tabObj.find("[name=discountValue]").val(data.value);
                        	tabObj.find(".inputnumber").change().blur();  
						}
                }  
            }); 
  
        } 
        
        
        this.updateCustomer =  function updateCustomer(){ 
            var customerkey = tabObj.find("[name=hidCustomerKey]" ).val();  
            var value = 0;

            if(!customerkey)   return;

               $.ajax({
                    type: "GET",
                    url:  'ajax-voucher-transaction.php',
                    async: false,
                    data: "action=getAvailableVoucher&customerkey=" + customerkey , 
                    beforeSend:function (xhr){ 
                         tabObj.find("[name=discountValue]").val(0);
                    },   
                   success: function( data ) { 
                       if(data){   

                            var select = tabObj.find(" [name=selVoucher]");
                            $('option', select).remove();
                            var data = JSON.parse(data);
 
                            var newOptions = {}; 

                            for(var i=0;i<data.length;i++)
                                newOptions[data[i].pkey] = data[i].code; 
 
                            var options = (select.prop)  ? select.prop('options') : select.attr('options'); 
                           
                            $('option', select).remove();

                            $.each(newOptions, function(val, text) {
                                options[options.length] = new Option(text, val);
                            });

                             select.find('option:eq(0)').prop('selected', true).change();                                     

                        }

                       thisObj.calculateTotal();
                    }
                });
        }
        
      
        
        this.calculateTotal = function calculateTotal(){
            var value = parseInt(unformatCurrency(tabObj.find("[name=discountValue]").val()));
            var registrationCost = parseInt(unformatCurrency(tabObj.find("[name=registrationCost]").val()));
            var total;

            total = registrationCost - value;            
            tabObj.find("[name=balance]").val(total).blur();
  
        }
 
        this.rebindEl = function rebindEl(){
            
        }
            
        this.loadOnReady = function loadOnReady(){
            
            tabObj.find("[name=selMembership]").change(function() { thisObj.updateMembership(); }); 
            tabObj.find("[name=selVoucher]").change(function() { thisObj.updateVoucher(); }); 
            tabObj.find("[name=discountValue],[name=registrationCost]").change(function(){thisObj.calculateTotal(); }); 
 
			thisObj.rebindEl(); 
            
            tabObj.find("[name=selMembership]").change();
             
		}
}
