var phpConfiguration = {}; 
var phpErrorMsg = {}; 
var phpLang = {}; 
var phpSetting = {}; 
var phpModuleSetting = {};
 	
jQuery(document).ready(function(){ 

        $.ajax({
			type: "POST",
			url: "/admin/getPHPVariables.php",
			async: false, 
			success: function(data){  
                variables = JSON.parse(data);   
                phpConfiguration = variables['phpConfiguration'];
                phpErrorMsg = variables['phpErrorMsg'];
                phpLang = variables['phpLang'];
                phpSetting = variables['phpSetting'];
                phpModuleSetting = variables['phpModuleSetting'];
			} 
		}); 
     
});   