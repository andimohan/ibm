<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('CashBank.class.php');
$cashBank = createObjAndAddToCol( new CashBank()); 
$carServiceMaintenance = createObjAndAddToCol( new CarServiceMaintenance()); 
$cashBankTransfer = createObjAndAddToCol( new CashBankTransfer()); 
$purchaseOrder = createObjAndAddToCol( new PurchaseOrder()); 

$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 
$carServiceMaintenance = createObjAndAddToCol( new CarServiceMaintenance()); 
$purchaseOrder = createObjAndAddToCol( new PurchaseOrder()); 
$cashBankTransfer = createObjAndAddToCol( new CashBankTransfer()); 
$cashOut = createObjAndAddToCol( new CashOut()); 
$cashIn = createObjAndAddToCol( new CashIn());

include '_global.php';

$obj = $cashBank;

$securityObject = 'reportCashBankMaintenance'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 

$_POST['selStatus[]'] = array(2,3);
$arrFilterInformation = array();
$detailCriteria = ''; 

$customCodeInactiveCriteria = '';


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date'); 
$arrDataStructure['refCode'] = array('title'=>ucwords($obj->lang['refCode']),'dbfield' => 'refcode', 'width'=>"150px", "sortable" => false);
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" , "sortable" => false ); 
$arrDataStructure['policenumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber', 'width'=>"80px", "sortable" => false  );
$arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"150px", "sortable" => false  );
$arrDataStructure['openingBalance'] = array('title'=>ucwords($obj->lang['openingBalance']),'dbfield' => 'openingBalance', 'width'=>"120px" ,'format'=>'number' );
$arrDataStructure['debit'] = array('title'=>ucwords($obj->lang['debit']),'dbfield' => 'debit', 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', 'calculateTotal' => true);
$arrDataStructure['credit'] = array('title'=>ucwords($obj->lang['credit']),'dbfield' => 'credit', 'width'=>"120px" ,'format'=>'number', 'textColor' => 'C41E3A','calculateTotal' => true);
$arrDataStructure['balance'] = array('title'=>ucwords($obj->lang['endingBalance']),'dbfield' => 'balance', 'width'=>"120px" ,'format'=>'number' );
$arrDataStructure['transaction'] = array('title'=>ucwords($obj->lang['transactionType']),'dbfield' => 'transactiontypename',  'width'=>"120px");
$arrDataStructure['desc'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"250px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['maintenanceCashVoucherReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

/*	if(isset($_POST) && !empty($_POST['cashBankCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['cashBankCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['cashBankCode']));
	}*/
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['refCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.refcode LIKE ('.$class->oDbCon->paramString('%'.$_POST['refCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Ref.', 'filter' => $_POST['refCode']));
	} 
     
    if(isset($_POST) && !empty($_POST['selCOA'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCOA']));   
        
       	$criteria .= ' AND coakey in('.$key.')';  
        
        $rsCriteria = $chartOfAccount->searchData('','',true, ' and '.$chartOfAccount->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['coaname']);
			
		$coaName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Akun', 'filter' => $coaName ));
        
	}
    
      if(isset($_POST) && !empty($_POST['selCashBankTransactionType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCashBankTransactionType']));    
        $criteria .= ' AND transactiontypekey in ('.$key.')';  
            
        $arrTempStatus = array();
          
        $cashBankType = $_POST['selCashBankTransactionType'];
        foreach ($cashBankType as $typeRow){ 
            $rsType =  $obj->getTransactionType($typeRow);
            array_push($arrTempStatus,$rsType[0]['name']);
        }
			
		$cashBankType = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Jenis Transaksi', 'filter' => $cashBankType ));
        
	}  
	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
	
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
    //$cashBank->setTimeLog('search data',true);
    
    // biar gk keload kalo gk milih ap2
	$rs = (isset($_POST['selCOA']) && !empty($_POST['selCOA'])) ? $obj->searchData('','',true,$criteria,$order) : array();
    //$cashBank->setTimeLog('end search data',true);

    $tempreport = ''; 
    
    
 // ============================= GENERATE DATA ============================= 
		 
    //$cashBank->setTimeLog('sum',true);
    
    $openingBalance = $cashBank->sumCashMovement(array_column($rs,'coakey'),$_POST['trStartDate']);  
    $arrColumn = array('refcode','warehousename','policenumber','suppliername');
    $arrTableType = array_column($rs, 'reftabletype');
     
    //$cashBank->setTimeLog('end sum',true);
    
    $objSet = array();
    
    // untuk setiap table type, search dulu datanya biar gk lama 
    
    $arrDataForTable = array();
    foreach($rs as $key=>$row){ 
        $tableType = $row['reftabletype'];
        
        // pisah data per jenis table
        if (!isset($arrDataForTable[$tableType])) $arrDataForTable[$tableType] = array(); 
        array_push($arrDataForTable[$tableType] ,$row['refkey'] );
        
        // create obj
        if (!isset($objSet[$tableType])) 
            $objSet[$tableType] =  $class->getTableNameAndObjById($tableType)['obj'];
        
    }
     
    foreach($objSet as $tableType=>$rowObj) {   
        $arrDataForTable[$tableType] = $rowObj->getRelatedDataForCashBankReport(  $arrDataForTable[$tableType]  );
    }
     
    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
 
        $balance =  $openingBalance + ($rs[$i]['debit'] + $rs[$i]['credit']);
        $rs[$i]['openingBalance'] = $openingBalance;
        $rs[$i]['balance'] = $balance;
        $openingBalance = $balance;
          
        $tabletype = $rs[$i]['reftabletype'];
        $refkey = $rs[$i]['refkey'];
        
        foreach($arrColumn as $col)  
            $rs[$i][$col] = (isset($arrDataForTable[$tabletype][$refkey][$col])) ? $arrDataForTable[$tabletype][$refkey][$col]  : ''; 
        
        $return = $obj->formatReportRows(array('data'=>$rs[$i]),$arrTemplate); 

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

$arrTransaction = $class->convertForCombobox($obj->getTransactionType(),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and iscashbank = 1','order by coaname asc'),'pkey','coaname');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelCOA'] =  $class->inputSelect('selCOA[]', $arrCOA, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCashBankTransactionType'] =  $class->inputSelect('selCashBankTransactionType[]', $arrTransaction, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCashBankCode'] =  $class->inputText('cashBankCode');
$arrTwigVar['inputRefCode'] =  $class->inputText('refCode');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportCashBankVoucherMaintenance.html', $arrTwigVar);   

?>