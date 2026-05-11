<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';

$obj = $truckingCostCashOut;
$securityObject = 'reportTruckingCostCashOut'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(2,3); 


// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;

// ====================== must be set before TWIG

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['cashOutDate']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['orderdate'] = array('title'=>ucwords($obj->lang['jobDate']),'dbfield' => 'refdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['joCode'] = array('title'=>ucwords($obj->lang['JOCode']),'dbfield' => 'jobordercode', 'width'=>"120px");
$arrDataStructure['workOrderCode'] = array('title'=>ucwords($obj->lang['WOCode']),'dbfield' => 'workordercode', 'width'=>"120px");
$arrDataStructure['services'] = array('title'=>ucwords($obj->lang['services']),'dbfield' => 'servicename', 'width'=>"150px"); 
$arrDataStructure['costname'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'costname', 'width'=>"150px"); 
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['recipient']),'dbfield' => 'employeename', 'width'=>"150px");
$arrDataStructure['car'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"110px"); 
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px"); 
$arrDataStructure['containerNumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'containernumber', 'width'=>"150px", "sortable" => false);
$arrDataStructure['cargotype'] = array('title'=>ucwords($obj->lang['cargoType']),'dbfield' => 'cargotype', 'width'=>"100px",);
$arrDataStructure['jobtype'] = array('title'=>ucwords($obj->lang['jobType']),'dbfield' => 'jobtypename', 'width'=>"130px",);
$arrDataStructure['location'] = array('title'=>ucwords($obj->lang['location']),'dbfield' => 'locationname', 'width'=>"110px"); 
$arrDataStructure['route'] = array('title'=>ucwords($obj->lang['route']),'dbfield' => 'route', 'width'=>"150px"); 
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['credit']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'number', 'calculateTotal' => true);
//$arrDataStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balance', 'width'=>"100px" ,'format'=>'number', "sortable" => false);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
 
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['truckingCashFlowReportReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1)'),'pkey','name'); 
 
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and ' . $chartOfAccount->tableName .'.isleaf = 1' ),'pkey','coaname');
$arrTwigVar['inputCOAName'] =  $class->inputSelect('selCOA[]', $arrCOA, array( 'etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputTruckingCostCashOutCode'] =  $class->inputText('truckingCostCashOutCode');  
$arrTwigVar['inputHidDriverKey'] =  $class->inputHidden('hidDriverKey');
$arrTwigVar['inputDriverName'] =  $class->inputText('driverName');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputPlannerName'] =  $class->inputText('plannerName'); 
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['truckingCostCashOutCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['truckingCostCashOutCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['truckingCostCashOutCode']));
	}
	
	if(isset($_POST) && !empty($_POST['refCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref', 'filter' => $_POST['refCode']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and  '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
	if(isset($_POST) && !empty($_POST['driverName'])) { 
        $criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['driverName'].'%').' )';
	    array_push($arrFilterInformation,array("label" => 'Driver', 'filter' => $_POST['driverName']));
	}
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}
     
    
    if(isset($_POST) && !empty($_POST['selCOA'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCOA']));   
        
       	$criteria .= ' AND '.$obj->tableNameDetail.'.coakey in('.$key.')';  
 
        $rsCOA = $chartOfAccount->searchData('','',true, ' and '.$chartOfAccount->tableName.'.pkey in ('.$key.')');
			
		$coaName = implode(", ",array_column($rsCOA, 'coaname')); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['cashBank'], 'filter' => $coaName));
	}
    
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
	
	$order = 'order by '.$orderCriteria['orderBy'].' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc'); 
    $rs = $obj->generateCostFlowReport($criteria,$order); 
    
    /* 
	$rsJOKey =  $obj->getTableKeyAndObj($obj->tableSalesOrder); 
    $joTableTypeKey = $rsJOKey['key'];
	$rsWOKey =  $obj->getTableKeyAndObj($obj->tableSalesWorkOrder); 
    $woTableTypeKey = $rsWOKey['key'];
    
    $rsJO = array_filter($rs, function($item) {
        global $joTableTypeKey;
        //$obj->setLog($item['reftabletype'] .' == '.$joTableTypeKey);
        return $item['reftabletype'] == $joTableTypeKey;
    });
    
    $rsWO = array_filter($rs, function($item) {
        global $woTableTypeKey;
        //$obj->setLog($item['reftabletype'] .' == '.$woTableTypeKey);
        return $item['reftabletype'] == $woTableTypeKey;
    });
    
    $obj->setLog($rsWO);*/
    
    $tempreport = ''; 	
    for( $i=0;$i<count($rs);$i++) {   
        
            $arrContainer = array();
            if (!empty($rs[$i]['containernumber']))  array_push($arrContainer, $rs[$i]['containernumber']);
            if (!empty($rs[$i]['container2number']))  array_push($arrContainer, $rs[$i]['container2number']);
        
            $rs[$i]['containernumber'] = implode(', ', $arrContainer);
                
            //$truckingServiceOrder = new TruckingServiceOrder();
           /* if($rs[$i]['reftabletype'] == $rsWOKey['key']){
                $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
                
              
                //$rsSo = $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.pkey',$rsWO[0]['refkey'],true);
                //$rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($rsWO[0]['refkey'],' and '.$truckingServiceOrder->tableNameDetail.'.pkey = '.$rsWO[0]['refdetailkey']);
                //$rs[$i]['servicename'] = $rsSODetail[0]['label'];
                //$rs[$i]['orderdate'] = $rsSo[0]['trdate'];
                //$rs[$i]['customername'] = $rsSo[0]['customername'];
                //$rs[$i]['trdesc'] = $rsSo[0]['categoryname'];
                //$rs[$i]['policecode'] = $rsWO[0]['policecode'];
                //$rs[$i]['containernumber'] = $containerNumber;
            } else {
                //$rs[$i]['servicename'] = '';
                //$rsSO = $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.pkey',$rs[$i]['refkey'],true);
                //$rs[$i]['customername'] = $rsSO[0]['customername'];
                //$rs[$i]['trdesc'] = $rsSO[0]['categoryname'];
                //$rs[$i]['policecode'] = '';
                //$rs[$i]['containernumber'] = '';
            }   */
			     
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];
        
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
    } 
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
}


echo $twig->render('reportTruckingCashFlow.html', $arrTwigVar);   

?> 