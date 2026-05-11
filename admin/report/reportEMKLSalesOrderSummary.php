<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('EMKLJobOrder.class.php','EMKLPurchaseOrder.class.php','Warehouse.class.php')); 
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$emklPurchaseOrder = createObjAndAddToCol(new EMKLPurchaseOrder());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';
 
$obj= $emklJobOrder;
$securityObject = 'ReportSalesOrderSummaryFF';
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrFilterInformation = array();  
 
$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array(); 

$arrDataStructure = array(); 
 
// ==================================== ADD PERIOD COLUMN
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
    $_POST['trStartDate'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['trEndDate'] = date('F Y');  
} 
$monthPeriod = $obj->getMonthPeriod($_POST['trStartDate'], $_POST['trEndDate']);

$arrKeyPeriod = array(); 
foreach ($monthPeriod as $dt) { 
    $keyIndex = $dt->format('n-Y');  
    $arrKeyPeriod[$keyIndex] = array('label' => $dt->format('M Y')); 
}


$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']), 'width'=>"200px", 'dbfield' => 'name', 'format' => 'string', "sortable" => false);  

foreach($arrKeyPeriod as $keyPeriod => $period){ 
    $arrDataStructure['selling'.$keyPeriod] = array('title'=>ucwords($obj->lang['selling']),'group' => $period['label'], 'dbfield' => 'selling'.$keyPeriod, 'width'=>"90px" ,"format" => 'decimal','calculateTotal' => true,  "sortable" => false);
    $arrDataStructure['buying'.$keyPeriod] = array('title'=>ucwords($obj->lang['buying']),'group' => $period['label'],'dbfield' => 'buying'.$keyPeriod, 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false);
    $arrDataStructure['commission'.$keyPeriod] = array('title'=>ucwords($obj->lang['purchaseRefund']),'group' => $period['label'],'dbfield' => 'commission'.$keyPeriod, 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false);
    $arrDataStructure['totaldebitnote'.$keyPeriod] = array('title'=>ucwords($obj->lang['debitNote']),'group' => $period['label'],'dbfield' => 'totaldebitnote'.$keyPeriod, 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false);
    $arrDataStructure['totalemployeecommission'.$keyPeriod] = array('title'=>ucwords($obj->lang['adminFee']),'group' => $period['label'],'dbfield' => 'totalemployeecommission'.$keyPeriod, 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false);
    $arrDataStructure['grossprofit'.$keyPeriod] = array('title'=>ucwords($obj->lang['grossProfit']),'group' => $period['label'],'dbfield' => 'grossprofit'.$keyPeriod, 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false);
}

$arrDataStructure['selling'] = array('title'=>ucwords($obj->lang['selling']),'group' => 'total', 'dbfield' => 'selling', 'width'=>"90px" ,"format" => 'decimal','calculateTotal' => true,  "sortable" => false, "style" => 'font-weight:bold');
$arrDataStructure['buying'] = array('title'=>ucwords($obj->lang['buying']),'group' => 'total','dbfield' => 'buying', 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false, "style" => 'font-weight:bold');
$arrDataStructure['commission'] = array('title'=>ucwords($obj->lang['purchaseRefund']),'group' => 'total','dbfield' => 'commission', 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false, "style" => 'font-weight:bold');
$arrDataStructure['totaldebitnote'] = array('title'=>ucwords($obj->lang['debitNote']),'group' => 'total','dbfield' => 'totaldebitnote', 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false, "style" => 'font-weight:bold');
$arrDataStructure['totalemployeecommission'] = array('title'=>ucwords($obj->lang['adminFee']),'group' => 'total','dbfield' => 'totalemployeecommission', 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false, "style" => 'font-weight:bold');
$arrDataStructure['grossprofit'] = array('title'=>ucwords($obj->lang['grossProfit']),'group' => 'total','dbfield' => 'grossprofit', 'width'=>"90px","format" => 'decimal','calculateTotal' => true, "sortable" => false, "style" => 'font-weight:bold');

