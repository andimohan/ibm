<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
require_once '_report-config.php';  
  
$obj = $itemDepotMovement;
 
$arrFilterInformation = array();    
  
// ===== FOR EXPORT SECTION
$dataToExport = array(); 

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'name', 'width'=>"280px" );


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
     
  /*  $arrDataStructure['qty'.$keyPeriod] = array('title'=>ucwords($obj->lang['beginningQty']),'group' => $period['label'],'dbfield' => 'qtyinbaseunit', 'width'=>"80px" ,'format'=>'number', "sortable" => false);
    $arrDataStructure['unit'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'baseunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');
    $arrDataStructure['totalWeight'.$keyPeriod] = array('title'=>ucwords($obj->lang['totalWeight']),'group' => $period['label'],'dbfield' => 'totalweight', 'hidWidth'=>"100px" ,'format'=>'decimal', "sortable" => false);
    $arrDataStructure['weightUnit'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
*/ 
 
    $arrDataStructure['qtyIn'.$keyPeriod] = array('title'=>ucwords($obj->lang['in']),'group' => $period['label'], 'dbfield' => 'qtyinbaseunitIn'.$keyPeriod, 'width'=>"90px" ,"align" => 'right', "sortable" => false, "textColor" => '568203');
    $arrDataStructure['unitIn'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'baseunitname'.$keyPeriod, 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');
  /*  $arrDataStructure['totalWeightIn'.$keyPeriod] = array('title'=>ucwords($obj->lang['totalWeight']),'group' => $period['label'],'dbfield' => 'totalweightIn'.$keyPeriod, 'width'=>"100px" ,'format'=>'decimal', "sortable" => false, "textColor" => '568203');
    $arrDataStructure['weightUnitIn'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
*/
    $arrDataStructure['qtyOut'.$keyPeriod] = array('title'=>ucwords($obj->lang['out']),'group' => $period['label'],'dbfield' => 'qtyinbaseunitOut'.$keyPeriod, 'width'=>"90px","align" => 'right', "sortable" => false, "textColor" => 'C41E3A');
    $arrDataStructure['unitOut'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'baseunitname'.$keyPeriod, 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');
  /*  $arrDataStructure['totalWeightOut'.$keyPeriod] = array('title'=>ucwords($obj->lang['totalWeight']),'group' => $period['label'],'dbfield' => 'totalweightOut'.$keyPeriod, 'width'=>"100px" ,'format'=>'decimal', "sortable" => false, "textColor" => 'C41E3A');
    $arrDataStructure['weightUnitOut'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
  */   
    $arrDataStructure['qtyBalance'.$keyPeriod] = array('title'=>ucwords($obj->lang['balanceQty']),'group' => $period['label'],'dbfield' => 'qtyinbaseunitBalance'.$keyPeriod, 'width'=>"90px" ,"align" => 'right', "sortable" => false);
    $arrDataStructure['unitBalance'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'baseunitname'.$keyPeriod, 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;');
  /*  $arrDataStructure['totalWeightBalance'.$keyPeriod] = array('title'=>ucwords($obj->lang['totalWeight']),'group' => $period['label'],'dbfield' => 'totalweightBalance'.$keyPeriod, 'width'=>"100px" ,'format'=>'decimal', "sortable" => false);
    $arrDataStructure['weightUnitBalance'.$keyPeriod] = array('title'=>'','group' => $period['label'],'dbfield' => 'weightunitname', 'width'=>"40px", "sortable" => false,'textColor' => '999999', 'style' => 'padding-left:0px;'); 
  */  
}
// ==================================== ADD PERIOD COLUMN

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['monthlySummaryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


$itemCriteria = ' and ' . $item->tableName.'.statuskey = 1';
$movementCriteria = ' and ' . $obj->tableName.'.statuskey = 1 and customerkey = '. $class->oDbCon->paramString(USERKEY);
 
if(isset($_POST) && !empty($_POST['trStartDate'])){
    array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}

if(isset($_POST) && !empty($_POST['selDepot'])) { 

    $key = implode(",", $class->oDbCon->paramString($_POST['selDepot']));   

    $movementCriteria .= ' AND depotkey in('.$key.')';  

    $rsCriteria = $depot->searchData('','',true, ' and '.$depot->tableName.'.pkey in ('.$key.')');

    $arrTempStatus = array();
    for ($k=0;$k<count($rsCriteria);$k++)
        array_push($arrTempStatus,$rsCriteria[$k]['name']);

    $depotName = implode(", ",$arrTempStatus); 
    array_push($arrFilterInformation,array("label" => $obj->lang['depot'], 'filter' => $depotName ));

    $_POST['selDepot[]'] = $_POST['selDepot'];
}


if(isset($_POST) && !empty($_POST['itemName'])) { 
    $itemCriteria .= ' AND '.$itemDepot->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
    array_push($arrFilterInformation,array("label" => $obj->lang['itemName'], 'filter' => $_POST['itemName']));
}
  
//tambahin criteria item yg hanya pernah terjadi transaksi dengan cust
$customerItem = $item->getCustomerItem(USERKEY);
if(empty($customerItem))  $customerItem = '0';
$itemCriteria .= ' and '.$itemDepot->tableName.'.pkey in ('.$obj->oDbCon->paramString($customerItem,',').') ' ;

$orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'code';  
$orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 

$order = 'order by '.$orderBy.' ' .$orderType;
  
$rsItem = $itemDepot->searchData('','',true,$itemCriteria,$order); 
 
// starting balance 
$date = date('d / m / Y',strtotime( $_POST['trStartDate'] .' -1 day'));  
$rsStartingBalance = $obj->getItemMovementMonthlySummary('',$date,$movementCriteria,'itemkey');
$rsStartingBalanceByPeriod = array_column($rsStartingBalance,null,'itemkey');

// get summary
$rs = $obj->getItemMovementMonthlySummary( date('d / m / Y',strtotime($_POST['trStartDate'])), date('d / m / Y',strtotime($_POST['trEndDate'])),$movementCriteria);
$rsBalanceByPeriod = array_column($rs,null,'periodindex');
           
$tempreport = '';  
$balance = array();

foreach ($rsItem as $item) { 
   
    $itemkey= $item['pkey'];
    if (!isset($balance[$itemkey]))
        $balance[$itemkey] =  isset($rsStartingBalanceByPeriod[$itemkey]['total']) ? $rsStartingBalanceByPeriod[$itemkey]['total'] : 0;
 
    
    foreach($arrKeyPeriod as $keyPeriod => $period){ 
            
        $periodIndex= $itemkey.'-'.$keyPeriod; 
 
        $item['baseunitname'.$keyPeriod] = $item['baseunitname'] . '<br>'.$item['weightunitname']; 
       // $item['weightunitname'.$keyPeriod] = $item['weightunitname'];
        
        $totalIn = isset($rsBalanceByPeriod[$periodIndex]['totalin']) ? $rsBalanceByPeriod[$periodIndex]['totalin'] : 0;
        $item['qtyinbaseunitIn'.$keyPeriod] = $obj->formatNumber($totalIn) .'<br>'. $obj->formatNumber($totalIn * $item['gramasi']);
       // $item['totalweightIn'.$keyPeriod] = $totalIn * $item['gramasi'];
        
        $totalOut = isset($rsBalanceByPeriod[$periodIndex]['totalout']) ? $rsBalanceByPeriod[$periodIndex]['totalout'] : 0;
        $item['qtyinbaseunitOut'.$keyPeriod] = $obj->formatNumber($totalOut) .'<br>'. $obj->formatNumber($totalOut * $item['gramasi']);
       // $item['totalweightOut'.$keyPeriod] = $totalOut * $item['gramasi'];
        
        $monthlyBalance = isset($rsBalanceByPeriod[$periodIndex]['total']) ? $rsBalanceByPeriod[$periodIndex]['total'] : 0;
        $balance[$itemkey] += $monthlyBalance;      
              
        //$obj->setLog($itemkey.' => ' . ' => ' . $keyPeriod . ' = ' .$balance[$itemkey]);
        $item['qtyinbaseunitBalance'.$keyPeriod] =  $obj->formatNumber($balance[$itemkey]) .'<br>'. $obj->formatNumber($balance[$itemkey] * $item['gramasi']);
       // $obj->setLog($item['qtyinbaseunitBalance'.$keyPeriod]);
        //$item['totalweightBalance'.$keyPeriod] = $balance[$itemkey] * $item['gramasi']; 
    }
     
     
    $return = $obj->formatReportRows(array('data' => $item),$arrTemplate);  

    // ===== FOR EXPORT SECTION 
    //array_push($dataToExport, $return['data']);  
    // ===== END FOR EXPORT SECTION

    $tempreport .= $return['html'];  
}

// EXPORT TO EXCEL
if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){   
    $excel = new Excel();

    $arrTemplate[0]['dataToExport'] = $dataToExport;
    $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
    $excel->exportToSave($arrTemplate,null,array('name' => $rsCustomer[0]['name'] ));       
    die;
}


$arrHeaderTemplate = $arrTemplate[0];     

$reportContent = $tempreport;
if (empty($reportContent)) 
    $reportContent = '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

// total rows
$reportContent .= $obj->formatReportFooterRows($arrHeaderTemplate['total'],$arrHeaderTemplate['dataStructure']); 
$arrDepot = $class->convertForCombobox($depot->searchData($depot->tableName.'.statuskey',1,true,' and isprivate = 1','order by name asc'),'pkey','name');
  
$arrTwigVar['title'] = $arrHeaderTemplate['reportTitle']; 
$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelDepot'] = $class->inputSelect('selDepot[]', $arrDepot, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));   
$arrTwigVar['inputItemName'] = $class->inputText('itemName'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
  
$arrTwigVar['arrFilterInformation'] = $arrFilterInformation;  
$arrTwigVar['reportContent'] = $reportContent;

$arrTwigVar['hidOrderBy'] = $class->inputHidden('hidOrderBy');
$arrTwigVar['hidOrderType'] = $class->inputHidden('hidOrderType'); 
$arrTwigVar['order'] = array("orderBy" => $orderBy, "orderType" => (isset($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1);
 
$arrTwigVar['btnExportExcel'] = '';

echo $twig->render('report-monthly-summary.html', $arrTwigVar);

?>