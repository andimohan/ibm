<?php 
die ("gk tau masi kepake gk");

include '../../_config.php'; 
include '../../_include-v2.php';  


includeClass(array('TruckingServiceWorkOrder.class.php','GPSConnection.class.php','Car.class.php'));

$obj= new TruckingServiceWorkOrder();
$securityObject = $obj->securityObject; 
$gps = new GPSConnection();
$car = new Car();

if(!$security->isAdminLogin($securityObject,10,true)); 
 
$rsCar = $car->searchData($car->tableName.'.statuskey',1,true, 'and '.$car->tableName.'.gpstrackerid <> ""'); 
$arrGPSKey = array_column($rsCar,'gpskey');

$rsGPS = $gps->searchDataRow(array($gps->tableName.'.pkey',$gps->tableName.'.name'),
							' and '.$gps->tableName.'.pkey in ('.$this->oDbCon->paramString($arrGPSKey,',').')');
$rsGPS = array_column($rsGPS, null, 'pkey');

$arrAllVehicle = array();

foreach($arrGPSKey as $row){
	
	$gpsProviderName = $rsGPS[$row];
	
	$provider = strtolower($gpsProviderName['name']);
	$gpsObj = $this->getGPSObj($provider);
		
	$arrAllVehicle[$row] = $gpsObj->opt['getAllVehicle'];
			  
}

$obj->setLog($arrAllVehicle,true);

$arrCar = array();
foreach($rsCar as $carRow) {
    array_push($arrCar, array(
                                'pkey' => $carRow['pkey'],
                                'gpstrackerid' => $carRow['gpstrackerid'],
                                'getallvehicle' => $arrAllVehicle[$carRow['gpskey']] ,
                                'fetching' => false,
                            )
                );
}

?> 
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive-1.0.min.css" /> 

<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
<script>
jQuery(document).ready(function(){    
      
    var carArray = <?php echo json_encode($arrCar); ?>;
    var currentCarIndex = 0;
    var totalCar = carArray.length;
    
//    function updateGPSInformation(){  
//          
//		 
//        var cardataset = []; 
//        cardataset.push(carArray[currentCarIndex]);
//         
//        // kalo masi onprocess lewati saja
//        if (carArray[currentCarIndex]['fetching']) return;
//        
//        //console.log(currentCarIndex);
//        
//         $.ajax({
//			type: "GET",
//            data : 'cardataset='+ JSON.stringify(cardataset), 
//			url: "ajax-gps.php", 
//            beforeSend : function() {
//                //console.log(currentCarIndex);
//                carArray[currentCarIndex]['fetching'] = true;           
//            },
//			success: function(data){     
//                
//                if (!data) return;
//                 
//                data = JSON.parse(data); 
//                  
//                var i,carkey,address,longitude,latitude;
//                
//                carkey = data[0]['pkey']; 
//                address = data[0]['location']['address'];
//                longitude = data[0]['location']['address'];
//                latitude = data[0]['location']['address'];
//                
//                //console.log(currentCarIndex);
//                var objLocation = $("#" + carkey).find(".gps-location"); 
//                if (address && objLocation.html() != address){
//                    var el = objLocation,
//                    newone = el.clone(true);
//
//                    el.before(newone); 
//                    objLocation.remove();
//
//                    newone.html(address);    
//                }
//                  
//			},
//            complete:function(xhr, desc) {  
//                carArray[currentCarIndex]['fetching'] = false;
//                
//                currentCarIndex++;
//                if (currentCarIndex >= totalCar)
//                    currentCarIndex = 0;
//                 
//             }
//		}); 
//    } 

	
	function updateGPSInformation(){  
         
		var cardataset = []; 
		var cardata = [];
		
//		console.log(carArray);
		
		carArray = parseJSON(carArray);
		
		for(var i=0;i<carArray.length; i++){
//			console.log(cardataset);
			if (carArray[i]['getallvehicle'])
				cardataset.push(carArray[i]);
			else
				cardata.push(carArray[i]);
		}
			
//		console.log(cardataset);
//		console.log(cardata);
	
//		$.ajax({
//			type: "GET",
//            data : 'cardataset='+ JSON.stringify(cardataset), 
//			url: "ajax-gps.php", 
//            beforeSend : function() { 
//				// gk perlu, karena langsugn ke fetch
////                carArray[currentCarIndex]['fetching'] = true;         
//            },
//			success: function(data){     
//                
//                if (!data) return;
//                 
//                data = JSON.parse(data); 
//                  
//                var i,carkey,address,longitude,latitude;
//                
//                carkey = data[0]['pkey']; 
//                address = data[0]['location']['address'];
//                longitude = data[0]['location']['address'];
//                latitude = data[0]['location']['address'];
//                
//                //console.log(currentCarIndex);
//                var objLocation = $("#" + carkey).find(".gps-location"); 
//                if (address && objLocation.html() != address){
//                    var el = objLocation,
//                    newone = el.clone(true);
//
//                    el.before(newone); 
//                    objLocation.remove();
//
//                    newone.html(address);    
//                }
//                  
//			},
//            complete:function(xhr, desc) {  
//                carArray[currentCarIndex]['fetching'] = false;
//                
//                currentCarIndex++;
//                if (currentCarIndex >= totalCar)
//                    currentCarIndex = 0;
//                 
//             }
//		}); 
		  
    } 
  
	
	
//   updateGPSInformation();     
   setInterval(updateGPSInformation, 2000);

})  
</script>
</head>
<body> 
<div style="height:2em"></div>      
<div class="div-table transaction-detail" style="width: 96%; margin:auto; font-size: 2em">
<div class="div-table-row row-header bg-blue-steel text-white" style="font-family:Palanquin"> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['carRegistrationNumber']); ?></div> 
    <div class="div-table-col"><?php echo  strtoupper($obj->lang['location']); ?></div>
</div> 
<?php foreach ($rsCar as $carRow) {?>
    <div id="<?php echo $carRow['pkey']; ?>" class="div-table-row"> 
    <div class="div-table-col car-registration-number" style="width: 100px;"><?php echo $carRow['policenumber']; ?></div> 
    <div class="div-table-col"><div class="gps-location blink"></div></div>
</div>
<?php } ?>
</div> 
 
</body>    
</html>