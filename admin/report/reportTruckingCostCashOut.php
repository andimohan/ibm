<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('TruckingCostCashOut.class.php'); 
$truckingCostCashOut = createObjAndAddToCol(new TruckingCostCashOut());

$truckingServiceOrder =  createObjAndAddToCol(new TruckingServiceOrder());
$warehouse = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());

include '_global.php';

$obj = $truckingCostCashOut;
$securityObject = 'reportTruckingCostCashOut'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrDateType= array(
    '1' => $obj->lang['JOWODate'], 
    '2' => $obj->lang['submissionDate'],  
    '3' => $obj->lang['confirmedDate']
);

$arrFilterInformation = array();
$detailCriteria = ''; 
$_POST['selStatus[]'] = array(2,3,4); 
if(!isset($_POST['isGrouping']))  $_POST['isGrouping'] = 1;
if(!isset($_POST['isShowDetail']))  $_POST['isShowDetail'] = 0;


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
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"120px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['JOWODate']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['stuffingDate'] = array('title'=>ucwords($obj->lang['stuffingDate']),'dbfield' => 'lastwodate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['submissionDate'] = array('title'=>ucwords($obj->lang['submissionDate']),'dbfield' => 'createdon', 'width'=>"120px",'format'=>'date');
$arrDataStructure['confirmedDate'] = array('title'=>ucwords($obj->lang['confirmedDate']), 'align'=>'center', 'width'=>"120px", 'dbfield'=>'confirmedon','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''), 'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'donumber', 'width'=>"100px");

if(!$isGrouping){ 
    $arrDataStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),  'width'=>"200px", 'dbfield' => 'costname');
    $arrDataStructure['coalink'] = array('title'=>ucwords($obj->lang['cashBankAccount']),'dbfield' => 'coaname', 'width'=>"200px"); 
}

$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['employee']),'dbfield' => 'employeename', 'width'=>"150px");
$arrDataStructure['refCode'] = array('title'=>ucwords($obj->lang['reference']). ' 1',  'width'=>"150px", 'dbfield' => 'refcode');
$arrDataStructure['refCode2'] = array('title'=>ucwords($obj->lang['reference']). ' 2',  'width'=>"150px", 'dbfield' => 'refcode2');
$arrDataStructure['party'] = array('title'=>ucwords($obj->lang['party']),  'width'=>"140px", 'dbfield' => 'party');

$arrDataStructure['carRegistrationNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'width'=>"100px", 'dbfield' => 'policenumber');
//$arrDataStructure['jobdescription'] = array('title'=>ucwords($obj->lang['jobDescription']),'width'=>"200px" ,'dbfield' => 'jobdescription');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'width'=>"200px" ,'dbfield' => 'trdesc');

if(!$isGrouping) {
    $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'amount', 'width'=>"100px" ,'format'=>'number', 'calculateTotal' => true);
}else{ 
    $arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal', 'width'=>"100px" ,'format'=>'number','calculateTotal' => true);
    $arrDataStructure['aremployee'] = array('title'=>ucwords($obj->lang['employeeAR']),'dbfield' => 'aremployee', 'width'=>"150px" ,'format'=>'number','calculateTotal' => true);
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
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType ); 
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
$arrTwigVar['inputDoNumber'] =  $class->inputText('doNumber');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
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
        
        switch($_POST['selDateType']){
            case '1' : $dateField = $obj->tableName.'.trdate'; break;
            case '2' : $dateField = $obj->tableName.'.trsubmissiondate';  break;  
            case '3' : $dateField = $obj->tableName.'.confirmedon';  break;  
        }
	  
		$criteria .= ' and  '.$dateField.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
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
    
    if(isset($_POST) && !empty($_POST['doNumber'])) {
		$criteria .= ' AND ('.$truckingServiceOrder->tableName.'.donumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['doNumber'].'%').') )';
		//$criteria .= ' AND  donumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['doNumber'].'%').') ';
		array_push($arrFilterInformation,array("label" => 'S/I', 'filter' => $_POST['doNumber']));
	}
	
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND ('.$truckingServiceOrder->tableName.'.customerkey in('.$key.'))';   
       	//$criteria .= ' AND customerkey in('.$key.') ';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
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
    //$rs = (!$isGrouping) ? $obj->generateCostReport($criteria,$order) :  $obj->searchData('','',true,$criteria,$order);
    $rs = ($isGrouping) ?  $obj->searchData('','',true,$criteria,$order):  $obj->generateCostReport($criteria,$order);
       
    $tempreport = ''; 
		
    $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();
     
    $totalRs = count($rs);
    $arrsokey = array();
    for( $i=0;$i<$totalRs;$i++) { 
         $sokey = (!empty($rs[$i]['refkey2'])) ? $rs[$i]['refkey2'] : $rs[$i]['refkey'];
         array_push($arrsokey,$sokey);
    }
     
    $rsParty= $truckingServiceOrder->getPartyDescription($arrsokey);      
    
    for( $i=0;$i<$totalRs;$i++) {    
            $sokey = (!empty($rs[$i]['refkey2'])) ? $rs[$i]['refkey2'] : $rs[$i]['refkey']; 
            $rs[$i]['party'] = (isset($rsParty[$sokey])) ? $rsParty[$sokey] : '';
                
            if($isGrouping && $isShowDetail){
               // $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria);  
                $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
                if (empty($rsDetail)) continue;

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
