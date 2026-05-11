<?php
require_once '../../_config.php';  
require_once '_include.php'; 

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';    

$truckingServiceOrder = new TruckingServiceOrder(); 

$OBJ = $truckingServiceOrder;


// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access"); 
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  

if($ACTION != 'GET') endForRequestMethodError(); 

$RETURN_VALUE = array();

// POST / PUT 
/*
$fileContent = file_get_contents("php://input");
parse_str($fileContent,$postVars);
*/

/* RECEIVE VALUE */  

$totalRows = isset($_GET['rowPerPage']) ? $_GET['rowPerPage']: 5;
$offset = isset($_GET['offset']) ? $_GET['offset'] : 1;

if($offset <= 0) $offset = 1;
 
$statuskey = json_encode(array('1','2','3','4','5','6')); // kalo yg dikirim user bukan array, convert ke array dulu

 
$criteria = array();

$statuskey = json_decode($statuskey);
array_push($criteria, ' and '.$OBJ->tableName.'.statuskey in ('.$OBJ->oDbCon->paramString($statuskey,',').')');

$orderBy = 'order by '. $OBJ->tableName.'.pkey desc';

$limitFrom = ($offset - 1) * $totalRows; 

$limit = ' limit '.$limitFrom.','.$totalRows; 

$OBJ->userkey = 1; // testing
$rs = $OBJ->searchData('','',true,implode(' and ',$criteria),$orderBy, $limit);

$rsStatus = $OBJ->getAllStatus();
$rsStatus = array_column($rsStatus, 'textcolor', 'pkey');

$RETURN_VALUE['response_code']  = 200; 
$RETURN_VALUE['data'] = array();
foreach($rs as $row){
    
    $arrDetail = array();
    
    $rsDetail = $OBJ->getDetailWithRelatedInformation($row['pkey']); 
     
    foreach($rsDetail as $detailRow){
        array_push($arrDetail, array(
                                        'service_name' => $detailRow['itemname'],
                                        'qty' => $detailRow['qtyinbaseunit'],
                                    ));
    }
    
    array_push($RETURN_VALUE['data'],array(
                                            'code' => $row['code'],
                                            'date' => $row['trdate'],
                                            'customer_name' => $row['customername'],
                                            'consignee_name' => $row['consigneename'],
                                            'route_from' => $row['routefrom'],
                                            'route_to' => $row['routeto'],
                                            'location_name' => $row['locationname'],
                                            'status_name' => $row['statusname'],
                                            'status_color' => $rsStatus[$row['statuskey']],
                                            'details' => $arrDetail,
                                        ) ); 
}

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>