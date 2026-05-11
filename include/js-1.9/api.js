function compileAPIResponseMessage(data){  
	
        var responseMessage = '';
        var success = true; 
        var colorClass = 'text-green-avocado';
        
        if(data.response_code != 200){ 
            success = false;
            colorClass = 'text-red-cardinal'; 
        }
	
        
        if (data.message){             
			responseMessage = data.message;
		}else{ 
				var failedData = data.failed_data;
				 
				for(var i=0; i<failedData.length ;i++ ) { 
					var msg = failedData[i].message;
					
					for(var j=0; j<msg.length ;j++ ) { 
						responseMessage += msg[j];
						responseMessage += '<br>'; 
					}
				}

				var successData = data.success_data;
				for(var i=0; i<successData.length ;i++ ) {  
					var msg = successData[i].message;
						
					for(var j=0; j<msg.length ;j++ ) { 
						responseMessage += msg[j];
						responseMessage += '<br>'; 
					}
				}
		}
            
        var returnArr = {};
        returnArr['code'] = data.response_code;
        returnArr['message'] = responseMessage;
        returnArr['color'] = colorClass;
        returnArr['success'] = success;
        
        return  returnArr;
}

function importData(arrData, ajaxURL, arrList,ctr){

        if (!arrList[ctr]) return; 
          
        var indexKey = arrList[ctr]['index'];
        var itemRow =  arrList[ctr]['row'];
     
        $.ajax({ 
              // gk boleh pake path relative, karena akan bentrok di htacess, selalu diredirect dr index
            
              url: '/tools/import/'+ajaxURL,  
              method : 'POST',
              async: false,
              data: {
                        data: arrData[indexKey] , 
                    },
              success: function(data){
                    var data = JSON.parse(data);   
				  	
                    $response = compileAPIResponseMessage(data);
                    itemRow.find(".response-code").addClass($response.color).html($response.code);
                    itemRow.find(".desc").addClass($response.color).html($response.message);

                    // pindahin ke atas utk hasil yg error 
                    var itemRows = $("[relgroup="+indexKey+"]");

                    if (!$response.success)
                        $(".import-result-failed").append(itemRows); 
                    else
                        $(".import-result-success").append(itemRows); 
                   
               } 
          });

                   importData(arrData, ajaxURL, arrList, ++ctr);
}

function startImportData(itemList, arrData, ajaxFile){ 
     
   var arrList = new Array(); 
       
    itemList.each(function(){  
        arrList.push({
             index:  $(this).attr("relkey"), 
             row : $(this) 
            });
     });

    importData(arrData,ajaxFile,arrList,0);    
}