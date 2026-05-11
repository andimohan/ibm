<?php 

$obj = $truckingCostCashOut;
$securityObject = 'reportTruckingCostCashOut'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(2,3); 
if(!isset($_POST['isGrouping']))  $_POST['isGrouping'] = 1;
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 1;


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
$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;
$isShowDetail = (isset($_POST['isShowDetail']) && $_POST['isShowDetail'] == 1) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
 
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['employee']),'dbfield' => 'employeename', 'width'=>"150px");
$arrDataStructure['refCode'] = array('title'=>ucwords($obj->lang['reference']). ' 1',  'width'=>"150px", 'dbfield' => 'refcode');
$arrDataStructure['refCode2'] = array('title'=>ucwords($obj->lang['reference']). ' 2',  'width'=>"150px", 'dbfield' => 'refcode2');
$arrDataStructure['party'] = array('title'=>ucwords($obj->lang['party']),  'width'=>"100px", 'dbfield' => 'party');

$arrDataStructure['carRegistrationNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'width'=>"100px", 'dbfield' => 'policenumber');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'width'=>"200px" ,'dbfield' => 'trdesc');

if(!$isGrouping) {
    $arrDataStructure['coalink'] = array('title'=>ucwords($obj->lang['cashBankAccount']),'dbfield' => 'coaname', 'width'=>"200px"); 
    $arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'width'=>"200px", 'dbfield' => 'costname');
    $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'number', 'calculateTotal' => true);
}else{ 
    $arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
}

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
 
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['truckingCostCashOutReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping && $isShowDetail){ 
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'dbfield' => 'costname', 'mergeExcelCell' => 2);  
    $arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"120px", 'format' => 'number','calculateTotal' => true ); 

    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = "700px";
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1)'),'pkey','name'); 
 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputTruckingCostCashOutCode'] =  $class->inputText('truckingCostCashOutCode'); 
$arrTwigVar['inputRefCode2'] =  $class->inputText('refCode2'); 
$arrTwigVar['inputRefCode'] =  $class->inputText('refCode'); 
$arrTwigVar['inputHidDriverKey'] =  $class->inputHidden('hidDriverKey');
$arrTwigVar['inputDriverName'] =  $class->inputText('driverName');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidPlannerKey'] =  $class->inputHidden('hidPlannerKey');
$arrTwigVar['inputPlannerName'] =  $class->inputText('plannerName'); 
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping'); 
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
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
	
	if(isset($_POST) && !empty($_POST['refCode2'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode2 LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode2'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref2', 'filter' => $_POST['refCode2']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and  '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
	if(isset($_POST) && !empty($_POST['driverName'])) { 
        $criteria .= ' AND '.$obj->tableEmployee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['driverName'].'%').' )';
	    array_push($arrFilterInformation,array("label" => 'Driver', 'filter' => $_POST['driverName']));
	}
    
	/*if(isset($_POST) && !empty($_POST['plannerName'])) { 
        $criteria .= ' AND tablePlanner.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['plannerName'].'%').' )';
	    array_push($arrFilterInformation,array("label" => 'Planner', 'filter' => $_POST['plannerName']));
	}*/
    
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
    $rs = (!$isGrouping) ? $obj->generateCostReport($criteria,$order) :  $obj->searchData('','',true,$criteria,$order);
       
    $tempreport = ''; 
		
    for( $i=0;$i<count($rs);$i++) {    
            $sokey = (!empty($rs[$i]['refkey2'])) ? $rs[$i]['refkey2'] : $rs[$i]['refkey'];
            $rs[$i]['party'] = $truckingServiceOrder->getPartyDescription($sokey);      
        
            if($isGrouping && $isShowDetail){
                $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
                if (empty($rsDetail))
                    continue;

                // has detail
                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
            }
            
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


echo $twig->render('reportTruckingCostCashOut.html', $arrTwigVar);   

?> 