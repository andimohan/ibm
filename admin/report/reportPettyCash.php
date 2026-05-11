<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('PettyCash.class.php');
$pettyCash = createObjAndAddToCol( new PettyCash()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 

include '_global.php';

$obj = $pettyCash;

$securityObject = 'reportPettyCash'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 

// $_POST['selStatus[]'] = array(2,3);
$arrFilterInformation = array();
$detailCriteria = ''; 

$customCodeInactiveCriteria = '';


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
// $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px", 'format'=>'date', "sortable" => false);
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px", "sortable" => false);
$arrDataStructure['doNumber'] = array('title'=>ucwords($obj->lang['si']).' / DO','dbfield' => 'donumber', 'width'=>"170px", "sortable" => false);
$arrDataStructure['workOrder'] = array('title'=>ucwords($obj->lang['workOrder']),'dbfield' => 'isspk', 'width'=>"50px", "sortable" => false);
$arrDataStructure['costName'] = array('title'=>ucwords($obj->lang['costName']),'dbfield' => 'costname', 'width'=>"150px", "sortable" => false);
$arrDataStructure['from'] = array('title'=>'Pick Up Point','dbfield' => 'stuffinglocationfromname', 'width'=>"150px", "sortable" => false);
$arrDataStructure['to'] = array('title'=>'Area Terjauh','dbfield' => 'stuffinglocationname', 'width'=>"150px", "sortable" => false);
$arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'servicename', 'width'=>"100px", "sortable" => false);
$arrDataStructure['car'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"100px" , "sortable" => false );
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivernamedesc', 'width'=>"100px" , "sortable" => false );
$arrDataStructure['codriver'] = array('title'=>ucwords($obj->lang['codriver']),'dbfield' => 'codrivernamedesc', 'width'=>"100px" , "sortable" => false );
$arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"200px", "sortable" => false);
$arrDataStructure['outsource'] = array('title'=>ucwords($obj->lang['outsource']),'dbfield' => 'isoutsource', 'width'=>"60px", "sortable" => false);
$arrDataStructure['multi'] = array('title'=>'Multi','dbfield' => 'qtymulti', 'width'=>"60px" ,'format'=>'number', 'textColor' => '568203', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['desc'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"250px" , "sortable" => false);
$arrDataStructure['debit'] = array('title'=>ucwords($obj->lang['debit']),'dbfield' => 'debit', 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['credit'] = array('title'=>ucwords($obj->lang['credit']),'dbfield' => 'credit', 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', 'calculateTotal' => true, "sortable" => false);
$arrDataStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balance', 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', 'calculateTotal' => false, "sortable" => false);
//$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
// $arrDataStructure['coa'] = array('title'=>ucwords($obj->lang['account']),'dbfield' => 'codename',  'width'=>"160px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['pettyCashReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selCOA'])) { 
        
        // $key = implode(",", $class->oDbCon->paramString($_POST['selCOA']));   
        
       	$criteria .= ' AND coakey in('.$_POST['selCOA'].')';  
        
        $rsCriteria = $chartOfAccount->searchData('','',true, ' and '.$chartOfAccount->tableName.'.pkey in ('.$_POST['selCOA'].')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['coaname']);
			
		$coaName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Akun', 'filter' => $coaName ));
        
	}
    
   if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
        $criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');

        $arrTempStatus = array();
		  for ($k=0;$k<count($rsCriteria);$k++)
		  array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		 $warehouseName = implode(", ",$arrTempStatus); 
	     array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $warehouseName ));
        
	} 
	
	// if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
    //     $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
    //    	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

    //     $rsCriteria =  $obj->getStatusById($key);
	 
    //     $arrTempStatus = array();
	// 	for ($k=0;$k<count($rsCriteria);$k++)
	// 	 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
	// 	$statusName = implode(", ",$arrTempStatus); 
	//     array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	// }
	
    $orderBy = 'trdate,pkey'; //(!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = 'asc';// (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	  
    $rs = $obj->searchData('','',true,$criteria,$order);
    $arrStartingBalance = $obj->sumAccount($_POST['selCOA'],'',$_POST['trStartDate']); 
    $balance = (!empty($arrStartingBalance)) ? $arrStartingBalance['balance'] : 0 ;
    $startingBalance = $balance;

    $tempreport = ''; 
    
    
 // ============================= GENERATE DATA ============================= 
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   

        $rs[$i]['credit'] += $rs[$i]['settlementamount'];
        
        $balance = $balance + $rs[$i]['debit'] - $rs[$i]['credit'];

        // if($rs[$i]['isdownpayment'] == 1) {
        //     $balance -= $rs[$i]['settlementamount'];
        // }

        $rs[$i]['balance'] = $balance;
        $rs[$i]['policenumber'] = ($rs[$i]['isoutsource'] == 1) ? $rs[$i]['caroutsource'] : $rs[$i]['policenumber'];
        $rs[$i]['isoutsource'] = ($rs[$i]['isoutsource'] == 1) ? "<i class=\"fas fa-check text-green-avocado\"></i>" : "";
        $rs[$i]['isspk'] = ($rs[$i]['isspk'] == 1) ? "<i class=\"fas fa-check text-green-avocado\"></i>" : "";
        $return = $obj->formatReportRows(array('data'=>$rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    } 
    if (!empty($rs)) {
        array_push($arrFilterInformation,array("label" => 'Balance', 'filter' => $obj->formatNumber($startingBalance).' - '.$obj->formatNumber($balance)));
    }
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and iscashbank = 1','order by coaname asc'),'pkey','coaname');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
// $arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
// $arrTwigVar['inputSelCOA'] =  $class->inputSelect('selCOA[]', $arrCOA, array('class' => 'multi-selectbox'));
$arrTwigVar['inputSelCOA'] =  $class->inputSelect('selCOA', $arrCOA);  
$arrTwigVar['inputCashBankCode'] =  $class->inputText('cashBankCode');
$arrTwigVar['inputRefCode'] =  $class->inputText('refCode');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportPettyCash.html', $arrTwigVar);   

?>