/*
$arrDataStructure['totalTrip'] = array('title'=>ucwords($obj->lang['trip']),'group' => $obj->lang['total'], 'dbfield' => 'totaltrip', 'width'=>"20px" ,"align" => 'center', "sortable" => false,"format" => 'number', "style" => 'font-weight:bold');
$arrDataStructure['totalRevenue'] = array('title'=>ucwords($obj->lang['revenue']),'group' => $obj->lang['total'], 'dbfield' => 'totalrevenue', 'width'=>"60px" ,"format" => 'number', "sortable" => false, "textColor" => '568203', "style" => 'font-weight:bold');
*/

// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderSummaryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
    
$criteria = '';
    
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));    
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
//$arrTwigVar['exportExcel'] = false;
 

if (isset($_POST) && !empty($_POST['hidAction'])){    
       
	if(isset($_POST) && !empty($_POST['trStartDate'])){
      switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.etdpol'; break;
            case '3' : $fieldName = $obj->tableName.'.etapod'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
         
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate(date('01 / m / Y',strtotime($_POST['trStartDate'])),' / ').' AND LAST_DAY('.$class->oDbCon->paramDate(date('01 / m / Y',strtotime($_POST['trEndDate'])),' / ').') '; 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	
	}
 
	$tempreport = '';
	
	$rs = $obj->getSellingSummary($criteria,
								  $obj->tableName.'.warehousekey, year('.$fieldName.'),month('.$fieldName.')', 
								 'concat(warehousekey,\'-\',DATE_FORMAT('.$fieldName.', \'%c-%Y\'))'); 
	$rs = array_column($rs,null,'periodindex');
	 
	// nanti coba diudpate bisa group by gudang / customer maybe
	$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey', $warehouse->tableName.'.name'),
											 ' and '.$warehouse->tableName.'.statuskey = 1'
											);
	
	
    foreach($rsWarehouse as $row){    
		
        $arrHeaderStyle = array();   
		
		$totalSelling = 0;
		$totalBuying = 0;
		$totalCommission = 0;
		$totalGrossProfit = 0;
		
        foreach($arrKeyPeriod as $keyPeriod => $period){  
				$detailIndex = $row['pkey'] .'-'.$keyPeriod; 
			
				$rsDetail = $rs[$detailIndex];
			
				$totalSelling += $rsDetail['totalselling'];
				$totalBuying += $rsDetail['totalbuying'];
				$totalCommission += $rsDetail['totalcommission'];
				$totalEmployeeCommission += $rsDetail['totalemployeecommission'];
				$totalDebitNote += $rsDetail['totaldebitnote'];
				$grosProfit =  ($rsDetail['totalselling'] + $rsDetail['totaldebitnote']) - ($rsDetail['totalbuying'] +  $rsDetail['totalemployeecommission'] + $rsDetail['totalcommission']);
				$totalGrossProfit += $grosProfit;
		 
                $row['selling'.$keyPeriod] = $rsDetail['totalselling'];
                $row['buying'.$keyPeriod]  = $rsDetail['totalbuying'];
                $row['commission'.$keyPeriod]  = $rsDetail['totalcommission'];
                $row['totalemployeecommission'.$keyPeriod]  = $rsDetail['totalemployeecommission'];
                $row['totaldebitnote'.$keyPeriod]  = $rsDetail['totaldebitnote'];
                $row['grossprofit'.$keyPeriod] = $grosProfit; 
			
				$textColor =  ($grosProfit < 0) ? 'C41E3A' : '568203';
				$arrHeaderStyle['grossprofit'.$keyPeriod]['textColor'] = $textColor; 
        } 
        

		$row['selling'] =  $totalSelling;
		$row['buying']  = $totalBuying;
		$row['commission']  = $totalCommission;
		$row['totalemployeecommission']  = $totalEmployeeCommission;
		$row['totaldebitnote']  = $totalDebitNote;
		$row['grossprofit'] = $totalGrossProfit; 
 
		$textColor =  ($totalGrossProfit < 0) ? 'C41E3A' : '568203';
		$arrHeaderStyle['grossprofit']['textColor'] = $textColor; 
		//$arrHeaderStyle['grossprofit']['fontWeight'] = 'bold'; 


        $return = $obj->formatReportRows(array('data' => $row, 'style' => $arrHeaderStyle),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar); 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportEMKLSalesOrderSummary.html', $arrTwigVar);   
?>