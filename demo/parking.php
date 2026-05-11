<?php
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('TruckingServiceWorkOrder.class.php','GPSConnection.class.php'));

$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$GPSConnection = new GPSConnection();
$car = new Car();
$obj = $truckingServiceWorkOrder;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

//$arrGeofenceCode = array("muarabaru");

function convertToHumanReadable($seconds){
    $hours = floor($seconds / 3600);
    $mins = floor($seconds / 60 % 60);
    $secs = floor($seconds % 60);
    
    return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
}
   
if(!isset($_POST) || empty($_POST['btnSubmit'])){
	$_POST['trDate'] = date('d / m / Y');
}else{
    $rsParking = $GPSConnection->getParkingData(array('startDate'=>$_POST['trDate'], 'endDate' =>$_POST['trDate'] )); 
}

?>
<html>
  <head>
    <title>Parking Calculation Demo</title>
	 <style>
		.div-table{display:table}
		.div-table .div-table-caption {display:table-caption}
		.div-table-col{display:table-cell; vertical-align:top;}
		.div-table-col-3{display:table-cell; vertical-align:middle; padding:0.3em; }
		.div-table-col-5{display:table-cell; vertical-align:middle; padding:0.5em; }
		.div-table-col-03 {display:table-cell; vertical-align:top; padding:0 0.3em;}
		.div-table-col-05{display:table-cell; vertical-align:top; padding:0 0.5em;} 
		.div-table .col-header{ border:1px solid #333;  border-left:0; border-right:0; font-weight: bold} 
		.div-table .row-bb{ border-bottom:1px solid #dedede; } 
		.div-table-row{display:table-row;}
		 
		 .table-data .div-table-col-3{border:1px solid #333}

	  </style>
    <link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
  	<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   
	<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js" charset="utf-8"></script> 
  	<script>
	  jQuery(document).ready(function(){  
		  $(".input-date").datepicker({ showButtonPanel: true, 
                                                  currentText: 'Now', 
                                                  dateFormat:'dd / mm / yy', 
                                                  changeMonth: true,  
                                                  changeYear: true,
                                                  beforeShow : function(input, inst) {  
                                                            inst.dpDiv.removeClass('month-year-datepicker');
                                                        }
                                                    }
                                               );  
	  });
	 
	</script>
  </head>
  <body>
	 <form action="parking.php" method="post" enctype="multipart/form-data"  id="form-import"> 
     <?php 
	  	echo $obj->inputDate('trDate', array('etc' => 'style="text-align:center"'));
	  	echo $obj->inputSubmit('btnSubmit', $obj->lang['submit'], array('class' => 'btn btn-primary btn-second-tone'));
	  
	  ?>
	  	</form>
	  
	  <div>
	  	
	  	<?php  foreach($rsParking as $key=>$row){  ?>
                <?php echo $key; ?>
                <div class="div-table table-data">   
                <?php  foreach($row as $parkingRow){  ?>
				<div class="div-table-row">
					<div class="div-table-col-3" style="width: 5em" ><?php echo $parkingRow['vehiclecode']; ?></div>
					<div class="div-table-col-3" style="width: 7em" ><?php echo $car->normalizePoliceNumber($parkingRow['policenumber']); ?></div>
					<div class="div-table-col-3" style="width: 15em"  ><?php echo $parkingRow['geoname']; ?></div>
					<div class="div-table-col-3" style="width: 4em"  ><?php echo convertToHumanReadable($parkingRow['parkingduration']); ?></div> 
					<div class="div-table-col-3" style="width: 4em; <?php echo ($parkingRow['parkingduration'] != $parkingRow['parkingdurationfromprevday']) ? 'color: #f00' : ''; ?>"  ><?php echo convertToHumanReadable($parkingRow['parkingdurationfromprevday']); ?></div> 
			     </div>	
            <?php }  ?>
                </div>
          <br><br>
		 <?php }  ?>
	  </div>
  </body>
</html>