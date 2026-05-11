<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('EMKLPurchaseOrder.class.php','EMKLJobOrder.class.php', 'Warehouse.class.php', 'TermOfPayment.class.php'));
$emklPurchaseOrder = createObjAndAddToCol( new EMKLPurchaseOrder());
$warehouse = createObjAndAddToCol( new Warehouse());
$termOfPayment = createObjAndAddToCol( new TermOfPayment());
$container = createObjAndAddToCol(new Container());
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
    
include '_global.php';

$securityObject = 'reportTransactionCashFlow'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$arrFilterInformation = array();
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['transactionCashFlowReport'];

$rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc');
$rsContainer = $container->getContainerType();

$rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey'),' and '.$termOfPayment->tableName.'.duedays = 0');
$arrTOPKey = array_column($rsTOP,'pkey');

if (isset($_POST) && !empty($_POST['hidAction'])){
  
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		 array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    // purchaes cash
    $rsCashPurchase = $emklPurchaseOrder->sumTotalPurchase(array(
                                                                'startDate' => $_POST['trStartDate'],
                                                                'endDate' => $_POST['trEndDate'],
                                                                'termOfPaymentKey' => $arrTOPKey
                                                            )); 
    $rsCashPurchase = mapAmount($rsCashPurchase);

    $rsAPPayment = $emklPurchaseOrder->sumTotalAPPayment(array(
                                                                'startDate' => $_POST['trStartDate'],
                                                                'endDate' => $_POST['trEndDate']
                                                            ));
    $rsAPPayment = mapAmount($rsAPPayment);

    $rsARPayment = $emklJobOrder->sumTotalARPayment(array(
                                                                    'startDate' => $_POST['trStartDate'],
                                                                    'endDate' => $_POST['trEndDate'],
                                                                    'warehousekey' => $arrWarehouseKey, 
                                                                    'containerTypeKey' => $arrContainerTypeKey,
                                                                ));
    $rsARPayment = mapAmount($rsARPayment);
        
    
    $criteria = ''; 
    $tempreport .= '<table style="table-layout:fixed; width:100%">'; 
    $tempreport .= '<tr class="row-plain"  style="border-bottom:1px solid #333">';   
    $tempreport .= '<td colspan="2"></td>';
    foreach($rsWarehouse as $warehouseRow) 
         $tempreport .= '<td style="text-align:right; font-weight:bold">'.$warehouseRow['name'].'</td>'; 
    
    $tempreport .= '<td style="text-align:right; font-weight:bold">'.$class->lang['total'].'</td>'; 
    $tempreport .= '</tr>';
    
    //init
    $totalBalance = array();
    foreach($rsContainer as $containerRow)
        foreach($rsWarehouse as $warehouseRow)
            $totalBalance[$containerRow['pkey']][$warehouseRow['pkey']] = 0;
         
    foreach($rsContainer as $containerRow){
            
            $tempreport .= '<tr class="row-plain">';  
            $tempreport .= '<td rowspan="4" style=" font-weight:bold">'.$containerRow['name'].'</td>'; 
            $tempreport .= '<td style="width: 8em; font-weight: bold">'.$class->lang['purchase'].' ('.$class->lang['cash'].')</td>';  
        
            $totalRows = 0;
            foreach($rsWarehouse as $warehouseRow){ 
                $amount = (isset($rsCashPurchase[$containerRow['pkey']][$warehouseRow['pkey']])) ? $rsCashPurchase[$containerRow['pkey']][$warehouseRow['pkey']] : 0;
                $totalRows += $amount;
                $totalBalance[$containerRow['pkey']][$warehouseRow['pkey']] -= $amount;
                $tempreport .= '<td style="text-align:right">'.$class->formatNumber($amount * -1).'</td>'; 
            }
        
            $tempreport .= '<td style="text-align:right">'.$class->formatNumber($totalRows * -1).'</td>';   
            $tempreport .= '</tr>';

            $tempreport .= '<tr class="row-plain">';   
            $tempreport .= '<td style=" font-weight: bold">'.$class->lang['apPayment'].'</td>'; 

            $totalRows = 0;
            foreach($rsWarehouse as $warehouseRow){ 
                $amount = (isset($rsAPPayment[$containerRow['pkey']][$warehouseRow['pkey']])) ? $rsAPPayment[$containerRow['pkey']][$warehouseRow['pkey']] : 0;
                $totalRows += $amount;
                $totalBalance[$containerRow['pkey']][$warehouseRow['pkey']] -= $amount;
                $tempreport .= '<td style="text-align:right">'.$class->formatNumber($amount * -1).'</td>'; 
            }
        
            $tempreport .= '<td style="text-align:right">'.$class->formatNumber($totalRows * -1).'</td>';   

            $tempreport .= '</tr>';
 

            $tempreport .= '<tr class="row-plain">';   
            $tempreport .= '<td style=" font-weight: bold">'.$class->lang['arPayment'].'</td>'; 
            
            $totalRows = 0;
            foreach($rsWarehouse as $warehouseRow){ 
                $amount = (isset($rsARPayment[$containerRow['pkey']][$warehouseRow['pkey']])) ? $rsARPayment[$containerRow['pkey']][$warehouseRow['pkey']] : 0;
                $totalRows += $amount;
                $totalBalance[$containerRow['pkey']][$warehouseRow['pkey']] += $amount;
                $tempreport .= '<td style="text-align:right">'.$class->formatNumber($amount).'</td>'; 
            }
        
            $tempreport .= '<td style="text-align:right">'.$class->formatNumber($totalRows).'</td>'; 
            $tempreport .= '</tr>';

            $tempreport .= '<tr class="row-plain"  style="border-bottom:1px solid #333">';   
            $tempreport .= '<td style="font-weight:bold">'.$class->lang['balance'].'</td>'; 
            foreach($rsWarehouse as $warehouseRow){  
                 $total = $totalBalance[$containerRow['pkey']][$warehouseRow['pkey']];
                 $rowClass = ($total >= 0) ? 'text-green-avocado' : 'text-red-cardinal';
                 $tempreport .= '<td class="'.$rowClass.'" style="text-align:right">'.$class->formatNumber($total).'</td>'; 
            }
        
            $total = 0; 
            foreach($rsWarehouse as $warehouseRow)
                $total += $totalBalance[$containerRow['pkey']][$warehouseRow['pkey']];
        
            $rowClass = ($total >= 0) ? 'text-green-avocado' : 'text-red-cardinal';
            $tempreport .= '<td class="'.$rowClass.'" style="text-align:right">'.$class->formatNumber($total).'</td>';     
            $tempreport .= '</tr>';
    } 
    
    $tempreport .= '</table>';
     
    

	$reportResult = array();
	$reportResult['filterInformation'] = $arrFilterInformation;
 	$reportResult['content'] = $tempreport;

    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
        
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
}else{
    $_POST['trStartDate'] = date('01 / m / Y');
	$_POST['trEndDate'] = date('t / m / Y');  
}


//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
//$arrContainer = $class->convertForCombobox($container->getContainerType(),'pkey','name');   

//$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportEMKLTransactionCashFlow.html', $arrTwigVar); 


function mapAmount($rs){
    global $class;
    
    $returnArr = array();
    
    foreach($rs as $row){ 
        $containertype = $row['containertypekey'];
        $warehousekey = $row['warehousekey'];  
        
        if(!isset($returnArr[$containertype][$warehousekey])) $returnArr[$containertype][$warehousekey] = 0;
        $returnArr[$containertype][$warehousekey] += $row['amount'];
    }
    
    //$class->setLog($returnArr,true);
    return $returnArr;
}

?>