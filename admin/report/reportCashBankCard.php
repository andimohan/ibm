<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('CashBank.class.php','GeneralJournal.class.php'));
$cashBankCard = createObjAndAddToCol( new CashBank());  
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 

include '_global.php';

$obj = $cashBankCard;

$securityObject = 'reportCashBankCard'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));

$_POST['selStatus[]'] = array(2, 3);
$arrFilterInformation = array();
// $detailCriteria = ''; 

$customCodeInactiveCriteria = '';

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
   $_POST['trStartDate'] = date('d / m / Y');
   $_POST['trEndDate']   = date('d / m / Y');
}


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"60px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"160px", 'sortable'=>false,'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate','sortable'=>false, 'width'=>"120px",'format'=>'date');
$arrDataStructure['reference'] = array('title'=>ucwords($obj->lang['reference']),'dbfield' => 'refcode', 'width'=>"150px", "sortable" => false);
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename','sortable'=>false, 'width'=>"100px");
$arrDataStructure['recipient'] = array('title'=>ucwords($obj->lang['senderOrRecipient']),'dbfield' => 'recipientname','sortable'=>false, 'width'=>"150px");
$arrDataStructure['startingbalance'] = array('title'=>ucwords($obj->lang['startingBalance']),'dbfield' => 'startingbalance','sortable'=>false, 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', "sortable" => false);
$arrDataStructure['debit'] = array('title'=>ucwords($obj->lang['debit']),'dbfield' => 'debit','sortable'=>false, 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', 'calculateTotal' => true);
$arrDataStructure['credit'] = array('title'=>ucwords($obj->lang['credit']),'dbfield' => 'credit','sortable'=>false, 'width'=>"120px" ,'format'=>'number', 'textColor' => 'C41E3A','calculateTotal' => true);
$arrDataStructure['balance'] = array('title'=>ucwords($obj->lang['endingBalance']),'dbfield' => 'balance','sortable'=>false, 'width'=>"120px" ,'format'=>'number', 'textColor' => '568203', "sortable" => false);
$arrDataStructure['detailTransaction'] = array('title'=>ucwords($obj->lang['transaction']),'dbfield' => 'detailtransaction' , 'width' => '150px','sortable'=>false );
$arrDataStructure['desc'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc' , 'width' => '200px','sortable'=>false );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = str_replace('/',' ',$obj->lang['cashBankCardReport']) ;
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrTransaction = $class->convertForCombobox($obj->getTransactionType(),'pkey','name');
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and iscashbank = 1','order by coaname asc'),'pkey','coaname');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');


if (isset($_POST) && !empty($_POST['hidAction'])){
	

    $criteria = '';
    $arrFilterInformation = array();
    $criteriaArr = array();
    
    array_push($criteriaArr, array(
        'postVariable' => array('trStartDate', 'trEndDate'),
        'fieldName' => $obj->tableName . '.trdate',
        'label' =>  $obj->lang['date'],
        'type' => 'daterange'
    ));

    array_push($criteriaArr, array(
        'postVariable' => 'selCOA',
        'fieldName' => $obj->tableName . '.coakey',
        'label' => $obj->lang['chartOfAccount'],
        'useArrayKey' => array('obj' => $chartOfAccount)
    ));
     
    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);
    
    $criteria .= ' and '.$obj->tableName.'.statuskey in (2,3)';

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	  
	$rs =  $obj->searchData('','',true,$criteria,$order);

	// cari detail transaksi
	$rsTrans = array();
	
	if(!empty($rs)){ 
		$rsTrans = $obj->getTransactionDetail(array_column($rs,'pkey'));
		$rsTrans = $obj->reindexDetailCollections($rsTrans,'refkey'); 
	}
	
    $tempreport = ''; 
    
    
 // ============================= GENERATE DATA ============================= 
    $totalRs = count($rs);
    
    $generalJournal = new GeneralJournal(); 
    $coakey = (isset($_POST)) ? $_POST['selCOA'] : array_keys($arrCOA)[0];
    $arrStartingBalance = $generalJournal->sumAccount(array($_POST['selCOA']),'',$_POST['trStartDate']); 
    $balance = $arrStartingBalance[$_POST['selCOA']][0]['balance'];
 
	 
    for( $i=0;$i<$totalRs;$i++) {
          
        $rs[$i]['startingbalance'] = $balance; 
		 
		$rs[$i]['detailtransaction'] = implode('<br>',array_column($rsTrans[$rs[$i]['pkey']],'refcode'));
			
        $amount = $rs[$i]['amount'] * $rs[$i]['credittype'];
        $rs[$i]['debit'] = 0;
        $rs[$i]['credit'] = 0;
        
        if( $amount > 0) 
            $rs[$i]['debit'] = $rs[$i]['amount'];
        else
            $rs[$i]['credit'] = $rs[$i]['amount'];
        
        $balance += $amount;
        $rs[$i]['balance'] = $balance; 

         $return = $obj->formatReportRows(array('data'=>$rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];  
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    } 

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}


$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelCOA'] =  $class->inputSelect('selCOA', $arrCOA);
$arrTwigVar['inputSelCashBankTransactionType'] =  $class->inputSelect('selCashBankTransactionType[]', $arrTransaction, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCashBankCode'] =  $class->inputText('cashBankCode');
echo $twig->render('reportCashBankCard.html', $arrTwigVar);   

?>