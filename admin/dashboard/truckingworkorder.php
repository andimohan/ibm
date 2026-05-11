<?php 

include '../../_config.php'; 
include '../../_include-v2.php';  

includeClass(array('TruckingServiceWorkOrder.class.php','GPSConnection.class.php','Car.class.php'));
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$obj = $truckingServiceWorkOrder;   

$gps = new GPSConnection();
$car = new Car();
 
$securityObject = $obj->securityObject; 

if(!$security->isAdminLogin($securityObject,10,true)); 

// ambil informasi GPS mana saja yg bisa sekaligus narik semua
$rsCar = $car->searchData($car->tableName.'.statuskey',1,true, 'and '.$car->tableName.'.gpskey <> "" '); 

$arrCarBatchKey = array();

foreach($rsCar as $row){ 
	$gpsObj = $gps->getGPSObj(strtolower($row['gpsprovidername'])); 
	if ($gpsObj->opt['getAllVehicle']) {
		array_push($arrCarBatchKey,$row['policenumber']);
	}
}

?> 
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive-1.0.min.css" /> 
	
<style>
	
@keyframes highlight {
  from {color: #0093AF;}
  to {color: #000;}
}	
.animate-highlight{animation: highlight 1s}
	
</style>	

<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
<script>
jQuery(document).ready(function(){    
       
    var baseURL = '/admin/dashboard/';
    var fetching = {};
    var fetchingIndex = 0;
    var workOrderRowName = 'work-order-detail-row';
	 
	var batchUpdateVehicle = <?php echo json_encode($arrCarBatchKey); ?>; 
	 
	
    function updateWorkProgress(){  
              
         $.ajax({
			type: "POST",
            //data : 'action=updateWorkProgress', 
			async : false,
			url: baseURL + "ajax-work-order.php",  
			success: function(data){    
                if (!data) {
                    // remove all rows
                    $("."+workOrderRowName).remove();
                }
                  
                data = JSON.parse(data);


                for(var i=0;i<data.length;i++){ 
//                for(var i=0;i<2;i++){  
					
                        $newRow = $(".row-template").clone().removeClass("row-template").addClass(workOrderRowName); 
                        $newRow.insertBefore($(".row-template"));
						
						$newRow.attr("relPoliceNumber", data[i]['policenumber'].replace(/\s/g,''));
                        $newRow.attr("id", data[i]['wokey']);

                        $newRow.find(".row-number").html((i+1) + ".");
                        $newRow.find(".socode").html(data[i]['socode']);
                        $newRow.find(".wocode").html(data[i]['wocode']);
                        $newRow.find(".customername").html(data[i]['customername']);
                        $newRow.find(".registrationnumber").html(data[i]['policenumber']);
                        $newRow.find(".drivername").html(data[i]['drivername']);
                        $newRow.find(".route").html(data[i]['routefrom'] + " - " + data[i]['routeto']);
                        
						// bisa beda2 tergantung provider
						$newRow.find("[name='hidTrackingId[]']").val(data[i]['gpstrackingid']); 
						$newRow.find("[name='hidRegistrationNumber[]']").val(data[i]['policenumber']); 
						$newRow.find("[name='hidGPSKey[]']").val(data[i]['gpskey']); 
						$newRow.find("[name='hidProviderName[]']").val(data[i]['providername']); 
					
                }
                  
			} 
		}); 
    } 
  
//    setInterval(updateWorkProgress, 180000);
    updateWorkProgress();     
	
	function parseJSON(data){ 

		data = $.trim(data);

		if(!data) data = '[]'; 
		if(data.length == 0) data = '[]'; 

		return JSON.parse(data);
	}
    
     function updateGPSInformationBatch(){
		   
            
         $.ajax({
			type: "GET",
            data : 'registrationnumber='+JSON.stringify(batchUpdateVehicle), 
            url: baseURL + "ajax-gps.php",   
            beforeSend : function() {
                 
            },
			success: function(data){   
				
                if (!data) return;
				
                 data = parseJSON(data); 
				 if(data.length == 0)  return;
				
				//loop data cari row berdasarkan plat no
				for(var i=0; i < data.length; i++){
					var vehicleNumber = data[i]['policenumber'];
					
					var dataRow = $("[relPoliceNumber="+vehicleNumber+"]");
					dataRow.addClass("animate-highlight");
					dataRow.find(".location").html(data[i]['location']['address']);
					dataRow.find(".speed").html(data[i]['speed']);
					
				}
				  
   
                  
			},
            complete:function(xhr, desc) {   
				$(".animate-highlight").removeClass("animate-highlight");
				setTimeout(function(){
				  updateGPSInformationBatch();
				}, 1000); 
             }
		}); 
	 }
	
		
	updateGPSInformationBatch();
//	 setInterval(updateGPSInformationBatch, 3000);
	
//     function updateGPSInformation(){  
//               
//		 
//         var rows = $("." + workOrderRowName); 
//         
//         if (fetchingIndex >= rows.length) fetchingIndex = 0;
//          
//         var currentRow = rows.eq(fetchingIndex);  
//		 
//         var registrationnumber = currentRow.find("[name=\'hidRegistrationNumber[]\']").val(); 
//           
//         if (fetching[registrationnumber]) return; 
//          
//         $.ajax({
//			type: "GET",
//            data : 'registrationnumber='+registrationnumber, 
//            url: baseURL + "ajax-gps.php",   
//            beforeSend : function() {
//                currentRow.addClass("text-blue-munsell");
//                fetching[registrationnumber] = true ;         
//            },
//			success: function(data){    
//				console.log(data);
//				
//                if (!data) return;
//				
//                 data = parseJSON(data); 
//				 if(data.length == 0)  return;
//					
//				data = data[0];
//                
//                var objLocation = currentRow.find(".location");
//                var objSpeed = currentRow.find(".speed");
//                
//                var address = data['location']['address'] || "";
//                var latitude = data['location']['latitude'] || "";
//                var longitude = data['location']['longitude'] || "";
//                var speed = data['speed']  || 0;
//    
//                objSpeed.html(speed);
//                
//                //console.log(objLocation.html() + " != " + location);
//                if (address && objLocation.html() != address){ 
//                    var el = objLocation,
//                    newone = el.clone(true);
//
//                    el.before(newone); 
//                    objLocation.remove();
//
//                    newone.html('<a href="../dashboard/maps?location='+latitude+','+longitude+'" target="_blank">'+address+'</a>');    
//                } 
//   
//                  
//			},
//            complete:function(xhr, desc) {    
//				
//                currentRow.removeClass("text-blue-munsell");
//                fetchingIndex++;
//                fetching[registrationnumber] = false;
//				
//				setTimeout(function(){
//				  updateGPSInformation();
//				}, 1000); 
//             }
//		}); 
//         
//    } 
	
//	updateGPSInformation();
  
//    setInterval(updateGPSInformation, 3000);

})  
</script>
</head>
<body> 
	
<div class="dashboard">
<div style="margin:2em">  
<!--
<div style="float:right; text-align:right">
    <div class="text-muted"><i>in collaboration with</i></div>
    <div style=" width: 150px; height: 40px; background-position:right; background-size:contain; background-repeat:no-repeat; background-image:url('/include/img/partners/accugps.png') "> </div>
    <div class="text-muted">beta version</div>
</div>    
-->
<div style="clear:both;height:1em"></div>    
<div class="div-table transaction-detail" style="width: 100%; font-size:1em">
<div class="div-table-row row-header bg-blue-steel text-white" style="font-family:Palanquin">  
    <div class="div-table-col" style="width: 5px;"></div> 
	<div class="div-table-col" style="width: 20px;"></div> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['jobOrder']); ?></div> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['WOCode']); ?></div> 
    <div class="div-table-col" style="width: 200px;"><?php echo strtoupper($obj->lang['customer']); ?></div> 
    <div class="div-table-col" style="width: 100px;"><?php echo strtoupper($obj->lang['carRegistrationNumber']); ?></div> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['driver']); ?></div> 
    <div class="div-table-col" style="width: 200px;"><?php echo strtoupper($obj->lang['route']); ?></div> 
    <div class="div-table-col" style="width: 60px; text-align:center">KM / H</div> 
    <div class="div-table-col"><?php echo strtoupper($obj->lang['location']); ?></div>
    <div class="div-table-col" style="width: 5px;"></div> 
</div>  
<div class="div-table-row row-template" >  
    <div class="div-table-col"><?php echo $obj->inputHidden('hidRegistrationNumber[]'); ?><?php echo $obj->inputHidden('hidGPSKey[]'); ?><?php echo $obj->inputHidden('hidTrackingId[]'); ?><?php echo $obj->inputHidden('hidProviderNamep[]'); ?></div> 
    <div class="div-table-col row-number" style="text-align:right"></div> 
    <div class="div-table-col socode"></div> 
    <div class="div-table-col wocode"></div> 
    <div class="div-table-col customername"></div> 
    <div class="div-table-col registrationnumber"></div> 
    <div class="div-table-col drivername"></div>
    <div class="div-table-col route"></div>  
    <div class="div-table-col speed" style=" text-align:center"></div>  
    <div class="div-table-col"><div class="location blink"></div></div>  
    <div class="div-table-col"></div> 
</div> 	
</div> 
</div>
    

</div>  
</body>    
</html>