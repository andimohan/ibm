<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
include '_global.php';

includeClass(array('CostRate.class.php','TruckingService.class.php','Location.class.php'));

$costRate = createObjAndAddToCol(new CostRate());
$truckingService = createObjAndAddToCol(new Service());
$warehouse = createObjAndAddToCol(new Warehouse());
$location = createObjAndAddToCol(new Location());

$obj = $costRate;
$securityObject = 'ReportCostRate'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(1,2);

// ===== FOR EXPORT SECTION
$dataToExport = array();
/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px" );
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"150px" );
$arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"200px" );
$arrDataStructure['location'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'location', 'width'=>"150px" );
$arrDataStructure['cargoType'] = array('title'=>ucwords($obj->lang['cargoType']),'dbfield' => 'cargotypename', 'width'=>"150px" );
$arrDataStructure['jobType'] = array('title'=>ucwords($obj->lang['jobType']),'dbfield' => 'jobtypename', 'width'=>"150px" );
$arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'costname', 'width'=>"150px" );
		    
$rsService = $truckingService->searchData($truckingService->tableName.'.statuskey',1,true, '','order by '.$truckingService->tableName.'.name asc');
foreach($rsService as $row => $value) 
    $arrDataStructure['itemprice'.$value['pkey']] = array('title'=> $value['name'] ,'align'=>'right', 'dbfield' => 'itemprice'.$value['pkey'],'format'=>'number', 'sortable' => false, 'width'=>"100px");

 

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['costRateReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
  

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
    	
	$criteria = '';
	$criteriaArr = array();
	
    // untuk pencarian berdasarkan kode
	array_push($criteriaArr, array('postVariable' => 'costCode', 
								   'fieldName' => $obj->tableName.'.code', 
								   'label' => $obj->lang['code']));
	  
	array_push($criteriaArr, array('postVariable' => 'selWarehouse', 
								   'fieldName' => $obj->tableName.'.warehousekey', 
								   'label' => $obj->lang['warehouse'], 
								   'useArrayKey' => array('obj' => $warehouse) ));

	array_push($criteriaArr, array('postVariable' => 'selLocation', 
								   'fieldName' => $obj->tableName.'.locationkey', 
								   'label' => $obj->lang['location'], 
								   'useArrayKey' => array('obj' => $location) ));
	  
	array_push($criteriaArr, array('postVariable' => 'selStatus',
								   'type' => 'status'));
 
	$obj->createReportCriteria($criteria,$arrFilterInformation,$criteriaArr);
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 
	$order = 'order by '.$orderBy.' ' .$orderType; 
		    
    // ambil dulu jensi2 cost, kalo langsung digabung sama biaya, yg ad double2 gara2 joinnya
	$rs = $obj->generateReportCostRate($criteria,$order,' group by '.$obj->tableName.'.pkey, '.$obj->tableNameDetail.'.jobtypekey, '.$obj->tableCostDetail.'.costkey');
	$rsPriceCol = $obj->generateReportCostRate($criteria,$order);
     
    $tempreport = ''; 
 
    $arrCost = array();
    for( $i=0;$i<count($rsPriceCol);$i++) 
        $arrCost[$rsPriceCol[$i]['refkey'].'-'.$rsPriceCol[$i]['itemkey'].'-'.$rsPriceCol[$i]['costkey']] = $rsPriceCol[$i]['price'];
     
    for( $i=0;$i<count($rs);$i++) {   
         
        if($rs[$i]['costkey'] == -1) 
            $rs[$i]['costname']  = $obj->lang['driverCommission'];
        else if($rs[$i]['costkey'] == -2) 
             $rs[$i]['costname']  = $obj->lang['codriverCommission'];
       
        foreach( $rsService as $key => $row) {
            $rs[$i]['itemprice'.$row['pkey']] = (isset($arrCost[$rs[$i]['refkey'].'-'.$row['pkey'].'-'.$rs[$i]['costkey']])) ? $arrCost[$rs[$i]['refkey'].'-'.$row['pkey'].'-'.$rs[$i]['costkey']] : 0;
        }
                                 
        
        $headerkey = $rs[$i]['refheaderkey'];
        $detailkey = $rs[$i]['refkey'];
        $costkey = $rs[$i]['costkey'];  


        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }
    
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
    
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');     
$arrLocation = $class->convertForCombobox($location->searchData($location->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name'); 


$arrTwigVar['inputCostRateCode'] =  $class->inputText('costCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelLocation'] =  $class->inputSelect('selLocation[]', $arrLocation, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
//$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportTruckingCostRate.html', $arrTwigVar);  
 
?>