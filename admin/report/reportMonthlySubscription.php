<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
 
includeClass(array('Warehouse.class.php','Customer.class.php','MembershipSubscription.class.php','MembershipLevel.class.php'));
$membershipSubscription = createObjAndAddToCol( new MembershipSubscription());  
$warehouse = createObjAndAddToCol( new Warehouse());  
$customer = createObjAndAddToCol( new Customer());  
$membershipLevel = createObjAndAddToCol( new MembershipLevel());  

include '_global.php';

$obj = $membershipSubscription;
$securityObject = 'reportMembershipSubscription'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$arrFilterInformation = array();  
 
$_POST['selStatus[]'] = array(2,3);


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['membershipLevel'] = array('title'=>ucwords($obj->lang['membership']),'dbfield' => 'name', 'width'=>"200px" );
 
// ==================================== ADD PERIOD COLUMN
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
    $_POST['trStartDate'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['trEndDate'] = date('F Y');  
} 

$monthPeriod = $obj->getMonthPeriod($_POST['trStartDate'], $_POST['trEndDate']);

$arrKeyPeriod = array(); 
foreach ($monthPeriod as $dt) { 
    $keyIndex = $dt->format('nY');  
    $arrKeyPeriod[$keyIndex] = array('label' => $dt->format('M Y')); 
}


foreach($arrKeyPeriod as $keyPeriod => $period){ 
    $arrDataStructure['qty'.$keyPeriod] = array('title'=>ucwords($obj->lang['qty']),'group' => $period['label'], 'dbfield' => 'qty'.$keyPeriod, 'width'=>"60px" ,"format" => 'number', "sortable" => false, 'calculateTotal' => true);
    $arrDataStructure['sales'.$keyPeriod] = array('title'=>ucwords($obj->lang['sales']),'group' => $period['label'], 'dbfield' => 'grandtotal'.$keyPeriod, 'width'=>"60px" ,"format" => 'number', "sortable" => false, 'calculateTotal' => true);
}

$arrDataStructure['totalQty'] = array('title'=>ucwords($obj->lang['qty']),'group' => $obj->lang['qty'], 'dbfield' => 'totalqty', 'width'=>"60px" , "format" => 'number', "sortable" => false, "style" => 'font-weight:bold', 'calculateTotal' => true);
$arrDataStructure['totalSales'] = array('title'=>ucwords($obj->lang['sales']),'group' => $obj->lang['sales'], 'dbfield' => 'totalsales', 'width'=>"60px" , "format" => 'number', "sortable" => false, "style" => 'font-weight:bold', 'calculateTotal' => true);
      
// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['monthlySubscriptionnReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
   
if(isset($_POST) && !empty($_POST['trStartDate'])){
    array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}

$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
$arrTwigVar['exportExcel'] = false; 


if (isset($_POST) && !empty($_POST['hidAction'])){   
     
    
    $orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'name';  
    $orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 
 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	  
    // get summary
    $rs = $obj->getMonthlySalesSummary(date('d / m / Y',strtotime($_POST['trStartDate'])), date('d / m / Y',strtotime($_POST['trEndDate'])));
    $rsSalesSummary = array_column($rs,null,'periodindex');
   
	$tempreport = '';

	$rsMembershipLevel= $membershipLevel->searchDataRow(array( $membershipLevel->tableName.'.pkey', $membershipLevel->tableName.'.name'),
														' and '. $membershipLevel->tableName.'.statuskey = 1 and '. $membershipLevel->tableName.'.systemVariable <> 1',
														'order by '.$membershipLevel->tableName.'.pkey asc');
	
    foreach ($rsMembershipLevel as $levelRow) {  
		 
        $levelkey = $levelRow['pkey'];
        $totalSales = 0;
        $totalQty = 0;
       
		foreach($arrKeyPeriod as $keyPeriod => $period){ 

            $periodIndex= $levelkey.'-'.$keyPeriod; 
             
            $amount = (isset($rsSalesSummary[$periodIndex]['grandtotal'])) ? $rsSalesSummary[$periodIndex]['grandtotal']: 0;
            $qty  = (isset($rsSalesSummary[$periodIndex]['qty'])) ? $rsSalesSummary[$periodIndex]['qty']: 0;
			
            $totalSales += $amount;  
            $totalQty += $qty;  
                    
            //$obj->setLog($amount);
            $levelRow['qty'.$keyPeriod] = $qty;   
            $levelRow['grandtotal'.$keyPeriod] = $amount;   
            
            $filterBy['levelkey'] = $levelkey;
            $filterBy['periodIndex'] = $periodIndex;
              
        }
         
        
        $levelRow['totalsales'] = $totalSales;  
        $levelRow['totalqty'] = $totalQty;  
            
        $return = $obj->formatReportRows(array('data' => $levelRow),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
             
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportMonthlySubscription.html', $arrTwigVar);   
?>