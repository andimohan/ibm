<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("TruckingServiceOrder.class.php"));
$truckingServiceOrder = new TruckingServiceOrder();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();

$truckingServiceOrder->oDbCon =  $CUSTOMER_CONN;
$truckingServiceWorkOrder->oDbCon =  $CUSTOMER_CONN;

// pastikan mobil masih terhubung dengan spk customer
// yg dikirim spk id

if (!isset($_GET['spk']) && empty($_GET['spk'])) die;


$rsCust = $customer->searchDataRow(array('showgpslocation'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria = ''; 
$criteria .=  ' and '.$truckingServiceOrder->tableName.'.customerkey = '.$class->oDbCon->paramString(USERKEY);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$truckingServiceOrder->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').' or 
						'.$truckingServiceOrder->tableConsignee.'.name like '.$class->oDbCon->paramString('%'.$key.'%').' or 
						'.$truckingServiceOrder->tableName.'.routefrom like '.$class->oDbCon->paramString('%'.$key.'%').' or 
						'.$truckingServiceOrder->tableName.'.routeto like '.$class->oDbCon->paramString('%'.$key.'%').' 
					)'
			  ;
}


// STATUS
$statuskeyCriteria = array();

if(isset($_POST['hidStatusCriteria']) && !empty($_POST['hidStatusCriteria'])){
	$arrStatusKey = explode(',',$_POST['hidStatusCriteria']);
	
	foreach($arrStatusKey as $row){
		if($row == 1){
			array_push($statuskeyCriteria,2);
		}else if ($row == 2){ 
			array_push($statuskeyCriteria,3);
			array_push($statuskeyCriteria,4);
			array_push($statuskeyCriteria,5);
		}else if ($row == 3){  
			array_push($statuskeyCriteria,6);
		} 	
	} 
}

// buat jaga2 kalo gk dicentang semua jg
if(empty($statuskeyCriteria))
	$statuskeyCriteria = array(2,3,4,5,6);

$criteria .=  ' and '.$truckingServiceOrder->tableName.'.statuskey in ('.$class->oDbCon->paramString($statuskeyCriteria,',').')';


// TGL
if(isset($_POST['chkDatePeriodFilter']) && $_POST['chkDatePeriodFilter'] == 1){
	$fromDate = (isset($_POST['trStartDatePeriod']) && !empty($_POST['trStartDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	$endDate = (isset($_POST['trEndDatePeriod']) && !empty($_POST['trEndDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	
	$criteria .=  ' and '.$truckingServiceOrder->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDatePeriod'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDatePeriod'],' / ','Y-m-d 23:59');
}


$rs = $truckingServiceOrder->searchData('','',true,$criteria,$orderBy,$limit);

$arrJoKey = array_column($rs,'pkey');


$rsPartyCol = $truckingServiceOrder->getPartyDescription($arrJoKey);

$criteriaSPK =   ' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)';
$criteriaSPK .=  ' and '.$truckingServiceWorkOrder->tableName.'.refkey in ('.$class->oDbCon->paramString($arrJoKey,',').' )';

$rsSPK = $truckingServiceWorkOrder->searchData('','',true,$criteriaSPK);

for($j = 0 ;$j<count($rsSPK);$j++){
    $rsSPK[$j]['vehicle'] = ($rsSPK[$j]['isoutsource'] == 1) ? $rsSPK[$j]['outsourcecarregistrationnumber'] : $rsSPK[$j]['policenumber'];
}

$rsSPKCol = $class->reindexDetailCollections($rsSPK,'refkey');

for($i=0;$i<count($rs);$i++){ 
    
    $joKey =$rs[$i]['pkey'];
    $vehicleNumber = (isset($rsSPKCol[$joKey])) ? $rsSPKCol[$joKey] : array();
	
	//	$class->setLog(array_column($vehicleNumber, 'policenumber'),true);
    
    $rs[$i]['party']  = isset($rsPartyCol[$joKey])? $rsPartyCol[$joKey] : ''; 
	
	// utk hilangin yg gk diisi plat no nya Trcuking luar
	// utk non gps
	
	$vehicleNumber = array_filter(array_column($vehicleNumber, 'vehicle'));
    $rs[$i]['vehiclenumber']  =  implode(', ',$vehicleNumber); 
	
	$rs[$i]['gpsdetail'] = array();
	foreach($vehicleNumber as $vehicleRow){
		array_push( $rs[$i]['gpsdetail'], array('vehiclenumber'  => $vehicleRow ,
												'vehicleid' => str_replace(' ','',$vehicleRow) ));
	}
	 
	
	$rs[$i]['statusname'] =  $endUserStatusName[$rs[$i]['statuskey']];
}

$totalPages = ceil( $truckingServiceOrder->getTotalRows($criteria) / $totalrowsperpage); 
$arrTwigVar ['totalPages'] =  $totalPages;

$arrTwigVar['rsJobOrder'] =   $rs;  

$arrStatus = array();
array_push($arrStatus, array('pkey' => 1, 'status' => $class->lang['onDelivery']));
array_push($arrStatus, array('pkey' => 2, 'status' => $class->lang['documentCollection']));
array_push($arrStatus, array('pkey' => 3, 'status' => $class->lang['invoiced']));

$arrChkStatus = array();
$arrPostStatuskey = (isset($_POST['hidStatusCriteria'])) ? explode(',',$_POST['hidStatusCriteria']) : array();
foreach($arrStatus as $row){
	$chkValue = (in_array($row['pkey'], $arrPostStatuskey)) ? 1:0;
	array_push($arrChkStatus, array('input' => $class->inputCheckBox('chkStatus[]',array('value' => $chkValue, 'etc' => 'attr="'. $row['pkey'] .'"')),'label' => $row['status']));
}

$arrTwigVar['inputChkStatus'] =  $arrChkStatus;
$arrTwigVar['showGPSLocation'] =  $rsCust[0]['showgpslocation'];

$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('job-order-list.html', $arrTwigVar);

?>